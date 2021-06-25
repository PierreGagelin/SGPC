<?php

# Composer libraries
require_once "vendor/autoload.php";

# SGPC libraries
require_once "donnees.php";
require_once "vue.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$MAIL_CONFIG_FILE = "mail.json";
$MAIL_CONFIG = array();

function email_init()
{
    global $MAIL_CONFIG_FILE;
    global $MAIL_CONFIG;

    $json = file_get_contents($MAIL_CONFIG_FILE);
    if ($json == false)
    {
        die("Erreur : échec de la lecture du fichier des paramètres de connexion");
    }

    // Decode as an associative array
    $MAIL_CONFIG = json_decode($json, true);
    if ($MAIL_CONFIG == null)
    {
        die("Erreur : échec du décodage JSON des paramètres de connexion");
    }
}

function email_display_array($tableau)
{
    $entry = "<ul>";
    foreach(array_keys($tableau) as $col)
    {
        $entry .= "<li>$col : {$tableau[$col]}</li>";
    }
    $entry .= "</ul>";
    return $entry;
}

function email_display_member($numero_adherent)
{
    $adherent = member_get($numero_adherent);
    $entry = email_display_array($adherent);
    return $entry;
}

function email_send($subject, $message_html, $message_plain)
{
    global $MAIL_CONFIG;

    // Passing "true" enables exceptions
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Host = $MAIL_CONFIG["smtp_host"];
        $mail->Username = $MAIL_CONFIG["smtp_user"];
        $mail->Password = $MAIL_CONFIG["smtp_password"];
        $mail->Port = $MAIL_CONFIG["smtp_port"];

        // Recipients
        $mail->setFrom($MAIL_CONFIG["from_addr"], $MAIL_CONFIG["from_name"]);
        $mail->addAddress($MAIL_CONFIG["to_addr"], $MAIL_CONFIG["to_name"]);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message_html;
        $mail->AltBody = $message_plain;

        $mail->send();
    } catch (Exception $e) {
        die("Erreur : échec lors de l'envoi du courrier électronique");
    }
}

function email_delete_column($numero_adherent, $colonne, $message, $adherent_legacy)
{
    if (is_connected() == false)
    {
        return;
    }

    $session_region = $_SESSION["region"];
    $session_identifiant = $_SESSION["user"];

    $message_html = "";
    $message_html .= "<html><head></head><body>";
    $message_html .= "<p>Ceci est un mail automatique de l'interface de gestion de la ";
    $message_html .= "trésorerie de SGPC-CFE-CGC.</p>";
    $message_html .= "<h3>Provenance :</h3>";
    $message_html .= $message;
    $message_html .= "<h3>Informations complémentaires :</h3>";
    $message_html .= "<p>Suppression de la colonne $colonne pour l'adhérent $numero_adherent ";
    $message_html .= "par le compte '$session_identifiant' de la région '$session_region'</p>";
    $message_html .= "<h3>Récapitulatif adhérent :</h3>";
    $message_html .= email_display_member($numero_adherent);
    $message_html .= "<h3>Récapitulatif adhérent avant suppression :</h3>";
    $message_html .= email_display_array($adherent_legacy);
    $message_html .= "</body></html>";

    $message_plain = "Le message est censé s'afficher en HTML avec Gmail!";

    $sujet = "[Mail-Automatique] Suppression d'une information adhérent";

    email_send($sujet, $message_html, $message_plain);
}

function email_add_member($numero_adherent, $message)
{
    if (is_connected() == false)
    {
        return;
    }

    $session_region = $_SESSION["region"];
    $session_identifiant = $_SESSION["user"];

    $message_html = "";
    $message_html .= "<html><head></head><body>";
    $message_html .= "<p>Ceci est un mail automatique de l'interface de gestion de la ";
    $message_html .= "trésorerie de SGPC-CFE-CGC.</p>";
    $message_html .= "<h3>Provenance :</h3>";
    $message_html .= $message;
    $message_html .= "<h3>Informations complémentaires :</h3>";
    $message_html .= "<p>Création de l'adhérent $numero_adherent ";
    $message_html .= "par le compte '$session_identifiant' de la région '$session_region'</p>";
    $message_html .= "<h3>Récapitulatif adhérent :</h3>";
    $message_html .= email_display_member($numero_adherent);
    $message_html .= "</body></html>";

    $message_plain = "Le message est censé s'afficher en HTML avec Gmail!";

    $sujet = "[Mail-Automatique] Création d'un nouvel adhérent";

    email_send($sujet, $message_html, $message_plain);
}

function email_modify_member($modifications, $message, $adherent_legacy)
{
    if ((is_connected() == false) || (isset($modifications["numero_adherent"]) == false))
    {
        return;
    }

    $numero_adherent = $modifications["numero_adherent"];

    $session_region = $_SESSION["region"];
    $session_identifiant = $_SESSION["user"];

    $message_html = "";
    $message_html .= "<html><head></head><body>" .
    $message_html .= "<p>Ceci est un mail automatique de l'interface de gestion de la ";
    $message_html .= "trésorerie de SGPC-CFE-CGC.</p>";
    $message_html .= "<h3>Provenance :</h3>";
    $message_html .= $message;
    $message_html .= "<h3>Informations complémentaires :</h3>";
    $message_html .= "<p>Modification de l'adhérent $numero_adherent ";
    $message_html .= "par le compte '$session_identifiant' de la région '$session_region'.</p>";
    $message_html .= "<p>Les modifications réalisées sont les suivantes : </p>";
    $message_html .= email_display_array($modifications);
    $message_html .= "<h3>Récapitulatif adhérent :</h3>";
    $message_html .= email_display_member($numero_adherent);
    $message_html .= "<h3>Récapitulatif adhérent avant modification :</h3>";
    $message_html .= email_display_array($adherent_legacy);
    $message_html .= "</body></html>";

    $message_plain = "Le message est censé s'afficher en HTML avec Gmail!";

    $sujet = "[Mail-Automatique] Modification d'un adhérent";

    email_send($sujet, $message_html, $message_plain);
}

email_init();

?>
