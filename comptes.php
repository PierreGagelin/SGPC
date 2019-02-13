<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once('donnees.php');

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
  <title>Gestion des comptes</title>
</head>
<body>

<?php

require_once('account.php');

// Ajouter le compte
if (!empty($_POST) && isset($_POST["ajouter_compte"]))
{
    if (isset($_POST["region"]) && isset($_POST["identifiant"]) && isset($_POST["mot_de_passe"]))
    {
        $user = $_POST["identifiant"];
        $password = $_POST["mot_de_passe"];
        $region = $_POST["region"];

        if (is_priviledged() == false)
        {
            die("Echec de l'ajout du compte : vous n'avez pas les droits requis");
        }

        verifier("identifiant", $user);
        verifier("mot_de_passe", $password);
        verifier("region_compte", $region);

        account_add($user, $password, $region);
    }
}

// Supprimer le compte
if (!empty($_POST) && isset($_POST["supprimer_compte"]) && isset($_POST["identifiant"]))
{
    if (is_priviledged() == false)
    {
        die("Echec de la suppression du compte : vous n'avez pas les droits");
    }

    account_del($user);
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
