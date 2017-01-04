<?php

// vérification de la session
session_start();

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// gestion des donnees
require_once('donnees.php');

// gestion de la vue
require_once('vue.php');

if(!empty($_SESSION)) {
  if(!isset($_SESSION['identifiant']) || !isset($_SESSION['region'])) {
    header('Location: index.php');
    exit();
  }
} else {
  header('Location: index.php');
  exit();
}

// suppression d'une colonne spécifique pour l'adhérent
if( !empty($_POST) && isset($_POST['supprimer_ligne']) &&
    isset($_POST['numero_adherent']) &&
    isset($_POST['colonne'])) {
  $numero_adherent = $_POST['numero_adherent'];
  $adherent_legacy = adherent_tableau($numero_adherent);
  $colonne = $_POST['colonne'];
  supprimer($numero_adherent, $colonne);
  
  // envoi d'un mail pour prévenir de la suppression
  require_once("mail.php");
  $mail_message = "<p>Message provenant de 'ajouter_adherent.php' : </p>" .
      "<p>Suppression d'une colonne pour un adhérent</p>";
  mail_supprimer_colonne($numero_adherent, $colonne, $mail_message,
    $adherent_legacy);
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" />
  <title>Ajout ou modification d'adhérent</title>
</head>
<body>

<?php
// afficher le filtre et la barre de navigation
afficher_navigation();
afficher_filtre("ajouter_adherent.php");
?>

<div id="page">
<h2>Ajout ou modification d'un adhérent</h2>
<p>
Pour ajouter ou modifier un adhérent il suffit de remplir les informations 
dans le formulaire ci dessous :
</p>

<?php

// si le numéro d'adhérent transite, on récupère les informations de l'adhérent
// en fonction des données présentes dans la base
if(!empty($_POST) && isset($_POST['numero_adherent'])) {
  $numero_adherent = $_POST['numero_adherent'];
  $adherent = adherent_tableau($numero_adherent);
}

// formulaire d'informations
$formulaire = '<form action="liste_adherents.php" method="post">';

// on vérifie s'il s'agit d'un ajout ou d'un affichage
//   - pour un ajout on pose un marqueur distinctif pour savoir qu'on ajoute
//   - sinon on ajoute aussi le numéro d'adhérent pour les modifications
if(!empty($_POST) && isset($_POST['afficher'])) {
  $formulaire .= "<input type='hidden' name='numero_adherent' " .
    "value='{$_POST['numero_adherent']}'>";
  $formulaire .= '<input type="hidden" name="modifier">';
  $type = "Modifier";
} else {
  $formulaire .= '<input type="hidden" name="ajouter">';
  $type = "Ajouter";
}

// routine d'affichage des lignes
//   - en fonction du filtre de session
//   - des données présentes dans le POST
foreach($colonnes as $colonne) {
  if(isset($vue[$colonne])) {
    if( !empty($_SESSION) && isset($_SESSION['filtre']) &&
        $_SESSION['filtre'][$colonne] == 'off') {
      // on affiche rien
    } elseif(isset($adherent[$colonne])) {
      $ligne = "$colonne : ({$adherent[$colonne]}){$vue[$colonne]}";
      $formulaire .= $ligne;
    } else {
      $ligne = "$colonne : " . $vue[$colonne];
      $formulaire .= $ligne;
    }
  }
}
$formulaire .=  "<input type='submit' value='$type'></form>";
echo $formulaire . "</div>";

//===== section pour supprimer des informations spécifiques à l'adhérent
// génération du HTML de la section
//   - visiblement seulement pour une modification avec numéro adhérent connu
//   - chaque ligne est un formulaire permettant de supprimer la dite ligne
//   - contient aussi de quoi recharger le reste de la page
if($type == "Modifier" && !empty($_POST) && isset($_POST['numero_adherent'])) {
  $numero_adherent = $_POST['numero_adherent'];
  $section_supprimer = "" .
    "<div class='section'>" .
    "<h2>Suppression d'informations</h2>" .
    "<p>" .
    "Ici, vous pouvez supprimer des valeurs spécifiques à l'adhérent" .
    "</p>";
  $input_afficher = "" .
    "<input type='hidden' name='afficher'>";
  foreach($colonnes as $colonne) {
    if(($colonne != "numero_adherent") && isset($_POST[$colonne])) {
      $valeur = $_POST[$colonne];
      $input_afficher .= "<input type='hidden' " .
        "name='$colonne' value='$valeur'>";
    }
  }
  foreach($colonnes as $colonne) {
    if(($colonne != "numero_adherent") && isset($adherent[$colonne])) {
      $formulaire = "<form action='ajouter_adherent.php' method='post'>" .
        $input_afficher .
        "<input type='hidden' name='supprimer_ligne'>" .
        "<input type='hidden' " .
          "name='numero_adherent' value='$numero_adherent'>" .
        "<input type='hidden' name='colonne' value='$colonne'>" .
        "$colonne : {$adherent[$colonne]}" .
        "<input type='submit' value='Supprimer'></form>";
      $section_supprimer .= $formulaire;
    }
  }
  $section_supprimer .= "</div>";
  echo $section_supprimer;
}
?>

</body>
</html>





