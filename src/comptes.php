<?php

require_once "account.php";
require_once "donnees.php";
require_once "vue.php";

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

session_start();

if (is_privileged() == false)
{
    header("Location: index.php");
    exit();
}

// Add an acount
if ((empty($_POST) == false) && (isset($_POST["ajouter_compte"]) == true))
{
    if ((isset($_POST["identifiant"]) == true) &&
        (isset($_POST["mot_de_passe"]) == true) &&
        (isset($_POST["region"]) == true))
    {
        check_column_data("identifiant", $_POST["identifiant"]);
        check_column_data("mot_de_passe", $_POST["mot_de_passe"]);
        check_column_data("region_compte", $_POST["region"]);

        account_add($_POST["identifiant"], $_POST["mot_de_passe"], $_POST["region"], isset($_POST["privilege"]));
    }
}

// Delete an account
if ((empty($_POST) == false) && (isset($_POST["supprimer_compte"]) == true))
{
    if (isset($_POST["identifiant"]) == true)
    {
        account_del($_POST["identifiant"]);
    }
}

display_header("Gestion des comptes");
display_navigation();

$page = "";

// Display a section to add an account
$page .= "<div class='section'>";
$page .= "<h1>Ajouter un compte</h1>";
$page .= "<p>Renseigner le formulaire ci-dessous pour ajouter un nouveau compte</p>";
$page .= "<form method='post' action='comptes.php'>";
$page .= "<input type='hidden' name='ajouter_compte'>";
$page .= "<table>";
$page .= "<tr><td>region</td><td>{$vue["region_compte"]}</td></tr>";
$page .= "<tr><td>identifiant</td><td>{$vue["identifiant"]}</td></tr>";
$page .= "<tr><td>mot de passe</td><td>{$vue["mot_de_passe"]}</td></tr>";
$page .= "<tr><td>privilèges</td><td>{$vue["privilege"]}</td></tr>";
$page .= "</table>";
$page .= "<input type='submit' value='Ajouter'>";
$page .= "</form>";
$page .= "</div>";

// Display a section to list every account
$page .= "<div class='section'>";
$page .= "<h1>Liste des comptes</h1>";
$page .= "<table>";
$page .= "<tr><th>Region</th><th>Identifiant</th><th>Mot de passe</th><th>Privilèges</th><th>Action</th></tr>";
$account_list = account_get_list();
foreach($account_list as $account)
{
    $page .= "<tr><td>{$account["region"]}</td><td>{$account["user"]}</td><td>{$account["password"]}</td><td>{$account["privileged"]}</td>";
    $page .= "<td><form method='post' action='comptes.php'>";
    $page .= "<input type='hidden' name='supprimer_compte'>";
    $page .= "<input type='hidden' name='identifiant' value='{$account["user"]}'>";
    $page .= "<input type='submit' value='Supprimer'>";
    $page .= "</form></td></tr>";
}
$page .= "</table></div>";

echo $page;

display_footer();

?>
