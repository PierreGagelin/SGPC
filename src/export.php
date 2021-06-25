<?php

require_once "donnees.php";
require_once "vue.php";
require_once "vendor/autoload.php";

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

session_start();

if (is_connected() == false)
{
    header("Location: index.php");
    exit();
}

// Create a spreadsheet
$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getSheet(0);

// Set column names on the first line
$xls_col = "A";
$xls_lig = "1";
foreach($colonnes as $colonne)
{
    $worksheet->setCellValue($xls_col . $xls_lig, $colonne);

    $xls_col++;
}
$xls_lig++;

// Fill each line with a member
$member_list = member_get_list();
foreach($member_list as $member)
{
    // Skip member that don't belong to the region
    if (($member["region"] != $_SESSION["region"]) && (is_privileged() == false))
    {
        continue;
    }

    $xls_col = "A";
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
$writer->save("{$_SESSION["region"]}/export.xlsx");

display_header("Exporter Excel");
display_navigation();

// Display a download link
$page = "";
$page .= "<div class='section'>";
$page .= "<h1>Export du fichier</h1>";
$page .= "<p>Voici le fichier exporté :</p>";
$page .= "<form action='{$_SESSION['region']}/export.xlsx' method='get'>";
$page .= "<input type='submit' value='Téléchargez le fichier'>";
$page .= "</form>";
$page .= "</div>";
echo $page;

display_footer();

?>
