<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("donnees.php");
require_once("member.php");
require_once("vue.php");
require_once('vendor/autoload.php');

if (is_privileged() == false)
{
    header('Location: index.php');
    exit();
}

afficher_header("Importer XSLX");
afficher_navigation();
afficher_import_excel();

//
// Get column in worksheet
//
function get_columns($worksheet)
{
    $excel_cols = array();
    global $colonnes;

    $HCAL = $worksheet->getHighestColumn();
    $HCAL++;
    for ($CAL = 'A'; $CAL != $HCAL; $CAL++)
    {
        $col = $worksheet->getCell($CAL . '1')->getValue();
        $col = "$col";
        if (in_array($col, $colonnes) == true)
        {
            $excel_cols[$col] = $CAL;
        }
    }
    if ((isset($excel_cols['nom']) == false) ||
        (isset($excel_cols['prenom']) == false) ||
        (isset($excel_cols['numero_adherent']) == false))
    {
        $erreur = "";
        $erreur .= "Erreur : lors de la récupération initiale des colonnes : <br />";
        $erreur .= "il faut au moins les colonnes nom, prenom et numero_adherent dans le fichier que vous avez téléchargé<br />";
        die($erreur);
    }

    return $excel_cols;
}

//
// Print missing columns
//
function print_missing_columns($excel_cols)
{
    global $colonnes;

    $incomplet = false;
    foreach($colonnes as $colonne)
    {
        $clefs = array_keys($excel_cols);
        if (in_array($colonne, $clefs) == false)
        {
            $incomplet = true;
            echo "La colonne $colonne n'a pas été trouvée, pensez à l'ajouter<br />";
        }
    }

    if ($incomplet == false)
    {
        echo "Félicitations, votre fichier comporte toutes les colonnes<br />";
    }
}

//
// Parse an Excel sheet
//
function parse_excel($worksheet)
{
    $excel_cols = get_columns($worksheet);
    print_missing_columns($excel_cols);

    $clefs = array_keys($excel_cols);

    $HRN = $worksheet->getHighestRow();
    for ($ligne = 2 ; $ligne < $HRN + 1 ; $ligne++)
    {
        $num_ad = $worksheet->getCell($excel_cols['numero_adherent'] . $ligne)->getValue();
        $nom = $worksheet->getCell($excel_cols['nom'] . $ligne)->getValue();
        $prenom = $worksheet->getCell($excel_cols['prenom'] . $ligne)->getValue();

        // Force type
        $num_ad = "$num_ad";
        $nom = "$nom";
        $prenom = "$prenom";

        echo "Traitement de la ligne index=$ligne adherent=$num_ad nom=$nom prenom=$prenom<br />";

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

            echo "- Vérification de la valeur column=$clef value=$valeur<br />";
            verifier($clef, $valeur);

            member_update($num_ad, $clef, $valeur);
        }
    }

    echo "Fin de l'import du fichier Excel<br />";
}

// Process uploaded file
if (empty($_FILES) == false)
{
    if ((isset($_FILES['fichier_excel']) == false) || ($_FILES['fichier_excel']['error'] > 0))
    {
        die("Erreur : échec de la télétransmission du fichier Excel<br />");
    }

    // Check extension
    $extension = substr(strrchr($_FILES['fichier_excel']['name'], '.'), 1);
    if ($extension != "xlsx")
    {
        die("Erreur : le fichier n'est pas au format .xlsx <br />");
    }

    // Move the file to national folder
    $destination = "National/import.xlsx";
    if (move_uploaded_file($_FILES['fichier_excel']['tmp_name'], $destination) == false)
    {
        die("Erreur : le fichier n'a pas pu être déplacé<br />");
    }

    // Load Excel file
    $fileType = 'Xlsx';
    $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
    $spreadsheet = $reader->load($destination);
    $worksheet = $spreadsheet->getSheet(0);

    // Parse Excel file
    parse_excel($worksheet);
}

afficher_footer();

?>
