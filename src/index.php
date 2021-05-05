<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once("account.php");
require_once("donnees.php");
require_once("vue.php");

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
    $account = account_get($user);
    if (empty($account) == true)
    {
        echo "Erreur : utilisateur inconnu identifiant=$user<br />";
        return;
    }
    if ($account["password"] != $password)
    {
        echo "Erreur : mot de passe erroné<br />";
        return;
    }

    $_SESSION["user"] = $account["user"];
    $_SESSION["region"] = $account["region"];
    $_SESSION["privileged"] = $account["privileged"];

    header('Location: liste_adherents.php');
    exit();
}

if ((empty($_POST) == false) && (array_key_exists('connexion', $_POST) == true))
{
    if (!isset($_POST['identifiant']) || !isset($_POST['mot_de_passe']))
    {
        echo "Erreur : identifiant ou mot de passe vide<br />";
    }
    else
    {
        $user = $_POST['identifiant'];
        $password = $_POST['mot_de_passe'];

        authentification($user, $password);
    }
}

afficher_header("Page d'accueil");

?>

<div class="standalone">
    <p>
        Merci de vous connecter pour pouvoir continuer :
    </p>
    <form action="index.php" method="post">
        Identifiant : <input type="text" name="identifiant"><br />
        Mot de passe : <input type="password" name="mot_de_passe"><br />
        <input type="hidden" name="connexion" value="useless">
        <input type="submit" value="Connexion">
    </form>
</div>

<?php

afficher_footer();

?>
