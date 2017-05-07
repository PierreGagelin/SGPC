<?php

require_once('donnees.php');

// gestion du filtre
// permet de ne sélectionner que certaines colonnes
if(!empty($_POST) && isset($_POST['filtre'])) {
  $_SESSION['filtre'] = array();
  foreach($colonnes as $colonne) {
    if(isset($_POST["filtre_$colonne"])) {
      $_SESSION['filtre'][$colonne] = 'on';
    } else {
      $_SESSION['filtre'][$colonne] = 'off';
    }
  }
}

// affiche une invitation à importer un fichier Excel
function afficher_import_excel() {
  $import_excel = "<div class='fond_gris'><p>Veuillez choisir le fichier " .
    "à importer :</p>" .
    '<form method="post" action="import.php" enctype="multipart/form-data">' .
    "<input type='file' name='fichier_excel' /><br />" .
    "<input type='submit' value='Importer le fichier'>" .
    "</form></div>";
  echo $import_excel;
}

// Afficher la barre de navigation
function afficher_navigation() {
  $nav = '<div id="nav">' .
    '<form action="liste_adherents.php" method="get">' .
    '<input type="submit" value="Liste des adhérents">' .
    '</form>' .
    '<form action="ajouter_adherent.php" method="get">' .
    '<input type="submit" value="Ajouter un adhérent">' .
    '</form>';
  if(est_national()) {
    $nav .= '<form action="import.php" method="get">' .
      '<input type="submit" value="Import d\'un fichier Excel">' .
      '</form>';
  }
  $nav .= '<form action="export.php" method="get">' .
    '<input type="submit" value="Exporter au format Excel">' .
    '</form>';
  if(est_national()) {
    $nav .= '<form action="national.php" method="get">' .
      '<input type="submit" value="Gestion nationale">' .
      '</form>';
    
    $nav .= '<form action="comptes.php" method="get">' .
      '<input type="submit" value="Gestion des comptes">' .
      '</form>';
  }
  $nav .= '<form action="index.php" method="post">' .
    '<input type="hidden" name="deconnexion" value="useless">' .
    '<input type="submit" value="Déconnexion">' .
    '</form></div>';
  echo $nav;
}

// affiche le panneau latéral contenant le filtrage possible
// ne pas oublier d'ajouter une entrée pour toute nouvelle page
function afficher_filtre($page) {
  if( $page != "liste_adherents.php" &&
      $page != "ajouter_adherent.php" &&
      $page != "national.php") {
    die("Erreur : page non autorisée à utiliser le filtre");
  }
  $vue_filtre = "<div id='filtre'>" .
  "<h2>Filtrage :</h2>" .
  "<form action='$page' method='post'>";
  global $colonnes;
  // on ajoute les éventuelles information du précédent POST
  // s'il s'agissait d'un affichage
  if(!empty($_POST) && isset($_POST['afficher'])) {
    $vue_filtre .= "<input type='hidden' name='afficher' value='inutile'>";
    foreach($colonnes as $colonne) {
      if(isset($_POST[$colonne])) {
        $vue_filtre .= "<input type='hidden' name='$colonne' " .
          "value='{$_POST[$colonne]}'>";
      }
    }
  }
  if(empty($_SESSION) || !isset($_SESSION['filtre'])) {
    foreach($colonnes as $colonne) {
      if( $colonne == "numero_adherent" ||
          $colonne == "nom" ||
          $colonne == "prenom") {
        $vue_filtre .= "<input type='checkbox' name='filtre_$colonne' " .
          "checked>$colonne<br />";
      } else {
        $vue_filtre .= "<input type='checkbox' name='filtre_$colonne'>" .
          "$colonne<br />";
      }
    }
  } elseif(isset($_SESSION['filtre'])) {
    foreach($colonnes as $colonne) {
      if(isset($_SESSION['filtre']) && $_SESSION['filtre'][$colonne] == 'on') {
        $vue_filtre .= "<input type='checkbox' name='filtre_$colonne' " .
          "checked>$colonne<br />";
      } else {
        $vue_filtre .= "<input type='checkbox' name='filtre_$colonne'>" .
          "$colonne<br />";
      }
    }
  }
  $vue_filtre .= '<input type="hidden" name="filtre">' .
    "<input type='submit' value='Filtrer'></form></div><br />";
  echo $vue_filtre;
}

// affiche la liste des adhérents :
//   - $page : page vers laquelle envoyer les informations
//   - $type : "afficher" ou "supprimer"
function afficher_liste_adherents($page, $type) {
  if($type == "afficher") {
    $submit = '<input type="hidden" name ="afficher" value="inutile">' .
      '<input type="submit" value="Afficher">' .
      '</form>';
  } elseif($type == "supprimer") {
    if(!est_national()) {
      return;
    }
    $submit = '<input type="hidden" name ="supprimer" value="inutile">' .
      '<input type="submit" value="Supprimer">' .
      '</form>';
  } else {
    return;
  }
  global $colonnes;
  $res = liste_adherents();
  $liste_adherents = "<div id='liste'>" .
    "<h2>Liste des adhérents</h2>";
  if(!isset($_SESSION['filtre'])) {
    while($row = $res->fetch_array(MYSQLI_ASSOC)) {
      $liste_adherents .= "<form action='$page' method='post'>";
      foreach($colonnes as $colonne) {
        if(isset($row[$colonne])) {
          // attention, pour prendre en charge les ' on doit faire attention
          $liste_adherents .= "<input type='hidden' name='$colonne' " .
            'value="' . $row[$colonne] . '">';
          if( $colonne == "numero_adherent" ||
              $colonne == "nom" ||
              $colonne == "prenom") {
            $liste_adherents .= "$colonne : {$row[$colonne]}<br />";
          }
        }
      }
      $liste_adherents .= $submit;
    }
  } else {
    while($row = $res->fetch_array(MYSQLI_ASSOC)) {
      $liste_adherents .= "<form action='$page' method='post'>";
      foreach($colonnes as $colonne) {
        if(isset($row[$colonne])) {
          // attention, pour prendre en charge les ' on doit faire attention
          $liste_adherents .= "<input type='hidden' name='$colonne' " .
            'value="' . $row[$colonne] . '">';
          if($_SESSION['filtre'][$colonne] == 'on') {
            $liste_adherents .= "$colonne : {$row[$colonne]}<br />";
          }
        }
      }
      $liste_adherents .= $submit;
    }
  }
  $res->close();
  $liste_adherents .= '</div>';
  echo $liste_adherents;
}

// affichage HTML d'un tableau en liste non-ordonnée
function vue_tableau($tableau) {
  $vue = "<ul>";
  foreach(array_keys($tableau) as $col) {
    $vue .= "<li>$col : {$tableau[$col]}</li>";
  }
  $vue .= "</ul>";
  return $vue;
}

// vue HTML d'un adhérent comme liste non-ordonnée
function vue_adherent($numero_adherent) {
  $adherent = adherent_tableau($numero_adherent);
  $vue_adherent = vue_tableau($adherent);
  return $vue_adherent;
}

// affiche le bouton pour supprimer une colonne
function afficher_supprimer_colonne($colonne) {
  if(!est_national()) {
    return;
  }
  $supprimer_colonne = "<div class='section'>" .
    "<h2>Supprimer la colonne $colonne</h2>" .
    "<p>Cliquez pour supprimer la colonne $colonne : </p>" .
    '<form method="post" action="national.php">' .
    "<input type='hidden' name='supprimer_colonne' value='$colonne'>" .
    "<input type='submit' value='Supprimer'>" .
    "</form></div>";
  echo $supprimer_colonne;
}

// affiche le bouton de basculement des cotisations
function afficher_transition_annuelle() {
  if(!est_national()) {
    return;
  }
  $transition = '<div id="transition"><h2>Basculement des cotisations</h2>' .
    "<p> <strong>ATTENTION</strong> : cette action est à réaliser avec " .
    "précaution. Elle a pour " .
    "but d'effectuer la transition annuelle des comptes. Si vous cliquez sur " .
    "le bouton, les données de l'année précédente seront effacées et " .
    "remplacées par celles de l'année courante.</p>" .
    '<form action="national.php" method="post">' .
    '<input type="hidden" name="transition" value="inutile">' .
    '<input type="submit" value="Effectuer la transition annuelle">' .
    '</form></div>';
  echo $transition;
}

// Afficher la liste des comptes
function afficher_liste_comptes() {
    $res = liste_comptes();
    $liste_comptes = "<div class='section'>" .
        "<h2>Liste des comptes</h2>";
    while($row = $res->fetch_array(MYSQLI_ASSOC)) {
        $liste_comptes .= "region : {$row['region']}<br />";
        $liste_comptes .= "identifiant : {$row['identifiant']}<br />";
        $liste_comptes .= "mot de passe : {$row['mot_de_passe']}<br /><br />";
    }
    $res->close();
    $liste_comptes .= '</div>';
    echo $liste_comptes;
}

function afficher_ajouter_compte() {
    global vue;
    
    $ajouter_compte = "<div class='section'>" .
    "<h2>Ajouter un compte</h2>" .
    "<p>Remplir le formulaire pour ajouter le compte : </p>" .
    '<form method="post" action="comptes.php">' .
    '<input type="hidden" name="ajouter_compte" value="inutile">' .
    $vue['region'] .
    $vue['identifiant'] .
    $vue['mot_de_passe'] .
    "<input type='submit' value='Ajouter'>" .
    "</form></div>";
    echo $ajouter_compte;
}

function afficher_supprimer_compte() {
    global vue;
    
    $supprimer_compte = "<div class='section'>" .
    "<h2>Supprimer un compte</h2>" .
    "<p>Remplir le formulaire pour supprimer le compte : </p>" .
    '<form method="post" action="comptes.php">' .
    '<input type="hidden" name="supprimer_compte" value="inutile">' .
    $vue['region'] .
    $vue['identifiant'] .
    $vue['mot_de_passe'] .
    "<input type='submit' value='Supprimer'>" .
    "</form></div>";
    echo $supprimer_compte;
}

// tableau d'éléments HTML
$vue = array();


// HTML de la gestion des adhérents
$vue['numero_adherent'] = "<br />";
$vue['nom'] = "<input type='text' name='nom' maxlength='200'><br />";
$vue['prenom'] = "<input type='text' name='prenom' maxlength='200'><br />";
$vue['cotis_payee'] = "" .
'<select name="cotis_payee">' .
'  <option value="">vide</option>' .
'  <option value="1">1</option>' .
'  <option value="2">2</option>' .
'  <option value="3">3</option>' .
'  <option value="1/2">1/2</option>' .
'  <option value="3/2">3/2</option>' .
'</select><br />';
$vue['date_paiement'] = "<input type='text' name='date_paiement' " .
  "maxlength='10'><br />";
$vue['p_ou_rien'] = "" .
'<select name="p_ou_rien">' .
'  <option value="">vide</option>' .
'  <option value="p">P</option>' .
'</select><br />';
$vue['adhesion'] = "" .
'<select name="adhesion">' .
'  <option value="">vide</option>' .
'  <option value="1">1</option>' .
'  <option value="2">2</option>' .
'  <option value="3">3</option>' .
'  <option value="1/2">1/2</option>' .
'  <option value="3/2">3/2</option>' .
'</select><br />';
$vue['adresse_1'] = "<input type='text' name='adresse_1' " .
  "maxlength='200'><br />";
$vue['adresse_2'] = "<input type='text' name='adresse_2' " .
  "maxlength='200'><br />";
$vue['code_postal'] = "<input type='text' name='code_postal' " .
  "maxlength='5'><br />";
$vue['commune'] = "<input type='text' name='commune' maxlength='200'><br />";
$vue['ad'] = "" .
'<select name="ad">' .
'  <option value="">vide</option>' .
'  <option value="AD">AD</option>' .
'  <option value="AD-RSI">AD-RSI</option>' .
'  <option value="AD-RT">AD-RT</option>' .
'  <option value="AD-ARS">AD-ARS</option>' .
'</select><br />';
$vue['profession'] = "" .
'<select name="profession">' .
'  <option value="">vide</option>' .
'  <option value="MC">MC</option>' .
'  <option value="CDC">CDC</option>' .
'  <option value="PHC">PHC</option>' .
'  <option value="MCCS">MCCS</option>' .
'  <option value="CDCCS">CDCCS</option>' .
'  <option value="PHCCS">PHCCS</option>' .
'  <option value="MCRA">MCRA</option>' .
'  <option value="MCR">MCR</option>' .
'</select><br />';
$vue['region'] = "" .
'<select name="region">' .
'  <option value="">vide</option>' .
'  <option value="Alsace-Moselle">Alsace-Moselle</option>' .
'  <option value="Aquitaine">Aquitaine</option>' .
'  <option value="Auvergne">Auvergne</option>' .
'  <option value="Bourgogne">Bourgogne</option>' .
'  <option value="Bretagne">Bretagne</option>' .
'  <option value="Centre">Centre</option>' .
'  <option value="Nord-Est">Nord-Est</option>' .
'  <option value="Midi-Pyrenees">Midi-Pyrenees</option>' .
'  <option value="Languedoc">Languedoc</option>' .
'  <option value="Centre-Ouest">Centre-Ouest</option>' .
'  <option value="Nord-Picardie">Nord-Picardie</option>' .
'  <option value="Normandie">Normandie</option>' .
'  <option value="Ile-de-France">Ile-de-France</option>' .
'  <option value="Pays-de-la-Loire">Pays-de-la-Loire</option>' .
'  <option value="Paca">Paca</option>' .
'  <option value="Rhone-Alpes">Rhone-Alpes</option>' .
'  <option value="Antilles">Antilles</option>' .
'  <option value="Reunion">Reunion</option>' .
'  <option value="RSI">RSI</option>' .
'  <option value="TN">TN</option>' .
'</select><br />';
$vue['echelon'] = "<input type='text' name='echelon' maxlength='200'><br />";
$vue['bureau_nat'] = "" .
'<select name="bureau_nat">' .
'  <option value="">vide</option>' .
'  <option value="1">1</option>' .
'  <option value="2">2</option>' .
'</select><br />';
$vue['comite_nat'] = "" .
'<select name="comite_nat">' .
'  <option value="">vide</option>' .
'  <option value="1">1</option>' .
'  <option value="2">2</option>' .
'</select><br />';
$vue['tel_port'] = "<input type='text' name='tel_port' maxlength='10'><br />";
$vue['tel_prof'] = "<input type='text' name='tel_prof' maxlength='10'><br />";
$vue['tel_dom'] = "<input type='text' name='tel_dom' maxlength='10'><br />";
$vue['fonc_nat'] = "" .
'<select name="fonc_nat">' .
'  <option value="">vide</option>' .
'  <option value="PN">PN</option>' .
'  <option value="SN">SN</option>' .
'  <option value="TN">TN</option>' .
'  <option value="M">M</option>' .
'  <option value="SNA">SNA</option>' .
'  <option value="TNA">TNA</option>' .
'  <option value="VPN">VPN</option>' .
'  <option value="PH">PH</option>' .
'  <option value="TH">TH</option>' .
'</select><br />';
$vue['fonc_nat_irp'] = "" .
'<select name="fonc_nat_irp">' .
'  <option value="">vide</option>' .
'  <option value="DS">DS</option>' .
'  <option value="CCE-T">CCE-T</option>' .
'  <option value="CCE-S">CCE-S</option>' .
'  <option value="RS">RS</option>' .
'  <option value="CE-SEC">CE-SEC</option>' .
'  <option value="CE-TR">CE-TR</option>' .
'</select><br />';
$vue['fonc_reg'] = "" .
'<select name="fonc_reg">' .
'  <option value="">vide</option>' .
'  <option value="P">P</option>' .
'  <option value="S">S</option>' .
'  <option value="T">T</option>' .
'  <option value="M">M</option>' .
'  <option value="SA">SA</option>' .
'  <option value="TA">TA</option>' .
'  <option value="VP">VP</option>' .
'  <option value="PH">PH</option>' .
'</select><br />';
$vue['fonc_reg_irp'] = "" .
'<select name="fonc_reg_irp">' .
'  <option value="">vide</option>' .
'  <option value="DS">DS</option>' .
'  <option value="DP-T">DP-T</option>' .
'  <option value="CD-T">CD-T</option>' .
'  <option value="DP-S">DP-S</option>' .
'  <option value="CE-S">CE-S</option>' .
'  <option value="RS">RS</option>' .
'  <option value="CE-SEC">CE-SEC</option>' .
'  <option value="CE-TR">CE-TR</option>' .
'</select><br />';
$vue['mail_priv'] = "<input type='text' name='mail_priv' " .
  "maxlength='200'><br />";
$vue['mail_prof'] = "<input type='text' name='mail_prof' " .
  "maxlength='200'><br />";
$vue['remarque_r'] = "<input type='text' name='remarque_r' " .
  "maxlength='200'><br />";
$vue['remarque_n'] = "<input type='text' name='remarque_n' " .
  "maxlength='200'><br />";
$vue['chsc_pc_r'] = "" .
'<select name="chsc_pc_r">' .
'  <option value="">vide</option>' .
'  <option value="S">S</option>' .
'  <option value="T">T</option>' .
'  <option value="t">t</option>' .
'  <option value="s">s</option>' .
'  <option value="RS">RS</option>' .
'</select><br />';
$vue['chsc_pc_n'] = "" .
'<select name="chsc_pc_n">' .
'  <option value="">vide</option>' .
'  <option value="S">S</option>' .
'  <option value="T">T</option>' .
'  <option value="t">t</option>' .
'  <option value="s">s</option>' .
'  <option value="RS">RS</option>' .
'</select><br />';
$vue['com_bud'] = "" .
'<select name="com_bud">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_com'] = "" .
'<select name="com_com">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_cond'] = "" .
'<select name="com_cond">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_ce'] = "" .
'<select name="com_ce">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_dent'] = "" .
'<select name="com_dent">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_ffass'] = "" .
'<select name="com_ffass">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_pharma'] = "" .
'<select name="com_pharma">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['com_ret'] = "" .
'<select name="com_ret">' .
'  <option value="">vide</option>' .
'  <option value="M">M</option>' .
'  <option value="R">R</option>' .
'</select><br />';
$vue['naissance'] = "<input type='text' name='naissance' maxlength='10'><br />";
$vue['entree'] = "<input type='text' name='entree' maxlength='10'><br />";
$vue['abcd'] = "" .
'<select name="abcd">' .
'  <option value="">vide</option>' .
'  <option value="A">A</option>' .
'  <option value="B">B</option>' .
'  <option value="C">C</option>' .
'  <option value="D">D</option>' .
'  <option value="Z">Z</option>' .
'</select><br />';
$vue['c1'] = "<input type='text' name='c1' maxlength='200'><br />";
$vue['c2'] = "<input type='text' name='c2' maxlength='200'><br />";
$vue['c3'] = "<input type='text' name='c3' maxlength='200'><br />";
$vue['c4'] = "<input type='text' name='c4' maxlength='200'><br />";
$vue['c5'] = "<input type='text' name='c5' maxlength='200'><br />";
$vue['c6'] = "<input type='text' name='c6' maxlength='200'><br />";
$vue['c7'] = "<input type='text' name='c7' maxlength='200'><br />";
$vue['c8'] = "<input type='text' name='c8' maxlength='200'><br />";
$vue['c9'] = "<input type='text' name='c9' maxlength='200'><br />";

// HTML de la gestion des comptes
// On utilise aussi la region définie pour la gestion des adhérents
$vue['identifiant'] = "<input type='text' name='identifiant' " .
"maxlength='32'><br />";
$vue['mot_de_passe'] = "<input type='text' name='mot_de_passe' " .
"maxlength='32'><br />";






