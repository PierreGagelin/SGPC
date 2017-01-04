<?php

// vérification de la session
// cas particulier ici : seul les comptes nationaux sont autorisés !
session_start();
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
  <title>Fonctions Nationales</title>
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
afficher_filtre("national.php");

// basculement des cotisations de l'année courante vers la colonne d'archive
if(!empty($_POST) && isset($_POST["transition"])) {
  require_once("donnees.php");
  basculer_cotisations();
  echo "<div id='transition'>Basculement effectué avec succès<br />" .
    "Il est conseillé de vérifier le résultat en important le nouveau " .
    "fichier Excel ainsi généré<br /></div>";
}
// suppression de l'adhérent
if(!empty($_POST) && isset($_POST["supprimer"])) {
  if(isset($_POST["numero_adherent"])) {
    supprimer_adherent($_POST["numero_adherent"]);
  }
}

afficher_transition_annuelle();

afficher_liste_adherents("national.php", "supprimer");

?>

</body>
</html>




