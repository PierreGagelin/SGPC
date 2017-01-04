<?php

// vérification de la session
session_start();
if(!empty($_SESSION)) {
  if(!isset($_SESSION['identifiant']) || !isset($_SESSION['region'])) {
    header('Location: index.php');
    exit();
  }
} else {
  header('Location: index.php');
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" />
  <title>Exporter Excel</title>
</head>
<body>

<?php

// affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// afficher la barre de navigation
require_once('vue.php');
afficher_navigation();

// librairie pour utiliser Excel
require_once('Classes/PHPExcel.php');

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// feuille Excel de travail
$feuille = $objPHPExcel->setActiveSheetIndex(0);
$feuille->setTitle('Feuille 1');

// on récupère la liste des adhérents
require_once("donnees.php");
$adherents = liste_adherents();

// on entre les noms de colonne sur la première ligne
$xls_col = 'A';
$xls_lig = '1';
foreach($colonnes as $colonne) {
  $feuille->setCellValue($xls_col . $xls_lig, $colonne);
  $xls_col++;
}

// on rempli les données ligne par ligne
$xls_lig++;
while($row = $adherents->fetch_array(MYSQLI_ASSOC)) {
  $xls_col = 'A';
  foreach($colonnes as $colonne) {
    if(isset($row[$colonne])) {
      $feuille->setCellValue($xls_col . $xls_lig, $row[$colonne]);
    }
    $xls_col++;
  }
  $xls_lig++;
}
$adherents->close();

// sauvegarder au format .xls (Excel 95)
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// sauvegarder au format .xlsx (Excel 2007)
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("{$_SESSION['region']}/export.xlsx");

$lien_telecharger = "<form action='{$_SESSION['region']}/export.xlsx' " .
  "method='get'><input type='submit' value='Téléchargez le fichier'></form>";
echo $lien_telecharger;

?>


</body>
</html>





