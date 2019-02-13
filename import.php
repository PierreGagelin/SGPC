<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("donnees.php");

if (is_priviledged() == false)
{
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Importer XSLX</title>
    </head>
    <body>

<?php

require_once("member.php");
require_once("vue.php");

require_once('Classes/PHPExcel.php');

afficher_navigation();
afficher_import_excel();

// renvoie les colonnes trouvées
// doit au moins contenir :
//   - nom
//   - prenom
function recuperer_colonnes($feuille)
{
    $excel_cols = array();
    global $colonnes;

    $HCAL = $feuille->getHighestColumn();
    $HCAL++;
    for ($CAL = 'A'; $CAL != $HCAL; $CAL++)
    {
        $col = $feuille->getCell($CAL . '1')->getValue();
        $col = "$col";
        if (in_array($col, $colonnes) == true)
        {
            $excel_cols[$col] = $CAL;
        }
    }
    if (!isset($excel_cols['nom']) || !isset($excel_cols['prenom']))
    {
        $erreur = "";
        $erreur .= "Erreur : lors de la récupération initiale des colonnes : <br />";
        $erreur .= "il faut au moins les colonnes nom et prenom dans le fichier que vous avez téléchargé<br />";
        die($erreur);
    }

    return $excel_cols;
}

// affiche les colonnes manquantes
function afficher_colonnes_manquantes($excel_cols)
{
    global $colonnes;
    $incomplet = false;
    foreach($colonnes as $colonne)
    {
        $clefs = array_keys($excel_cols);
        if (!in_array($colonne, $clefs))
        {
            $incomplet = true;
            echo "la colonne $colonne n'a pas été trouvée, pensez à l'ajouter <br />";
        }
    }
    if (!$incomplet)
    {
        echo "Félicitations, votre fichier comporte toutes les colonnes <br />";
    }
}

// mets à jour les entrées ayant un numéro d'adhérent existant
// crée un adhérent pour celles qui n'ont pas de numéro
function traitement_entrees($feuille, $excel_cols)
{
    $clefs = array_keys($excel_cols);
    $num_ad_existe = isset($excel_cols['numero_adherent']);
    $HRN = $feuille->getHighestRow();
    for($ligne = 2 ; $ligne < $HRN + 1 ; $ligne++)
    {
        $nom = $feuille->getCell($excel_cols['nom'] . $ligne)->getValue();
        $prenom = $feuille->getCell($excel_cols['prenom'] . $ligne)->getValue();

        echo "Traitement d'une ligne [numero=$ligne ; nom=$nom ; prenom=$prenom]<br />";

        if ($num_ad_existe)
        {
            $num_ad = $feuille->getCell($excel_cols['numero_adherent'] . $ligne)->getValue();
            verifier("numero_adherent", $num_ad);
        }
        else
        {
            $num_ad = null;
        }

        member_add($num_ad);

        foreach($clefs as $clef)
        {
            $valeur = $feuille->getCell($excel_cols[$clef] . $ligne)->getValue();
            // les deux lignes qui suivent sont un peu hasardeuses...
            // la feuille Excel est censée être en UTF-8 directement
            $valeur = iconv("UTF-8", "ISO-8859-1", $valeur);
            $valeur = utf8_encode($valeur);
            echo "...verification colonne : $clef, valeur : $valeur<br />";
            insere($num_ad, $clef, $valeur);
        }

        echo "----ligne traitée avec succès [numero=$ligne]<br /><br />";
    }
}

//
// Parse an Excel sheet
//
function parse_excel($feuille)
{
    $imported = array();

    $excel_cols = recuperer_colonnes($feuille);
    afficher_colonnes_manquantes($excel_cols);

    $clefs = array_keys($excel_cols);
    if (isset($excel_cols['numero_adherent']) == false)
    {
        die("Erreur : colonne numero_adherent requise");
    }

    $HRN = $feuille->getHighestRow();
    for ($ligne = 2 ; $ligne < $HRN + 1 ; $ligne++)
    {
        $num_ad = $feuille->getCell($excel_cols['numero_adherent'] . $ligne)->getValue();
        $nom = $feuille->getCell($excel_cols['nom'] . $ligne)->getValue();
        $prenom = $feuille->getCell($excel_cols['prenom'] . $ligne)->getValue();

        // Force type
        $num_ad = "$num_ad";
        $nom = "$nom";
        $prenom = "$prenom";

        echo "Parsing line [index=$ligne ; adherent=$num_ad ; nom=$nom ; prenom=$prenom]<br />";

        if (empty($num_ad) == true)
        {
            die("Erreur : numéro d'adhérent vide !");
        }

        foreach ($clefs as $clef)
        {
            $valeur = $feuille->getCell($excel_cols[$clef] . $ligne)->getValue();
            $valeur = "$valeur";
            // les deux lignes qui suivent sont un peu hasardeuses...
            // la feuille Excel est censée être en UTF-8 directement
            // $valeur = iconv("UTF-8", "ISO-8859-1", $valeur);
            //$valeur = utf8_encode($valeur);

            if (empty($valeur) == true)
            {
                continue;
            }

            $encoding = mb_detect_encoding($valeur, "auto", true);
            echo "- verify entry [column=$clef ; value=$valeur ; encoding=$encoding]<br />";
            verifier($clef, $valeur);

            member_update($num_ad, $clef, $valeur, false);
        }

        echo "---- ligne traitée avec succès [index=$ligne]<br /><br />";
    }

    member_store();

    echo "Import finished<br />";
}

// traitement du fichier importé
if (!empty($_FILES))
{
    if (!isset($_FILES['fichier_excel']) || ($_FILES['fichier_excel']['error'] > 0))
    {
        die("Erreur lors de l'upload du fichier excel<br />");
    }
    $extension = substr(strrchr($_FILES['fichier_excel']['name'],'.'),1);
    if ($extension != "xlsx")
    {
        die("Erreur : le fichier n'est pas au format .xlsx !<br />");
    }
    // on déplace le fichier vers le dossier national
    if (is_connected())
    {
        $destination = "National/import.xlsx";
        if (!move_uploaded_file($_FILES['fichier_excel']['tmp_name'], $destination))
        {
            die("Erreur : le fichier n'a pas pu être déplacé<br />");
        }
    }
    else
    {
        die("Erreur : votre session a expiré, reconnectez vous<br />");
    }

    $fileType = 'Excel2007';
    $objReader = PHPExcel_IOFactory::createReader($fileType);
    $objPHPExcel = $objReader->load($destination);
    $feuille = $objPHPExcel->setActiveSheetIndex(0);
    parse_excel($feuille);
    // $excel_cols = recuperer_colonnes($feuille);
    // afficher_colonnes_manquantes($excel_cols);
    // traitement_entrees($feuille, $excel_cols);
}


/*
require_once('Classes/PHPExcel.php');

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// ouvrir le fichier excel
$fileType = 'Excel2007';
$fileName = '00SGPC20160517.xlsx';
$objReader = PHPExcel_IOFactory::createReader($fileType);
$objPHPExcel = $objReader->load($fileName);

// Set document properties
$objPHPExcel->getProperties()->setCreator("SGPC-CFE-CGC")
							 ->setLastModifiedBy("SGPC-CFE-CGC")
							 ->setTitle("trésorerie")
							 ->setSubject("trésorerie")
							 ->setDescription("excel pour la trésorerie")
							 ->setKeywords("excel trésorerie php")
							 ->setCategory("trésorerie");

// lecture du fichier PHP
// Highest Column As Letter
$HCAL = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
// Highest Row Number
$HRN = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
$HCAL++;
for ($row = 1; $row < $HRN + 1; $row++) {
  echo "ligne $row : ";
  for ($CAL = 'A'; $CAL != $HCAL; $CAL++) {
    echo $objPHPExcel->setActiveSheetIndex(0)
                             ->getCell($CAL.$row)->getValue() . ' ';
  }
}
*/

?>

    </body>
</html>
