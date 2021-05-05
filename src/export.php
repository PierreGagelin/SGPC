<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("donnees.php");
require_once('vue.php');
require_once('vendor/autoload.php');

if (is_connected() == false)
{
    header('Location: index.php');
    exit();
}

afficher_header("Exporter Excel");
afficher_navigation();

// Create a spreadsheet
$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getSheet(0);

// Set column names on the first line
$xls_col = 'A';
$xls_lig = '1';
foreach($colonnes as $colonne)
{
    $worksheet->setCellValue($xls_col . $xls_lig, $colonne);

    // Adapt column size to content
    $dimensions = $worksheet->getColumnDimension($xls_col);
    $dimensions->setAutoSize(true);

    $xls_col++;
}
$xls_lig++;

// Fill each line with a member
$id_list = member_get_list();
foreach($id_list as $id)
{
    $member = member_get($id);

    // Skip member that don't belong to the region
    if (($member['region'] != $_SESSION['region']) && (is_privileged() == false))
    {
        continue;
    }

    $xls_col = 'A';
    foreach($colonnes as $colonne)
    {
        if (isset($member[$colonne]) == true)
        {
            $worksheet->setCellValue($xls_col . $xls_lig, $member[$colonne]);
        }
        $xls_col++;
    }
    $xls_lig++;
}

// Save spreadsheet
$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save("{$_SESSION['region']}/export.xlsx");

$lien_telecharger = "";
$lien_telecharger .= "<form action='{$_SESSION['region']}/export.xlsx' method='get'>";
$lien_telecharger .= "    <input type='submit' value='Téléchargez le fichier'>";
$lien_telecharger .= "</form>";
echo $lien_telecharger;

afficher_footer();

?>
