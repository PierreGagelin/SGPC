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
        <title>Fonctions Nationales</title>
    </head>
    <body>

<?php

require_once("donnees.php");
require_once('vue.php');

afficher_filtre("national.php");

afficher_navigation();

// basculement des cotisations de l'année courante vers la colonne d'archive
if (!empty($_POST) && isset($_POST["transition"]))
{
    basculer_cotisations();

    $message = "";
    $message .= "<div id='transition'>";
    $message .= "    Basculement effectué avec succès<br />";
    $message .= "    Il est conseillé de vérifier le résultat en important le nouveau fichier Excel ainsi généré<br />";
    $message .= "</div>";

    echo $message;
}

// suppression de l'adhérent
if (!empty($_POST) && isset($_POST["supprimer"]))
{
    if (isset($_POST["numero_adherent"]))
    {
        member_del($_POST["numero_adherent"]);
    }
}

// suppression d'une colonne
if (!empty($_POST) && isset($_POST["supprimer_colonne"]))
{
    supprimer_colonne($_POST["supprimer_colonne"]);
}

afficher_transition_annuelle();

afficher_supprimer_colonne("date_paiement");
afficher_supprimer_colonne("c1");
afficher_supprimer_colonne("c2");
afficher_supprimer_colonne("c3");
afficher_supprimer_colonne("c4");
afficher_supprimer_colonne("c5");
afficher_supprimer_colonne("c6");
afficher_supprimer_colonne("c7");
afficher_supprimer_colonne("c8");
afficher_supprimer_colonne("c9");

afficher_liste_adherents("national.php", "supprimer");

?>

    </body>
</html>
