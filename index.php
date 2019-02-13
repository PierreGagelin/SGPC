<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("account.php");
require_once("donnees.php");

// Handle disconnection
if (!empty($_POST) && isset($_POST['deconnexion']))
{
    $_SESSION = array();
}

if (is_connected() == true)
{
    header('Location: liste_adherents.php');
    exit();
}

//
// Authenticate the user's session
//
function authentification($user, $password)
{
    global $ACCOUNT_ARRAY;

    if (account_password_exist($user, $password) == false)
    {
        echo "Echec de l'authentification : utilisateur inconnu [identifiant=$user]<br />";
        return;
    }

    $_SESSION["identifiant"] = $user;
    $_SESSION["region"] = $ACCOUNT_ARRAY[$user]["region"];
    $_SESSION["priviledged"] = $ACCOUNT_ARRAY[$user]["priviledged"];

    header('Location: liste_adherents.php');
    exit();
}

if (!empty($_POST))
{
    if (!isset($_POST['identifiant']) || !isset($_POST['mot_de_passe']))
    {
        echo "Vous avez oublié l'identifiant ou le mot de passe !<br />";
    }
    else
    {
        $user = $_POST['identifiant'];
        $password = $_POST['mot_de_passe'];

        authentification($user, $password);
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
                <input type="submit" value="Connexion">
            </form>
        </div>
    </body>
</html>
