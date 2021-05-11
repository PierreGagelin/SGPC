<?php

require_once "donnees.php";
require_once "vue.php";

$contact_sgpc = array(
    "nom" => "Trésorier National SGPCCFECGC",
    "adresse" => "sgpc.cfecgctn@gmail.com"
);

$contact_mail = array(
    "nom" => "Mailer SGPC",
    "adresse" => "mailer.sgpc@gmail.com"
);

// Adapt newline character
$mail = $contact_sgpc["adresse"];
if (preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail) == false)
{
    $newline = "\r\n";
}
else
{
    $newline = "\n";
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

// Generate email frontiers
function email_get_frontiers()
{
    global $newline;

    $frontiere = md5(rand());
    $frontieres = array();
    $frontieres["frontiere"] = $frontiere;
    $frontieres["ouverture"] = $newline . "--" . $frontiere . $newline;
    $frontieres["fermeture"] = $newline . "--" . $frontiere . "--" . $newline;

    return $frontieres;
}

function email_get_header($expediteur, $destinataire, $type, $frontiere)
{
    global $newline;

    $header = "";
    $header .= "From: \"{$expediteur["nom"]}\"<{$expediteur["adresse"]}>$newline";
    $header .= "Reply-to: \"{$expediteur["nom"]}\"<{$expediteur["adresse"]}>$newline";
    $header .= "MIME-Version: 1.0$newline";
    $header .= "Content-Type: $type;$newline";
    $header .= "boundary=\"$frontiere\"$newline";

    return $header;
}

function email_get_message($message_html, $message_plain, $frontieres)
{
    global $newline;

    $message = "";

    $message .= $frontieres["ouverture"];
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"$newline";
    $message .= "Content-Transfer-Encoding: 8bit$newline";
    $message .= $newline . $message_plain . $newline;

    $message .= $frontieres["ouverture"];
    $message .= "Content-Type: text/html; charset=\"UTF-8\"$newline";
    $message .= "Content-Transfer-Encoding: 8bit$newline";
    $message .= $newline . $message_html . $newline;

    $message .= $frontieres["fermeture"];
    $message .= $frontieres["fermeture"];

    return $message;
}

function email_delete_column($numero_adherent, $colonne, $message, $adherent_legacy)
{
    global $contact_sgpc;
    global $contact_mail;

    if (is_connected() == false)
    {
        return;
    }

    $frontieres = email_get_frontiers();
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
    $message = email_get_message($message_html, $message_plain, $frontieres);
    $header = email_get_header($contact_mail, $contact_sgpc, "multipart/alternative", $frontieres["frontiere"]);

    mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

function email_add_member($numero_adherent, $message)
{
    global $contact_sgpc;
    global $contact_mail;

    if (is_connected() == false)
    {
        return;
    }

    $frontieres = email_get_frontiers();
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
    $message = email_get_message($message_html, $message_plain, $frontieres);
    $header = email_get_header($contact_mail, $contact_sgpc, "multipart/alternative", $frontieres["frontiere"]);

    mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

function email_modify_member($modifications, $message, $adherent_legacy)
{
    global $contact_sgpc;
    global $contact_mail;

    if ((is_connected() == false) || (isset($modifications["numero_adherent"]) == false))
    {
        return;
    }

    $numero_adherent = $modifications["numero_adherent"];

    $frontieres = email_get_frontiers();
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
    $message = email_get_message($message_html, $message_plain, $frontieres);
    $header = email_get_header($contact_mail, $contact_sgpc, "multipart/alternative", $frontieres["frontiere"]);

    mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

?>
