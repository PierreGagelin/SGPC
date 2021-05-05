<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("donnees.php");
require_once("member.php");
require_once('vue.php');
require_once("mail.php");

if (is_connected() == false)
{
    header('Location: index.php');
    exit();
}

afficher_header("Liste des adhérents");
afficher_navigation();

// si l'adhérent a été ajouté, on reçoit un POST avec 'ajouter'
// si l'adhérent a été modifié, on reçoit un POST avec 'modifier'
if (!empty($_POST) && isset($_POST['ajouter']))
{
    if (array_key_exists("numero_adherent", $_POST) == true)
    {
        // on a pas besoin d'ajouter l'adhérent
        // rien à faire
    }
    elseif ((array_key_exists("nom", $_POST) == false) ||
            (array_key_exists("prenom", $_POST) == false) ||
            (array_key_exists("region", $_POST) == false) ||
            (empty($_POST["prenom"]) == true) ||
            (empty($_POST["nom"]) == true) ||
            (empty($_POST["region"]) == true))
    {
        die("Erreur : échec de l'ajout de l'adhérent, les champs 'nom', 'prenom' et 'region' sont requis");
    }
    else
    {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $region_name = $_POST['region'];

        $numero_adherent = sgpc_member_add($nom, $prenom, $region_name);

        // on vérifie chaque entrée possible et on ajoute
        foreach($colonnes as $colonne)
        {
            if ((array_key_exists($colonne, $_POST) == true) && (empty($_POST[$colonne]) == false))
            {
                $valeur = $_POST[$colonne];
                sgpc_member_update($numero_adherent, $colonne, $valeur);
            }
        }

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
        if ((array_key_exists($colonne, $_POST) == true) && (empty($_POST[$colonne]) == false))
        {
            $valeur = $_POST[$colonne];
            sgpc_member_update($numero_adherent, $colonne, $valeur);
            $mail_modifications[$colonne] = $valeur;
        }
    }

    $message = "";
    $message .= "<p>Message provenant de 'liste_adherents.php' :</p>";
    $message .= "<p>Modification d'un adhérent suite à la saisie du formulaire</p>";

    mail_modifier_adherent($mail_modifications, $message, $adherent_legacy);
}

// afficher la liste des adhérents
// chaque adhérent disposera d'un bouton "afficher"
// ce bouton enverra vers la page "ajouter_adherent.php"
afficher_liste_adherents("ajouter_adherent.php", "afficher");

afficher_footer();

?>
