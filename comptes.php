<?php

session_start();

// affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// vérification de la session
// cas particulier ici : seul les comptes nationaux sont autorisés !
if(!empty($_SESSION)) {
  if( !isset($_SESSION['identifiant']) ||
      !isset($_SESSION['region']) ||
      $_SESSION['region'] != "National") {
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
  <title>Gestion des comptes</title>
</head>
<body>

<?php

// Ajouter le compte
if(!empty($_POST) && isset($_POST["ajouter_compte"])) {
    if(     isset($_POST["region"]) &&
            isset($_POST["identifiant"]) &&
            isset($_POST["mot_de_passe"])) {
        ajouter_compte(
            $_POST["region"],
            $_POST["identifiant"],
            $_POST["mot_de_passe"]);
    }
}

// Supprimer le compte
if(!empty($_POST) && isset($_POST["supprimer_compte"])) {
    if(     isset($_POST["region"]) &&
            isset($_POST["identifiant"]) &&
            isset($_POST["mot_de_passe"])) {
        supprimer_compte(
            $_POST["region"],
            $_POST["identifiant"],
            $_POST["mot_de_passe"]);
    }
}

// Afficher la barre de navigation
require_once('vue.php');
afficher_navigation();
afficher_filtre("national.php");

// Afficher les comptes ainsi que les opérations de gestion associées
afficher_liste_comptes();
afficher_ajouter_compte();
afficher_supprimer_compte();

?>

</body>
</html>