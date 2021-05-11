<?php

require_once "donnees.php";
require_once "vue.php";

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

session_start();

// Delete the content of a column
function column_delete($colonne)
{
    global $colonnes;

    if (in_array($colonne, $colonnes) == false)
    {
        return;
    }

    $member_list = member_get_list();
    foreach($member_list as $member)
    {
        member_attr_del($member["numero_adherent"], $colonne);
    }
}

// Copy a column into another
function column_copy($col1, $col2)
{
    global $colonnes;

    if ((in_array($col1, $colonnes) == false) || (in_array($col2, $colonnes) == false))
    {
        return;
    }

    $member_list = member_get_list();
    foreach ($member_list as $member)
    {
        if (array_key_exists($col1, $member) == true)
        {
            member_update($member["numero_adherent"], $col2, $member[$col1]);
        }
        else
        {
            member_attr_del($member["numero_adherent"], $col2);
        }
    }
}

if (is_privileged() == false)
{
    header("Location: index.php");
    exit();
}

display_header("Fonctions Nationales");
display_navigation();

// basculement des cotisations de l'année courante vers la colonne d'archive
if ((empty($_POST) == false) && (isset($_POST["transition"]) == true))
{
    column_copy("cotis_payee", "adhesion");
    column_delete("cotis_payee");

    $message = "";
    $message .= "<div class='section'>";
    $message .= "<p>Basculement effectué avec succès</p>";
    $message .= "<p>Il est conseillé de vérifier le résultat en exportant le nouveau fichier Excel ainsi généré</p>";
    $message .= "</div>";

    echo $message;
}

// suppression de l'adhérent
if ((empty($_POST) == false) && (isset($_POST["supprimer"]) == true))
{
    if (isset($_POST["numero_adherent"]))
    {
        member_del($_POST["numero_adherent"]);
    }
}

// suppression d'une colonne
if ((empty($_POST) == false) && (isset($_POST["supprimer_colonne"]) == true))
{
    column_delete($_POST["supprimer_colonne"]);
}

$page = "";

// Display a section for annual transition
$page .= "<div class='section'>";
$page .= "<h1>Basculement des cotisations</h1>";
$page .= "<p><strong>ATTENTION</strong> : cette action est à réaliser avec précaution. ";
$page .= "Elle a pour but d'effectuer la transition annuelle des comptes.</p>";
$page .= "<p>Si vous cliquez sur le bouton, les données de l'année précédente seront effacées ";
$page .= "et remplacées par celles de l'année courante.</p>";
$page .= "<form action='national.php' method='post'>";
$page .= "<input type='hidden' name='transition'>";
$page .= "<input type='submit' value='Effectuer la transition annuelle'>";
$page .= "</form>";
$page .= "</div>";

// Display a section to delete columns
$page .= "<div class='section'>";
$page .= "<h1>Supprimer une colonne</h1>";
$page .= "<table>";
$page .= "<tr><th>Colonne</th><th>Action</th></tr>";
foreach (array("date_paiement", "c1", "c2", "c3", "c4", "c5", "c6", "c7", "c8", "c9") as $colonne)
{
    $page .= "<tr><td>$colonne</td><td>";
    $page .= "<form method='post' action='national.php'>";
    $page .= "<input type='hidden' name='supprimer_colonne' value='$colonne'>";
    $page .= "<input type='submit' value='Supprimer'>";
    $page .= "</form></td></tr>";
}
$page .= "</table></div>";

echo $page;

display_member_list("national.php", "supprimer");

display_footer();

?>
