<?php

require_once "donnees.php";
require_once "member.php";
require_once "vue.php";
require_once "mail.php";

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

session_start();

if (is_connected() == false)
{
    header("Location: index.php");
    exit();
}

if ((empty($_POST) == false) && (isset($_POST["ajouter"]) == true))
{
    if ((array_key_exists("nom", $_POST) == true) &&
        (array_key_exists("prenom", $_POST) == true) &&
        (array_key_exists("region", $_POST) == true) &&
        (empty($_POST["nom"]) == false) &&
        (empty($_POST["prenom"]) == false) &&
        (empty($_POST["region"]) == false))
    {
        check_column_data("nom", $_POST["nom"]);
        check_column_data("prenom", $_POST["prenom"]);
        check_column_data("region", $_POST["region"]);

        $numero_adherent = member_add($_POST["nom"], $_POST["prenom"], $_POST["region"]);

        foreach($colonnes as $colonne)
        {
            if ((array_key_exists($colonne, $_POST) == true) && (empty($_POST[$colonne]) == false))
            {
                check_column_data($colonne, $_POST[$colonne]);
                member_update($numero_adherent, $colonne, $_POST[$colonne]);
            }
        }

        $message = "";
        $message .= "<p>Message provenant de 'liste_adherents.php' :</p>";
        $message .= "<p>Création d'un adhérent suite à la saisie du formulaire d'ajout</p>";

        email_add_member($numero_adherent, $message);
    }
}
elseif((empty($_POST) == false) && (isset($_POST["modifier"]) == true))
{
    if (isset($_POST["numero_adherent"]) == true)
    {
        // update de toutes les données envoyées par POST
        $numero_adherent = $_POST["numero_adherent"];
        $adherent_legacy = member_get($numero_adherent);
        $mail_modifications = array();

        foreach($colonnes as $colonne)
        {
            if ((array_key_exists($colonne, $_POST) == true) && (empty($_POST[$colonne]) == false))
            {
                check_column_data($colonne, $_POST[$colonne]);
                member_update($numero_adherent, $colonne, $_POST[$colonne]);
                $mail_modifications[$colonne] = $_POST[$colonne];
            }
        }

        $message = "";
        $message .= "<p>Message provenant de 'liste_adherents.php' :</p>";
        $message .= "<p>Modification d'un adhérent suite à la saisie du formulaire</p>";

        email_modify_member($mail_modifications, $message, $adherent_legacy);
    }
}

// Display page
display_header("Liste des adhérents");
display_navigation();
display_member_list("ajouter_adherent.php", "afficher");
display_footer();

?>
