<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("donnees.php");
require_once('vue.php');
require_once('Classes/PHPExcel.php');

if (is_connected() == false)
{
    header('Location: index.php');
    exit();
}

afficher_header("Exporter Excel");
afficher_navigation();


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// feuille Excel de travail
$feuille = $objPHPExcel->setActiveSheetIndex(0);
$feuille->setTitle('Feuille 1');

// on entre les noms de colonne sur la première ligne
$xls_col = 'A';
$xls_lig = '1';
foreach($colonnes as $colonne)
{
    $feuille->setCellValue($xls_col . $xls_lig, $colonne);
    $xls_col++;
}
$xls_lig++;

// on rempli les données ligne par ligne
$id_list = member_get_list();
foreach($id_list as $id)
{
    $member = member_get($id);

    // Skip member that don't belong to the region
    if (($member['region'] != $_SESSION['region']) && (is_priviledged() == false))
    {
        continue;
    }

    $xls_col = 'A';
    foreach($colonnes as $colonne)
    {
        if (isset($member[$colonne]) == true)
        {
            $feuille->setCellValue($xls_col . $xls_lig, $member[$colonne]);
        }
        $xls_col++;
    }
    $xls_lig++;
}

// sauvegarder au format .xls (Excel 95)
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// sauvegarder au format .xlsx (Excel 2007)
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("{$_SESSION['region']}/export.xlsx");

$lien_telecharger = "";
$lien_telecharger .= "<form action='{$_SESSION['region']}/export.xlsx' method='get'>";
$lien_telecharger .= "    <input type='submit' value='Téléchargez le fichier'>";
$lien_telecharger .= "</form>";
echo $lien_telecharger;

afficher_footer();

?>
