<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("donnees.php");

if (is_connected() == false)
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
        <title>Liste des adhérents</title>
    </head>
    <body>

<?php

require_once("member.php");
require_once('vue.php');

afficher_navigation();
afficher_filtre("liste_adherents.php");

// si l'adhérent a été ajouté, on reçoit un POST avec 'ajouter'
// si l'adhérent a été modifié, on reçoit un POST avec 'modifier'
if (!empty($_POST) && isset($_POST['ajouter']))
{
    if (isset($_POST['numero_adherent']))
    {
        // on a pas besoin d'ajouter l'adhérent
        // rien à faire
    }
    elseif (!isset($_POST['nom']) || !isset($_POST['prenom']))
    {
        die('il faut au minimum le nom et prenom');
    }
    else
    {
        // on crée une entrée
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $numero_adherent = creer_adherent($nom, $prenom);

        // on vérifie chaque entrée possible et on ajoute
        foreach($colonnes as $colonne)
        {
            if (isset($_POST[$colonne]))
            {
                $valeur = $_POST[$colonne];
                insere($numero_adherent, $colonne, $valeur);
            }
        }

        // on notifie par mail la création
        require_once("mail.php");

        $message = "";
        $message .= "<p>Message provenant de 'liste_adherents.php' :</p>";
        $message .= "<p>Création d'un adhérent suite à la saisie du formulaire d'ajout</p>";

        mail_creer_adherent($numero_adherent, $message);
    }
}
elseif(!empty($_POST) && isset($_POST['modifier']) && isset($_POST['numero_adherent']))
{
    // update de toutes les données envoyées par POST
    $numero_adherent = $_POST['numero_adherent'];
    $adherent_legacy = member_get($numero_adherent);
    $mail_modifications = array();

    foreach($colonnes as $colonne)
    {
        if (isset($_POST[$colonne]) && !empty($_POST[$colonne]))
        {
            $valeur = $_POST[$colonne];
            insere($numero_adherent, $colonne, $valeur);
            $mail_modifications[$colonne] = $valeur;
        }
    }

    require_once("mail.php");

    $message = "";
    $message .= "<p>Message provenant de 'liste_adherents.php' :</p>";
    $message .= "<p>Modification d'un adhérent suite à la saisie du formulaire</p>";

    mail_modifier_adherent($mail_modifications, $message, $adherent_legacy);
}

// afficher la liste des adhérents
// chaque adhérent disposera d'un bouton "afficher"
// ce bouton enverra vers la page "ajouter_adherent.php"
afficher_liste_adherents("ajouter_adherent.php", "afficher");

?>

    </body>
</html>
