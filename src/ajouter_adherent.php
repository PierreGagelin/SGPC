<?php

require_once "donnees.php";
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

// Delete a specific column for a member
if ((empty($_POST) == false) && (isset($_POST["supprimer_ligne"]) == true))
{
    if ((isset($_POST["numero_adherent"]) == true) &&
        (isset($_POST["colonne"]) == true))
    {
        $adherent_legacy = member_get($_POST["numero_adherent"]);
        member_attr_del($_POST["numero_adherent"], $_POST["colonne"]);

        // Send an email to notify deletion
        $email_message = "";
        $email_message .= "<p>Message provenant de 'ajouter_adherent.php' : </p>";
        $email_message .= "<p>Suppression d'une colonne pour un adhérent</p>";
        email_delete_column($_POST["numero_adherent"], $_POST["colonne"], $email_message, $adherent_legacy);

        unset($adherent_legacy);
        unset($email_message);
    }
}

display_header("Information adhérent");
display_navigation();

$page = "";

// Display a section to add or modify a member
$page .= "<div class='section'>";
if ((empty($_POST) == false) && (isset($_POST['afficher']) == true) && (isset($_POST['numero_adherent']) == true))
{
    $page .= "<h1>Modification d'un adhérent</h1>";
    $page .= "<p>Renseignez le formulaire ci-dessous pour modifier les informations de l'adhérent</p>";
    $page .= "<form action='liste_adherents.php' method='post'>";
    $page .= "<input type='hidden' name='numero_adherent' value='{$_POST['numero_adherent']}'>";
    $page .= "<input type='hidden' name='modifier'>";
    $type = "Modifier";
    $adherent = member_get($_POST['numero_adherent']);
}
else
{
    $page .= "<h1>Ajout d'un adhérent</h1>";
    $page .= "<p>Renseignez le formulaire ci-dessous pour ajouter un nouvel adhérent</p>";
    $page .= "<form action='liste_adherents.php' method='post'>";
    $page .= "<input type='hidden' name='ajouter'>";
    $type = "Ajouter";
    $adherent = array();
}
$page .= "<table><tr><th>Colonne</th><th>Valeur</th><th>Nouvelle valeur</th></tr>";
foreach($colonnes as $colonne)
{
    $page .= "<tr><td>$colonne</td>";

    if (isset($adherent[$colonne]))
    {
        $page .= "<td>{$adherent[$colonne]}</td>";
    }
    else
    {
        $page .= "<td></td>";
    }

    $page .= "<td>{$vue[$colonne]}</td></tr>";
}
$page .= "</table>";
$page .= "<input type='submit' value='$type'>";
$page .= "</form>";
$page .= "</div>";

// Display a section to delete member information
if (($type == "Modifier") && (empty($_POST) == false) && (isset($_POST["numero_adherent"]) == true))
{
    $page .= "<div class='section'>";
    $page .= "<h1>Suppression d'informations</h1>";
    $page .= "<p>Utilisez cette section pour supprimer des informations spécifiques à l'adhérent</p>";

    $page .= "<table><tr><th>Colonne</th><th>Valeur</th><th>Action</th></tr>";
    foreach($colonnes as $colonne)
    {
        if (($colonne != "numero_adherent") &&
            ($colonne != "nom") &&
            ($colonne != "prenom") &&
            ($colonne != "region") &&
            (array_key_exists($colonne, $adherent) == true) &&
            (empty($adherent[$colonne]) == false))
        {
            $page .= "<tr><td>$colonne</td><td>{$adherent[$colonne]}</td>";
            $page .= "<td><form action='ajouter_adherent.php' method='post'>";
            $page .= "<input type='submit' value='Supprimer'>";
            $page .= "<input type='hidden' name='afficher'>";
            $page .= "<input type='hidden' name='supprimer_ligne'>";
            $page .= "<input type='hidden' name='numero_adherent' value='{$_POST["numero_adherent"]}'>";
            $page .= "<input type='hidden' name='colonne' value='$colonne'>";
            $page .= "</form>";
        }
    }
    $page .= "</div>";
}

echo $page;

display_footer()

?>
