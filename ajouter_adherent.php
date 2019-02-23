<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require_once('sgpc_session.php');
require_once('donnees.php');
require_once('vue.php');
require_once("mail.php");

if (is_connected() == false)
{
    header('Location: index.php');
    exit();
}

// suppression d'une colonne spécifique pour l'adhérent
if (!empty($_POST) && isset($_POST['supprimer_ligne']) && isset($_POST['numero_adherent']) && isset($_POST['colonne']))
{
    $numero_adherent = $_POST['numero_adherent'];
    $colonne = $_POST['colonne'];

    $adherent_legacy = member_get($numero_adherent);
    member_attr_del($numero_adherent, $colonne);

    // envoi d'un mail pour prévenir de la suppression
    $mail_message = "";
    $mail_message .= "<p>Message provenant de 'ajouter_adherent.php' : </p>";
    $mail_message .= "<p>Suppression d'une colonne pour un adhérent</p>";
    mail_supprimer_colonne($numero_adherent, $colonne, $mail_message, $adherent_legacy);
}

afficher_header("Ajout ou modification d'adhérent");
afficher_navigation();
afficher_filtre("ajouter_adherent.php");

?>

<div id="page">
<h2>Ajout ou modification d'un adhérent</h2>
<p>
    Pour ajouter ou modifier un adhérent il suffit de remplir les informations
    dans le formulaire ci dessous :
</p>

<?php

// si le numéro d'adhérent transite, on récupère les informations de l'adhérent
// en fonction des données présentes dans la base
if (!empty($_POST) && isset($_POST['numero_adherent']))
{
    $numero_adherent = $_POST['numero_adherent'];
    $adherent = member_get($numero_adherent);
}

// formulaire d'informations
$formulaire = '<form action="liste_adherents.php" method="post">';

// on vérifie s'il s'agit d'un ajout ou d'un affichage
//   - pour un ajout on pose un marqueur distinctif pour savoir qu'on ajoute
//   - sinon on ajoute aussi le numéro d'adhérent pour les modifications
if (!empty($_POST) && isset($_POST['afficher']))
{
    $formulaire .= "<input type='hidden' name='numero_adherent' value='$numero_adherent'>";
    $formulaire .= '<input type="hidden" name="modifier">';
    $type = "Modifier";
}
else
{
    $formulaire .= '<input type="hidden" name="ajouter">';
    $type = "Ajouter";
}

// routine d'affichage des lignes
//   - en fonction du filtre de session
//   - des données présentes dans le POST
foreach($colonnes as $colonne)
{
    if (isset($vue[$colonne]))
    {
        if ( !empty($_SESSION) && isset($_SESSION['filtre']) && $_SESSION['filtre'][$colonne] == 'off')
        {
            // on affiche rien
        }
        elseif (isset($adherent[$colonne]))
        {
            $ligne = "$colonne : ({$adherent[$colonne]}){$vue[$colonne]}";
            $formulaire .= $ligne;
        }
        else
        {
            $ligne = "$colonne : " . $vue[$colonne];
            $formulaire .= $ligne;
        }
    }
}
$formulaire .=  "<input type='submit' value='$type'></form>";
echo $formulaire . "</div>";

//
// Section pour supprimer des informations spécifiques à l'adhérent
//

// génération du HTML de la section
//   - visiblement seulement pour une modification avec numéro adhérent connu
//   - chaque ligne est un formulaire permettant de supprimer la dite ligne
//   - contient aussi de quoi recharger le reste de la page
if ($type == "Modifier" && !empty($_POST) && isset($_POST['numero_adherent']))
{
    $numero_adherent = $_POST['numero_adherent'];

    $section_supprimer = "";
    $section_supprimer .= "<div class='section'>";
    $section_supprimer .= "<h2>Suppression d'informations</h2>";
    $section_supprimer .= "<p>Ici, vous pouvez supprimer des valeurs spécifiques à l'adhérent</p>";

    $input_afficher = "<input type='hidden' name='afficher'>";

    foreach($colonnes as $colonne)
    {
        if (($colonne != "numero_adherent") && isset($_POST[$colonne]))
        {
            $valeur = $_POST[$colonne];
            $input_afficher .= "<input type='hidden' name='$colonne' value='$valeur'>";
        }
    }
    foreach($colonnes as $colonne)
    {
        if (($colonne != "numero_adherent") && isset($adherent[$colonne]))
        {
            $formulaire = "";
            $formulaire .= "<form action='ajouter_adherent.php' method='post'>";
            $formulaire .= "<input type='submit' value='Supprimer'>";
            $formulaire .= "$input_afficher";
            $formulaire .= "<input type='hidden' name='supprimer_ligne'>";
            $formulaire .= "<input type='hidden' name='numero_adherent' value='$numero_adherent'>";
            $formulaire .= "<input type='hidden' name='colonne' value='$colonne'>";
            $formulaire .= "$colonne : {$adherent[$colonne]}";
            $formulaire .= "</form>";

            $section_supprimer .= $formulaire;
        }
    }
    $section_supprimer .= "</div>";
    echo $section_supprimer;
}

afficher_footer()

?>
