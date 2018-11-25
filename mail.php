<?php

require_once("donnees.php");
require_once("vue.php");

// un contact contient un nom et une adresse
//XXX: seul quelques adresses fonctionnent avec OVH
$contact_sgpc = array(
  "nom" => "Trésorier National SGPCCFECGC",
  "adresse" => "sgpc.cfecgctn@gmail.com"
);
$contact_mail = array(
  "nom" => "Mailer SGPC",
  "adresse" => "mailer.sgpc@gmail.com"
);

// selon les mails il faut adapter le passage à la ligne
// normalement c'est \r\n mais certains hébergeurs remplacent automatiquement
// les \n par des \r\n.
// Il faut donc les lister pour éviter les bug d'affichage
$mail = $contact_sgpc["adresse"];
if(!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) {
  $passage_ligne = "\r\n";
} else {
  $passage_ligne = "\n";
}

// génère une frontière aléatoire pour les séparateurs du mail
// une même frontière doit être utilisée tout au long d'un même mail
function generer_frontiere() {
  global $passage_ligne;
  $frontiere = md5(rand());
  $frontieres = array();
  $frontieres["frontiere"] = $frontiere;
  $frontieres["ouverture"] = $passage_ligne . "--" . $frontiere .
    $passage_ligne;
  $frontieres["fermeture"] = $passage_ligne . "--" . $frontiere .
    "--" . $passage_ligne;
  return $frontieres;
}

function ajouter_expediteur($contact) {
  global $passage_ligne;
  if(isset($contact["nom"]) && isset($contact["adresse"])) {
    $nom = $contact["nom"];
    $adresse = $contact["adresse"];
  } else {
    return 0;
  }
  $expediteur = "From: \"$nom\"<$adresse>$passage_ligne";
  return $expediteur;
}

function ajouter_destinataire_reponse($contact) {
  global $passage_ligne;
  if(isset($contact["nom"]) && isset($contact["adresse"])) {
    $nom = $contact["nom"];
    $adresse = $contact["adresse"];
  } else {
    return 0;
  }
  $retour = "Reply-to: \"$nom\" <$adresse>$passage_ligne";
  return $retour;
}

function ajouter_type_contenu($type, $frontiere) {
  global $passage_ligne;
  $contenu = "Content-Type: $type;$passage_ligne" .
    " boundary=\"$frontiere\"$passage_ligne";
  return $contenu;
}

function header_mail($expediteur, $destinataire, $type, $frontiere) {
  global $passage_ligne;
  $header = "";
  $header .= ajouter_expediteur($expediteur);
  $header .= ajouter_destinataire_reponse($expediteur);
  $header .= "MIME-Version: 1.0$passage_ligne";
  $header .= ajouter_type_contenu($type, $frontiere);
  return $header;
}

function header_PLAIN() {
  global $passage_ligne;
  $header_plain = "Content-Type: text/plain; charset=\"UTF-8\"" .
    $passage_ligne;
  $header_plain .= "Content-Transfer-Encoding: 8bit$passage_ligne";
  return $header_plain;
}

function header_HTML() {
  global $passage_ligne;
  $header_html = "Content-Type: text/html; charset=\"UTF-8\"" .
    $passage_ligne;
  $header_html .= "Content-Transfer-Encoding: 8bit$passage_ligne";
  return $header_html;
}

function ajouter_messages($message_html, $message_plain, $frontieres) {
  global $passage_ligne;
  $ouverture_frontiere = $frontieres["ouverture"];
  $fermeture_frontiere = $frontieres["fermeture"];

  $message = $ouverture_frontiere;
  $message .= header_PLAIN();
  $message .= $passage_ligne . $message_plain . $passage_ligne;

  $message .= $ouverture_frontiere;
  $message .= header_HTML();
  $message .= $passage_ligne . $message_html . $passage_ligne;

  $message .= $fermeture_frontiere;
  $message .= $fermeture_frontiere;

  return $message;
}

function test_mail() {
  global $contact_sgpc;
  global $passage_ligne;
  $frontieres = generer_frontiere();
  $frontiere = $frontieres["frontiere"];
  $ouverture_frontiere = $frontieres["ouverture"];
  $fermeture_frontiere = $frontieres["fermeture"];

  $message_html = "<html><head></head><body><b>Salut</b> poulet</body></html>";
  $message_plain = "Salut ma poule";

  $message = $ouverture_frontiere;
  $message .= header_PLAIN();
  $message .= $passage_ligne . $message_plain . $passage_ligne;

  $message .= $ouverture_frontiere;
  $message .= header_HTML();
  $message .= $passage_ligne . $message_html . $passage_ligne;

  $message .= $fermeture_frontiere;
  $message .= $fermeture_frontiere;

  $header = header_mail($contact_sgpc, $contact_sgpc,
                        "multipart/alternative", $frontiere);
  $sujet = "Mail de test";
  mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

//test_mail();
//echo "Fin de l'envoi";

// mail notifiant la suppression d'une colonne pour un adhérent
//    @numero_adherent : numéro de l'adhérent concerné
//    @colonne : colonne concernée par la suppression
//    @message : message indiquant la provenance de l'action
//    @adherent_legacy : état de l'adhérent avant suppression
function mail_supprimer_colonne($numero_adherent, $colonne, $message,
    $adherent_legacy) {
  if(!est_connecte()) {
    return;
  }
  global $contact_sgpc;
  global $contact_mail;
  $frontieres = generer_frontiere();
  $session_region = $_SESSION["region"];
  $session_identifiant = $_SESSION["identifiant"];

  $message_html = "<html><head></head><body>" .
    "<p>Ceci est un mail automatique de l'interface de gestion de la " .
    "trésorerie de SGPC-CFE-CGC.</p>" .
    "<h3>Provenance :</h3>" .
    $message .
    "<h3>Informations complémentaires :</h3>" .
    "<p>Suppression de la colonne $colonne pour l'adhérent $numero_adherent " .
    "par le compte '$session_identifiant' de la région '$session_region'</p>" .
    "<h3>Récapitulatif adhérent :</h3>" .
    vue_adherent($numero_adherent) .
    "<h3>Récapitulatif adhérent avant suppression :</h3>" .
    vue_tableau($adherent_legacy) .
    "</body></html>";
  $message_plain = "Le message est censé s'afficher en HTML avec Gmail!";
  $message = ajouter_messages($message_html, $message_plain, $frontieres);

  $header = header_mail($contact_mail, $contact_sgpc,
                        "multipart/alternative", $frontieres["frontiere"]);
  $sujet = "[Mail-Automatique] Suppression d'une information adhérent";
  mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

function mail_creer_adherent($numero_adherent, $message) {
  if(!est_connecte()) {
    return;
  }
  global $contact_sgpc;
  global $contact_mail;
  $frontieres = generer_frontiere();
  $session_region = $_SESSION["region"];
  $session_identifiant = $_SESSION["identifiant"];

  $message_html = "<html><head></head><body>" .
    "<p>Ceci est un mail automatique de l'interface de gestion de la " .
    "trésorerie de SGPC-CFE-CGC.</p>" .
    "<h3>Provenance :</h3>" .
    $message .
    "<h3>Informations complémentaires :</h3>" .
    "<p>Création de l'adhérent $numero_adherent " .
    "par le compte '$session_identifiant' de la région '$session_region'</p>" .
    "<h3>Récapitulatif adhérent :</h3>" .
    vue_adherent($numero_adherent) .
    "</body></html>";
  $message_plain = "Le message est censé s'afficher en HTML avec Gmail!";
  $message = ajouter_messages($message_html, $message_plain, $frontieres);

  $header = header_mail($contact_mail, $contact_sgpc,
                        "multipart/alternative", $frontieres["frontiere"]);
  $sujet = "[Mail-Automatique] Création d'un nouvel adhérent";
  mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

function mail_modifier_adherent($modifications, $message, $adherent_legacy) {
  if(!est_connecte() || !isset($modifications["numero_adherent"])) {
    return;
  }
  $numero_adherent = $modifications["numero_adherent"];
  global $contact_sgpc;
  global $contact_mail;
  $frontieres = generer_frontiere();
  $session_region = $_SESSION["region"];
  $session_identifiant = $_SESSION["identifiant"];

  $message_html = "<html><head></head><body>" .
    "<p>Ceci est un mail automatique de l'interface de gestion de la " .
    "trésorerie de SGPC-CFE-CGC.</p>" .
    "<h3>Provenance :</h3>" .
    $message .
    "<h3>Informations complémentaires :</h3>" .
    "<p>Modification de l'adhérent $numero_adherent " .
    "par le compte '$session_identifiant' de la région '$session_region'.</p>" .
    "<p>Les modifications réalisées sont les suivantes : </p>" .
    vue_tableau($modifications) .
    "<h3>Récapitulatif adhérent :</h3>" .
    vue_adherent($numero_adherent) .
    "<h3>Récapitulatif adhérent avant modification :</h3>" .
    vue_tableau($adherent_legacy) .
    "</body></html>";
  $message_plain = "Le message est censé s'afficher en HTML avec Gmail!";
  $message = ajouter_messages($message_html, $message_plain, $frontieres);

  $header = header_mail($contact_mail, $contact_sgpc,
                        "multipart/alternative", $frontieres["frontiere"]);
  $sujet = "[Mail-Automatique] Modification d'un adhérent";
  mail($contact_sgpc["adresse"], $sujet, $message, $header);
}

?>
