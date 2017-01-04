<?php

// routine de déconnexion
if(!empty($_POST)) {
  if(isset($_POST['deconnexion'])) {
    session_start();
    $_SESSION = array();
  }
}

session_start();
if(!empty($_SESSION)) {
  if(isset($_SESSION['identifiant']) && isset($_SESSION['region'])) {
    header('Location: liste_adherents.php');
    exit();
  }
}

require_once("confidentiel.php");
// récupération des comptes confidentiels
$comptes = $cfdtl_comptes;

// vérifie le mot de passe
// inscrit les données de sessions en cas de succès
function connexion($identifiant, $mot_de_passe) {
  global $comptes;
  foreach(array_keys($comptes) as $region) {
    if(isset($comptes[$region][$identifiant])) {
      if($mot_de_passe == $comptes[$region][$identifiant]) {
        $_SESSION['identifiant'] = $identifiant;
        $_SESSION['region'] = $region;
        header('Location: liste_adherents.php');
        exit();
      } else {
        echo "Mot de passe erroné !<br />";
        return;
      }
    }
  }
  echo "Identifiant erroné !<br />";
}

if(!empty($_POST)) {
  if(!isset($_POST['identifiant']) || !isset($_POST['mot_de_passe'])) {
    echo "Vous avez oublié l'identifiant ou le mot de passe !";
  } else {
    $identifiant = $_POST['identifiant'];
    $mot_de_passe = $_POST['mot_de_passe'];
    connexion($identifiant, $mot_de_passe);
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="style.css" />
  <title>Page d'accueil</title>
</head>
<body>

<div class="fond_gris">
<p>
Merci de vous connecter pour pouvoir continuer :
</p>
<form action="index.php" method="post">
  Identifiant : <input type="text" name="identifiant"><br />
  Mot de passe : <input type="password" name="mot_de_passe"><br />
<input type="submit" value="Connexion"></form>
</div>

</body>
</html>
