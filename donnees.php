<?php

require_once("confidentiel.php");

// tableau de toutes les colonnes
$colonnes = array(
  "numero_adherent",
  "nom",
  "prenom",
  "cotis_payee",
  "date_paiement",
  "p_ou_rien",
  "adhesion",
  "adresse_1",
  "adresse_2",
  "code_postal",
  "commune",
  "ad",
  "profession",
  "region",
  "echelon",
  "bureau_nat",
  "comite_nat",
  "tel_port",
  "tel_prof",
  "tel_dom",
  "fonc_nat",
  "fonc_nat_irp",
  "fonc_reg",
  "fonc_reg_irp",
  "mail_priv",
  "mail_prof",
  "remarque_r",
  "remarque_n",
  "chsc_pc_r",
  "chsc_pc_n",
  "com_bud",
  "com_com",
  "com_cond",
  "com_ce",
  "com_dent",
  "com_ffass",
  "com_pharma",
  "com_ret",
  "naissance",
  "entree",
  "abcd",
  "c1",
  "c2",
  "c3",
  "c4",
  "c5",
  "c6",
  "c7",
  "c8",
  "c9"
);
//var_dump($colonnes);

//===== GESTION BASE DE DONNEES =====//

// connexion à la base de données
// gestion de l'utf8
function connexion() {
    global $cfdtl_host;
    global $cfdtl_user;
    global $cfdtl_pswd;
    global $cfdtl_base;
  $mysqli = new mysqli($cfdtl_host, $cfdtl_user, $cfdtl_pswd, $cfdtl_base);

  if ($mysqli->connect_errno) {
    echo "Echec lors de la connexion à MySQL : (" .
      $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!$mysqli->set_charset("utf8")) {
    die("Incapable de charger l'UTF-8");
  }

  return $mysqli;
}

// deconnecte de la base de données
function deconnexion($mysqli) {
  $mysqli->close();
}

// exécute la requête SQL $requete
// lorsque la requête renvoie un jeu de données (e.g. SELECT, SHOW...)
// appeler "$res->close();" pour libérer le résultat
function executer_requete($requete) {
  $bdd = connexion();
  $res = $bdd->query($requete);
  if(!$res) {
    die("Erreur : executer_requete : échec de la requête SQL<br />");
  }
  deconnexion($bdd);
  return $res;
}

//===================================//

// vérifie que la session existe
function est_connecte() {
  if(!empty($_SESSION) && isset($_SESSION["region"])) {
    return TRUE;
  } else {
    return FALSE;
  }
}

// vérifie que la session a les droits nationaux
// à appeler avant chaque opération de suppression
function est_national() {
  if( !empty($_SESSION) &&
      isset($_SESSION["region"]) &&
      $_SESSION["region"] == "National") {
    return TRUE;
  } else {
    return FALSE;
  }
}

//===== GESTION INTEGRITE DE DONNEES =====//

// tableau qui permet les vérifications
//   - contient une expression régulière
//   - un message d'erreur si la regex ne match pas
//XXX: va de pair avec les fonctions verifier_XXX
//XXX: est censé les remplacer : plus générique mais moins flexible
$verification = array(
  "numero_adherent" => array(
    "regex" => "#^[A-Z]{2}[0-9]{3}$#",
    "erreur" => "Erreur : le numéro d'adhérent doit vérifier :<br />" .
                "- deux lettres majuscules suivies de 3 chiffres<br />"
  ),
  "nom" => array(
    "regex" => "#^[\\p{L}'. \\\-]+$#u",
    "erreur" => "Erreur : sont autorisés les entrées contenant :<br />" .
                "- des lettres (majuscules, minuscules, accentuées)<br />" .
                "- des points<br />" .
                "- des espaces<br />" .
                "- des apostrophes<br />" .
                "- des tirets<br />"
  ),
  "code_postal" => array(
    "regex" => "#^[0-9]{5}$#",
    "erreur" => "Erreur : vérification du code postal :<br />" .
                "Le code postal doit être composé de 5 chiffres<br />"
  ),
  "commune" => array(
    "regex" => "#^['A-Z\\\ -]+$#",
    "erreur" => "Erreur : vérification de la commune :<br />" .
                "Sont autorisés :<br />" .
                "- des lettres de 'A' à 'Z'<br />" .
                "- des apostrophes<br />" .
                "- des espaces<br />" .
                "- des tirets<br />"
  ),
  "date_paiement" => array(
    "regex" => "#^[0123][0-9]/[01][0-9]/[12][0-9]{3}$#",
    "erreur" => "Erreur : la date n'est pas au format JJ/MM/AAAA<br />"
  ),
  "tel_port" => array(
    "regex" => "#^0[1-9][0-9]{8}$#",
    "erreur" => "Erreur : vérification du téléphone :<br />" .
                "Doit être de la forme 0[1-9]XXXXXXXX<br />"
  ),
  "mail_priv" => array(
    "regex" => "#^[a-z0-9_.-]+@[a-z0-9_.-]+\\.[a-z]+$#",
    "erreur" => "Erreur : vérification du mail :<br />" .
                "Doit être de la forme XX@XX.Y avec :<br />" .
                "X étant :<br />" .
                "- des lettres de 'a' à 'z'<br />" .
                "- des chiffres<br />" .
                "- des underscores<br />" .
                "- des points<br />" .
                "- des tirets<br />" .
                "Y étant :<br />" .
                "- des lettres de 'a' à 'z'<br />"
  ),
  "remarque_r" => array(
    "regex" => "#^[\\p{L}0-9\\\,_'. /-]+$#u",
    "erreur" => "Erreur :<br />" .
                "Ne doit contenir que :<br />" .
                "- des lettres (majuscules, minuscules, accentuées)<br />" .
                "- des chiffres<br />" .
                "- des virgules<br />" .
                "- des espaces<br />" .
                "- des apostrophes<br />" .
                "- des underscores<br />" .
                "- des slashs<br />" .
                "- des points<br />" .
                "- des tirets<br />"
  )
);
// pour les entrées qui sont équivalentes
$verification["adresse_1"] = $verification["remarque_r"];
$verification["adresse_2"] = $verification["remarque_r"];
$verification["prenom"] = $verification["nom"];
$verification["echelon"] = $verification["nom"];
$verification["tel_prof"] = $verification["tel_port"];
$verification["tel_dom"] = $verification["tel_port"];
$verification["mail_prof"] = $verification["mail_priv"];
$verification["remarque_n"] = $verification["remarque_r"];
$verification["naissance"] = $verification["date_paiement"];
$verification["entree"] = $verification["date_paiement"];
$verification["c1"] = $verification["remarque_r"];
$verification["c2"] = $verification["remarque_r"];
$verification["c3"] = $verification["remarque_r"];
$verification["c4"] = $verification["remarque_r"];
$verification["c5"] = $verification["remarque_r"];
$verification["c6"] = $verification["remarque_r"];
$verification["c7"] = $verification["remarque_r"];
$verification["c8"] = $verification["remarque_r"];
$verification["c9"] = $verification["remarque_r"];
$verification["identifiant"] = $verification["remarque_r"];
$verification["mot_de_passe"] = $verification["remarque_r"];


// affiche un message d'erreur pour une entrée
// telles que celle définie ci-après
function erreur_verification($tableau) {
  $erreur = "Erreur : les entrées autorisées sont :<br />";
  foreach($tableau as $entree) {
    $erreur .= "- $entree<br />";
  }
  die($erreur);
}

// tableau des entrées possibles par colonne
// pour les entrées statiquement définies (entre <select> HTML)
$cotis_payee = array(
  "1",
  "2",
  "3",
  "1/2",
  "3/2"
);
$p_ou_rien = array(
  "p"
);
$adhesion = $cotis_payee;
$ad = array(
  "AD",
  "AD-RSI",
  "AD-RT",
  "AD-ARS"
);
$profession = array(
  "MC",
  "CDC",
  "PHC",
  "MCCS",
  "CDCCS",
  "PHCCS",
  "MCRA",
  "MCR"
);
$region = array(
  "Alsace-Moselle",
  "Aquitaine",
  "Auvergne",
  "Bourgogne",
  "Bretagne",
  "Centre",
  "Nord-Est",
  "Midi-Pyrenees",
  "Languedoc",
  "Centre-Ouest",
  "Nord-Picardie",
  "Normandie",
  "Ile-de-France",
  "Pays-de-la-Loire",
  "Paca",
  "Rhone-Alpes",
  "Antilles",
  "Reunion",
  "RSI",
  "TN"
);
$region_compte = array(
  "Alsace-Moselle",
  "Aquitaine",
  "Auvergne",
  "Bourgogne",
  "Bretagne",
  "Centre",
  "Nord-Est",
  "Midi-Pyrenees",
  "Languedoc",
  "Centre-Ouest",
  "Nord-Picardie",
  "Normandie",
  "Ile-de-France",
  "Pays-de-la-Loire",
  "Paca",
  "Rhone-Alpes",
  "Antilles",
  "Reunion",
  "RSI",
  "TN",
  "National"
);
$bureau_nat = array(
  "1",
  "2"
);
$comite_nat = $bureau_nat;
$fonc_nat = array(
  "PN",
  "SN",
  "TN",
  "SNA",
  "TNA",
  "VPN",
  "PH",
  "TH"
);
$fonc_nat_irp = array(
  "DS",
  "CCE-T",
  "CCE-S",
  "RS",
  "CE-SEC",
  "CE-TR"
);
$fonc_reg = array(
  "P",
  "S",
  "T",
  "M",
  "SA",
  "TA",
  "VP",
  "PH"
);
$fonc_reg_irp = array(
  "DS",
  "DP-T",
  "CD-T",
  "DP-S",
  "CE-S",
  "RS",
  "CE-SEC",
  "CE-TR"
);
$chsc_pc_r = array(
  "S",
  "T",
  "t",
  "s",
  "RS"
);
$chsc_pc_n = $chsc_pc_r;
$com_bud = array(
  "M",
  "R"
);
$com_com = $com_bud;
$com_cond = $com_bud;
$com_ce = $com_bud;
$com_dent = $com_bud;
$com_ffass = $com_bud;
$com_pharma = $com_bud;
$com_ret = $com_bud;
$abcd = array(
  "A",
  "B",
  "C",
  "D",
  "Z"
);

// vérifie que la valeur est cohérente selon la colonne
function verifier($colonne, $valeur) {
  global $verification;
  try {
    global ${$colonne};
  } catch(Exception $e) {
    echo $e->getMessage() ; "<br />";
  }
  if(isset($verification[$colonne])) {
    $verif_col = $verification[$colonne];
    if(!isset($verif_col['regex']) || !isset($verif_col['erreur'])) {
      die("Erreur : tableau des vérifications corrompu !");
    }
    $regex = $verif_col['regex'];
    $erreur = $verif_col['erreur'];
    if(!preg_match($regex, $valeur)) {
      die($erreur);
    }
  } elseif(!empty(${$colonne})) {
    if(!in_array($valeur, ${$colonne})) {
      erreur_verification(${$colonne});
    }
  } else {
    die("Erreur : aucune vérification implémentée pour $colonne");
  }
}

// FIN GESTION INTEGRITE DE DONNEES
//========================================//

//===== GESTION DES ADHERENTS =====//

// renvoie le dernier numéro d'adhérent
function dernier_numero_adherent() {
  $bdd = connexion();
  $requete = "SELECT numero_adherent FROM adherents " .
    "ORDER BY numero_adherent DESC LIMIT 1";
  $res = $bdd->query($requete);
  if(!$res) {
    die("Erreur : dernier_numero_adherent : échec de la requête SQL<br />");
  }
  $first = $res->fetch_row();
  $numero_adherent = $first[0];
  $res->close();
  deconnexion($bdd);
  return $numero_adherent;
}

// envoie un booléen en fonction de l'existence du numéro d'adhérent
function adherent_existe($num_ad) {
  $requete = "SELECT numero_adherent FROM adherents " .
    "WHERE numero_adherent='$num_ad'";
  $res = executer_requete($requete);
  $row = $res->fetch_array(MYSQLI_ASSOC);
  if( !isset($row['numero_adherent']) ||
      !($row['numero_adherent'] == $num_ad)) {
    return FALSE;
  } else {
    return TRUE;
  }
}

// renvoie le numéro de l'adhérent créé en cas de succès
function creer_adherent($nom, $prenom) {
  verifier("nom", $nom);
  verifier("prenom", $prenom);
  $numero_adherent = dernier_numero_adherent();
  // en PHP l'incrémentation d'un alphanumérique marche bien
  $numero_adherent++;
  $requete = "INSERT INTO adherents(numero_adherent, nom, prenom) " .
    "VALUES ('$numero_adherent', '$nom', '$prenom')";
  executer_requete($requete);
  return $numero_adherent;
}

// insère :
//   - la valeur $valeur
//   - dans la colonne $colonne
//   - pour l'adhérent $numero_adherent
function insere($numero_adherent, $colonne, $valeur) {
  if(empty($valeur) || $colonne == "numero_adherent") {
    return;
  }
  verifier($colonne, $valeur);
  // attention, les " sont important pour prendre en compte les ' de $valeur
  $requete = "UPDATE adherents " .
    'SET ' . $colonne . '="' . $valeur . '" ' .
    "WHERE numero_adherent='$numero_adherent'";
  executer_requete($requete);
}

// liste les adhérents
// dépend de la session sur laquelle on est connecté
// renvoie le résultat de la requête
function liste_adherents() {
  if(empty($_SESSION) || !isset($_SESSION['region'])) {
    echo "Attention : Vous n'êtes connectés à aucune session";
    return;
  }
  $requete = "SELECT * FROM adherents";
  if($_SESSION['region'] != "National") {
    $requete .= " WHERE region='{$_SESSION['region']}'";
  }
  $requete .= " ORDER BY numero_adherent DESC";
  $res = executer_requete($requete);
  return $res;
}

// informations spécifiques à 1 adhérent
function adherent($numero_adherent) {
  $requete = "SELECT * FROM adherents " .
    "WHERE numero_adherent='$numero_adherent'";
  $res = executer_requete($requete);
  return $res;
}

// récupère les informations d'un adhérent au format tableau
function adherent_tableau($numero_adherent) {
  global $colonnes;
  $res = adherent($numero_adherent);
  $tableau = array();
  $row = $res->fetch_array(MYSQLI_ASSOC);
  foreach($colonnes as $colonne) {
    if(isset($row[$colonne])) {
      $tableau[$colonne] = $row[$colonne];
    }
  }
  $res->close();
  return $tableau;
}

// supprime l'adhérent en fonction du numéro d'adhérent
// DANGEREUX !!!
function supprimer_adherent($numero_adherent) {
  if(est_national()) {
    $requete = "DELETE FROM adherents " .
      "WHERE numero_adherent = '$numero_adherent'";
    executer_requete($requete);
  }
}

// supprime une colonne spécifique pour le numéro d'adhérent donné
// DANGEREUX !!!
function supprimer($numero_adherent, $colonne) {
  $requete = "UPDATE adherents " .
    "SET $colonne=NULL " .
    "WHERE numero_adherent = '$numero_adherent'";
  executer_requete($requete);
}

// FIN GESTION DES ADHERENTS
//=================================//

//===== GESTION NATIONALE =====//

// vide l'intégralité de la colonne $col
// DANGEREUX !!!
function supprimer_colonne($col) {
  if(est_national()) {
    global $colonnes;
    if(!in_array($col, $colonnes)) {
      return;
    }
    $requete = "UPDATE adherents SET $col=NULL";
    $res = executer_requete($requete);
    //$res->close();
  } else {
    die("Erreur : vous n'avez pas le droit de faire cette opération !");
  }
}

// copie la colonne $col1 vers la colonne $col2
// DANGEREUX !!!
function copier_colonne($col1, $col2) {
  if(est_national()) {
    global $colonnes;
    if(!in_array($col1, $colonnes) || !in_array($col2, $colonnes)) {
      return;
    }
    $requete1 = "SELECT numero_adherent, $col1 FROM adherents";
    $res1 = executer_requete($requete1);
    while($row = $res1->fetch_array(MYSQLI_ASSOC)) {
      if(isset($row["$col1"])) {
        $requete2 = "UPDATE adherents SET $col2={$row[$col1]} " .
          "WHERE numero_adherent='{$row['numero_adherent']}'";
      } else {
        $requete2 = "UPDATE adherents SET $col2=NULL " .
          "WHERE numero_adherent='{$row['numero_adherent']}'";
      }
      $res2 = executer_requete($requete2);
      //$res2->close();
    }
    $res1->close();
  } else {
    die("Erreur : vous n'avez pas le droit de faire cette opération !");
  }
}

// effectue le basculement annuel des cotisations :
//   - copie des cotisations de l'année vers le bilan de l'année précédente :
//     - colonne "cotis_payee" copiée dans "adhesion"
//   - remise à zéro des cotisations de l'année :
//     - suppression de la colonne "cotis_payee"
function basculer_cotisations() {
  if(est_national()) {
    copier_colonne("cotis_payee", "adhesion");
    supprimer_colonne("cotis_payee");
  } else {
    die("Erreur : vous n'avez pas le droit de faire cette opération !");
  }
}

// Vérifier si le compte existe
// Renvoyer vrai si le compte existe, faux sinon
function compte_existe($region, $identifiant, $mot_de_passe) {
    global $cfdtl_comptes;
    if(isset($cfdtl_comptes[$region][$identifiant])) {
      if($mot_de_passe == $cfdtl_comptes[$region][$identifiant]) {
        return True;
      } else {
        return False;
      }
    }
}

// Lister les comptes
// Ne pas vérifier les droits car on en a besoin à l'index de connexion
function liste_comptes() {
    $requete = "SELECT * FROM comptes";
    $res = executer_requete($requete);
    return $res;
}

// Ajouter un compte
function ajouter_compte($region, $identifiant, $mot_de_passe) {
    // Vérifier qu'on dispose des droits nationaux
    if(est_national() == FALSE) {
        die("Erreur : ajouter_compte : votre compte n'a pas le droit<br />");
    }

    // Vérifier l'intégrité des entrées
    verifier("region_compte", $region);
    verifier("identifiant", $identifiant);
    verifier("mot_de_passe", $mot_de_passe);

    // S'arrêter si le compte existe
    if(compte_existe($region, $identifiant, $mot_de_passe) == TRUE) {
        die("Erreur : ajouter_compte : le compte existe déjà<br />");
    }

    // Ajouter le compte dans la base de données
    $requete = "INSERT INTO comptes(region, identifiant, mot_de_passe) " .
        "VALUES ('$region', '$identifiant', '$mot_de_passe')";
    executer_requete($requete);
}

// Supprimer un compte
function supprimer_compte($region, $identifiant, $mot_de_passe) {
    // Vérifier qu'on dispose des droits nationaux
    if(est_national() == FALSE) {
        die("Erreur : supprimer_compte : votre compte n'a pas le droit<br />");
    }

    // Vérifier les entrées
    verifier("region_compte", $region);
    verifier("identifiant", $identifiant);
    verifier("mot_de_passe", $mot_de_passe);

    // S'arrêter si le compte n'existe pas
    if(compte_existe($region, $identifiant, $mot_de_passe) == FALSE) {
        die("Erreur : supprimer_compte : le compte n'existe pas<br />");
    }

    // Supprimer le compte de la base de données
    $requete = "DELETE FROM comptes " .
      "WHERE (region='$region' " .
      "AND identifiant='$identifiant' " .
      "AND mot_de_passe='$mot_de_passe')";
    executer_requete($requete);
}

// FIN GESTION NATIONALE
//=============================//

//===== FONCTIONS UTILES AU DEVELOPPEMENT =====//

//XXX: uniquement utile pour le développement
// affiche les 2 derniers adhérents triés par leur numéro
function afficher_base() {
  global $colonnes;
  $bdd = connexion();
  $requete = "SELECT * FROM adherents ".
    "ORDER BY numero_adherent DESC LIMIT 2";
  echo "Contenu de la table des adhérents :<br />";
  $res = $bdd->query($requete);
  if(!$res) {
    die("Erreur : afficher_base : échec de la requête SQL<br />");
  }
  while($row = $res->fetch_array(MYSQLI_ASSOC)) {
    foreach($colonnes as $colonne) {
      if(isset($row[$colonne])) {
        echo "$colonne : {$row[$colonne]}<br />";
      }
    }
    echo '<br />';
  }
  $res->close();
  deconnexion($bdd);
}

//XXX [dev]: remplace les entrées 'vide' puis 'autre' par NULL
//XXX: mysql non sensible à la casse par défaut
function remplacer_vide() {
  $bdd = connexion();
  global $colonnes;
  foreach($colonnes as $colonne) {
    $requete = 'UPDATE adherents ' .
      "SET $colonne=NULL WHERE $colonne='autre'";
    if(!$bdd->query($requete)) {
      die("Erreur : échec de la requête SQL");
    }
  }
  deconnexion($bdd);
}

/* requete utilisée pour créer la table des adherents
$requete = 'CREATE TABLE adherents (';
foreach($colonnes as $colonne) {
  if($colonnes[0] == $colonne) {
    $requete .= $colonne;
  } else {
    $requete .= ", $colonne";
  }
  $requete .= " VARCHAR(256)";
}
$requete .= ')';
//echo $requete;
$reponse = $bdd->query($requete);

while ($donnees = $reponse->fetch()) {
  var_dump($donnees);
}
*/

/* fonction utilisée pour créer la table des comptes
function creer_table_comptes() {
    $requete = '' .
    'CREATE TABLE comptes (' .
        'region VARCHAR(32), ' .
        'identifiant VARCHAR(32), ' .
        'mot_de_passe VARCHAR(32)' .
    ')';
    executer_requete($requete);
}
*/

// FIN FONCTION UTILES AU DEVELOPPEMENT
//=============================================//
