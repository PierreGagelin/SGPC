<?php

require_once "donnees.php";
require_once "member.php";
require_once "vue.php";
require_once "vendor/autoload.php";

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

session_start();

if (is_privileged() == false)
{
    header("Location: index.php");
    exit();
}

// Get columns in worksheet
function get_columns($worksheet)
{
    $excel_cols = array();
    global $colonnes;

    $HCAL = $worksheet->getHighestColumn();
    $HCAL++;
    for ($CAL = "A"; $CAL != $HCAL; $CAL++)
    {
        $col = $worksheet->getCell($CAL . "1")->getValue();
        $col = "$col";
        if (in_array($col, $colonnes) == true)
        {
            $excel_cols[$col] = $CAL;
        }
    }
    if ((isset($excel_cols["nom"]) == false) ||
        (isset($excel_cols["prenom"]) == false) ||
        (isset($excel_cols["numero_adherent"]) == false))
    {
        $erreur = "";
        $erreur .= "<p>Erreur : échec lors de la récupération initiale des colonnes. ";
        $erreur .= "Il faut au moins les colonnes 'nom', 'prenom' et 'numero_adherent' dans le fichier que vous avez téléchargé</p>";
        die($erreur);
    }

    return $excel_cols;
}

function print_missing_columns($excel_cols)
{
    global $colonnes;

    $missing_columns = array();
    foreach($colonnes as $colonne)
    {
        $clefs = array_keys($excel_cols);
        if (in_array($colonne, $clefs) == false)
        {
            array_push($missing_columns, $colonne);
        }
    }

    if (empty($missing_columns) == true)
    {
        echo "<p>Félicitations, votre fichier comporte toutes les colonnes</p>";
    }
    else
    {
        $entry = "";
        $entry .= "<p>Attention, votre fichier est incomplet. Il manque les colonnes ci-dessous</p>";
        $entry .= "<ul>";
        foreach($missing_columns as $missing_column)
        {
            $entry .= "<li>$missing_column</li>";
        }
        $entry .= "</ul>";
        echo $entry;
    }
}

// Parse an Excel sheet
function parse_excel($worksheet)
{
    $excel_cols = get_columns($worksheet);
    print_missing_columns($excel_cols);

    $clefs = array_keys($excel_cols);

    $HRN = $worksheet->getHighestRow();
    for ($ligne = 2 ; $ligne < $HRN + 1 ; $ligne++)
    {
        $num_ad = $worksheet->getCell($excel_cols["numero_adherent"] . $ligne)->getValue();
        $nom = $worksheet->getCell($excel_cols["nom"] . $ligne)->getValue();
        $prenom = $worksheet->getCell($excel_cols["prenom"] . $ligne)->getValue();

        // Force type
        $num_ad = "$num_ad";
        $nom = "$nom";
        $prenom = "$prenom";

        echo "<p>Traitement de la ligne index=$ligne numero_adherent=$num_ad nom=$nom prenom=$prenom</p>";

        if (empty($num_ad) == true)
        {
            die("Erreur : numéro d'adhérent vide !");
        }

        foreach ($clefs as $clef)
        {
            $valeur = $worksheet->getCell($excel_cols[$clef] . $ligne)->getValue();

            // Force type
            $valeur = "$valeur";

            if (empty($valeur) == true)
            {
                continue;
            }

            check_column_data($clef, $valeur);

            member_update($num_ad, $clef, $valeur);
        }
    }

    echo "<p>Fin de l'import du fichier Excel</p>";
}

display_header("Importer XSLX");
display_navigation();

// Display an entry to upload a file
$page = "";
$page .= "<div class='section'>";
$page .= "<h1>Import d'un fichier</h1>";
$page .= "<p>Veuillez choisir le fichier à importer :</p>";
$page .= "<form method='post' action='import.php' enctype='multipart/form-data'>";
$page .= "<table><th>Choix du fichier</th><th>Action</th>";
$page .= "<tr><td><input type='file' name='fichier_excel'></td><td><input type='submit' value='Importer le fichier'></td></tr>";
$page .= "</table></form></div>";
echo $page;

// Process uploaded file
if (empty($_FILES) == false)
{
    if ((isset($_FILES["fichier_excel"]) == false) || ($_FILES["fichier_excel"]["error"] > 0))
    {
        die("Erreur : échec de la télétransmission du fichier Excel");
    }

    // Move the file to national folder
    $destination = "National/import.xlsx";
    if (move_uploaded_file($_FILES["fichier_excel"]["tmp_name"], $destination) == false)
    {
        die("Erreur : le fichier n'a pas pu être déplacé");
    }

    // Load Excel file
    $fileType = "Xlsx";
    $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
    $spreadsheet = $reader->load($destination);
    $worksheet = $spreadsheet->getSheet(0);

    // Parse Excel file
    parse_excel($worksheet);
}

display_footer();

?>
