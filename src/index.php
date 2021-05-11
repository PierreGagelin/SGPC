<?php

require_once "account.php";
require_once "donnees.php";
require_once "vue.php";

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

session_start();

// Disconnect the session
if ((empty($_POST) == false) && (isset($_POST["deconnexion"]) == true))
{
    $_SESSION = array();
}

if (is_connected() == true)
{
    header("Location: liste_adherents.php");
    exit();
}

// Authenticate the session
function authenticate($user, $password)
{
    $account = account_get($user);
    if (empty($account) == true)
    {
        echo "<p>Erreur : utilisateur inconnu identifiant=$user</p>";
        return;
    }
    if ($account["password"] != $password)
    {
        echo "<p>Erreur : mot de passe erroné</p>";
        return;
    }

    $_SESSION["user"] = $account["user"];
    $_SESSION["region"] = $account["region"];
    $_SESSION["privileged"] = $account["privileged"];

    header("Location: liste_adherents.php");
    exit();
}

if ((empty($_POST) == false) && (array_key_exists("connexion", $_POST) == true))
{
    if ((isset($_POST["identifiant"]) == false) || (isset($_POST["mot_de_passe"]) == false))
    {
        echo "<p>Erreur : identifiant ou mot de passe vide</p>";
    }
    else
    {
        authenticate($_POST["identifiant"], $_POST["mot_de_passe"]);
    }
}

display_header("Page d'accueil");

$page = "";

$page .= "<div class='section'>";
$page .= "<h1>Connexion</h1>";
$page .= "<form action='index.php' method='post'>";
$page .= "<table>";
$page .= "<tr><td>Identifiant</td><td><input type='text' name='identifiant'></td></tr>";
$page .= "<tr><td>Mot de passe</td><td><input type='password' name='mot_de_passe'></td></tr>";
$page .= "</table>";
$page .= "<input type='hidden' name='connexion'>";
$page .= "<input type='submit' value='Connexion'>";
$page .= "</form>";
$page .= "</div>";
echo $page;

display_footer();

?>
