<?php

require_once('donnees.php');
require_once("member.php");

function afficher_header($title)
{
    $header = '';

    $header .= '<!DOCTYPE html>';
    $header .= '<html>';
    $header .= '<head>';
    $header .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
    $header .= '<link rel="stylesheet" href="style.css" />';
    $header .= "<title>$title</title>";
    $header .= '</head>';
    $header .= '<body>';

    echo $header;
}

function afficher_footer()
{
    $footer = "";

    $footer .= "</body>";
    $footer .= "</html>";

    echo $footer;
}

// affiche une invitation à importer un fichier Excel
function afficher_import_excel()
{
    $import_excel = "";

    $import_excel .= "<div class='standalone'>";
    $import_excel .= "<p>Veuillez choisir le fichier à importer :</p>";
    $import_excel .= '<form method="post" action="import.php" enctype="multipart/form-data">';
    $import_excel .= "<input type='file' name='fichier_excel' /><br />";
    $import_excel .= "<input type='submit' value='Importer le fichier'>";
    $import_excel .= "</form>";
    $import_excel .= "</div>";

    echo $import_excel;
}

// Afficher la barre de navigation
function afficher_navigation()
{
    $nav = "";

    $nav .= '<div id="nav">';

    $nav .= '<form action="liste_adherents.php" method="get">';
    $nav .= '<input type="submit" value="Liste des adhérents">';
    $nav .= '</form>';

    $nav .= '<form action="ajouter_adherent.php" method="get">';
    $nav .= '<input type="submit" value="Ajouter un adhérent">';
    $nav .= '</form>';

    if (is_priviledged() == true)
    {
        $nav .= '<form action="import.php" method="get">';
        $nav .= '<input type="submit" value="Import d\'un fichier Excel">';
        $nav .= '</form>';
    }

    $nav .= '<form action="export.php" method="get">';
    $nav .= '<input type="submit" value="Exporter au format Excel">';
    $nav .= '</form>';

    if (is_priviledged() == true)
    {
        $nav .= '<form action="national.php" method="get">';
        $nav .= '<input type="submit" value="Gestion nationale">';
        $nav .= '</form>';

        $nav .= '<form action="comptes.php" method="get">';
        $nav .= '<input type="submit" value="Gestion des comptes">';
        $nav .= '</form>';
    }

    $nav .= '<form action="index.php" method="post">';
    $nav .= '<input type="hidden" name="deconnexion" value="useless">';
    $nav .= '<input type="submit" value="Déconnexion">';
    $nav .= '</form>';

    $nav .= '</div>';

    echo $nav;
}

// affiche la liste des adhérents :
//   - $page : page vers laquelle envoyer les informations
//   - $type : "afficher" ou "supprimer"
function afficher_liste_adherents($page, $type)
{
    global $colonnes;

    $submit = "";
    if ($type == "afficher")
    {
        $submit .= '<input type="hidden" name ="afficher" value="inutile">';
        $submit .= '<input type="submit" value="Afficher">';
    }
    elseif ($type == "supprimer")
    {
        $submit .= '<input type="hidden" name ="supprimer" value="inutile">';
        $submit .= '<input type="submit" value="Supprimer">';
    }
    else
    {
        return;
    }

    $entry = "";
    $entry .= "<div class='section'>";
    $entry .= "<h2>Liste des adhérents</h2>";

    $id_list = member_get_list();
    foreach ($id_list as $id)
    {
        $member = member_get($id);

        // Skip member that don't belong to the region
        if (($member['region'] != $_SESSION['region']) && (is_priviledged() == false))
        {
            continue;
        }

        $entry .= "<form action='$page' method='post'>";
        foreach($colonnes as $colonne)
        {
            if (array_key_exists($colonne, $member) == false)
            {
                continue;
            }

            $entry .= "<input type='hidden' name='$colonne' value='{$member[$colonne]}'>";
            if (($colonne == "numero_adherent") ||
                ($colonne == "nom") ||
                ($colonne == "prenom"))
            {
                $entry .= "$colonne : {$member[$colonne]}<br />";
            }
        }
        $entry .= $submit;
        $entry .= '</form>';
    }

    $entry .= '</div>';

    echo $entry;
}

// affichage HTML d'un tableau en liste non-ordonnée
function vue_tableau($tableau)
{
    $vue = "<ul>";
    foreach(array_keys($tableau) as $col)
    {
        $vue .= "<li>$col : {$tableau[$col]}</li>";
    }
    $vue .= "</ul>";
    return $vue;
}

// vue HTML d'un adhérent comme liste non-ordonnée
function vue_adherent($numero_adherent)
{
    $adherent = member_get($numero_adherent);
    $vue_adherent = vue_tableau($adherent);
    return $vue_adherent;
}

// affiche le bouton pour supprimer une colonne
function afficher_supprimer_colonne($colonne)
{
    $entry = "";

    $entry .= "<div class='section'>";

    $entry .= "<h2>Supprimer la colonne $colonne</h2>";
    $entry .= "<p>Cliquez pour supprimer la colonne $colonne : </p>";

    $entry .= '<form method="post" action="national.php">';
    $entry .= "<input type='hidden' name='supprimer_colonne' value='$colonne'>";
    $entry .= "<input type='submit' value='Supprimer'>";
    $entry .= "</form>";

    $entry .= "</div>";

    echo $entry;
}

// affiche le bouton de basculement des cotisations
function afficher_transition_annuelle()
{
    $entry = "";

    $entry .= '<div class="section">';
    $entry .= '<h2>Basculement des cotisations</h2>';
    $entry .= "<p><strong>ATTENTION</strong> : cette action est à réaliser avec précaution. Elle a pour but d'effectuer la transition annuelle des comptes.</p>";
    $entry .= "<p>Si vous cliquez sur le bouton, les données de l'année précédente seront effacées et remplacées par celles de l'année courante.</p>";

    $entry .= '<form action="national.php" method="post">';
    $entry .= '<input type="hidden" name="transition" value="inutile">';
    $entry .= '<input type="submit" value="Effectuer la transition annuelle">';
    $entry .= '</form>';

    $entry .= '</div>';

    echo $entry;
}

// Afficher la liste des comptes
function afficher_liste_comptes()
{
    $user_list = account_get_list();

    $entry = "";
    $entry .= "<div class='section'>";
    $entry .= "<h2>Liste des comptes</h2>";

    foreach($user_list as $user)
    {
        $account = account_get($user);

        $entry .= "region : {$account['region']}<br />";
        $entry .= "identifiant : {$account['user']}<br />";
        $entry .= "mot de passe : {$account['password']}<br /><br />";
    }

    $entry .= '</div>';

    echo $entry;
}

function afficher_ajouter_compte()
{
    global $vue;

    $entry = "";

    $entry .= "<div class='section'>";
    $entry .= "<h2>Ajouter un compte</h2>";
    $entry .= "<p>Remplir le formulaire pour ajouter le compte : </p>";
    $entry .= '<form method="post" action="comptes.php">';
    $entry .= '<input type="hidden" name="ajouter_compte" value="inutile">';
    $entry .= "region : {$vue['region_compte']}";
    $entry .= "identifiant : {$vue['identifiant']}";
    $entry .= "mot de passe : {$vue['mot_de_passe']}";
    $entry .= "<input type='submit' value='Ajouter'>";
    $entry .= "</form>";
    $entry .= "</div>";

    echo $entry;
}

function afficher_supprimer_compte()
{
    global $vue;

    $entry = "";

    $entry .= "<div class='section'>";
    $entry .= "<h2>Supprimer un compte</h2>";
    $entry .= "<p>Remplir le formulaire pour supprimer le compte : </p>";
    $entry .= '<form method="post" action="comptes.php">';
    $entry .= '<input type="hidden" name="supprimer_compte" value="inutile">';
    $entry .= "identifiant : {$vue['identifiant']}";
    $entry .= "<input type='submit' value='Supprimer'>";
    $entry .= "</form>";
    $entry .= "</div>";

    echo $entry;
}

// tableau d'éléments HTML
$vue = array();


// HTML de la gestion des adhérents
$vue['numero_adherent'] = "<br />";
$vue['nom'] = "<input type='text' name='nom' maxlength='200'><br />";
$vue['prenom'] = "<input type='text' name='prenom' maxlength='200'><br />";

$vue['cotis_payee'] = "";
$vue['cotis_payee'] .= '<select name="cotis_payee">';
$vue['cotis_payee'] .= '<option value="">vide</option>';
$vue['cotis_payee'] .= '<option value="1">1</option>';
$vue['cotis_payee'] .= '<option value="2">2</option>';
$vue['cotis_payee'] .= '<option value="3">3</option>';
$vue['cotis_payee'] .= '<option value="1/2">1/2</option>';
$vue['cotis_payee'] .= '<option value="3/2">3/2</option>';
$vue['cotis_payee'] .= '</select><br />';

$vue['date_paiement'] = "<input type='text' name='date_paiement' maxlength='10'><br />";

$vue['p_ou_rien'] = "";
$vue['p_ou_rien'] .= '<select name="p_ou_rien">';
$vue['p_ou_rien'] .= '<option value="">vide</option>';
$vue['p_ou_rien'] .= '<option value="p">p</option>';
$vue['p_ou_rien'] .= '</select><br />';

$vue['adhesion'] = "";
$vue['adhesion'] .= '<select name="adhesion">';
$vue['adhesion'] .= '<option value="">vide</option>';
$vue['adhesion'] .= '<option value="1">1</option>';
$vue['adhesion'] .= '<option value="2">2</option>';
$vue['adhesion'] .= '<option value="3">3</option>';
$vue['adhesion'] .= '<option value="1/2">1/2</option>';
$vue['adhesion'] .= '<option value="3/2">3/2</option>';
$vue['adhesion'] .= '</select><br />';

$vue['adresse_1'] = "<input type='text' name='adresse_1' maxlength='200'><br />";
$vue['adresse_2'] = "<input type='text' name='adresse_2' maxlength='200'><br />";
$vue['code_postal'] = "<input type='text' name='code_postal' maxlength='5'><br />";
$vue['commune'] = "<input type='text' name='commune' maxlength='200'><br />";

$vue['ad'] = "";
$vue['ad'] .= '<select name="ad">';
$vue['ad'] .= '<option value="">vide</option>';
$vue['ad'] .= '<option value="AD">AD</option>';
$vue['ad'] .= '<option value="AD-RSI">AD-RSI</option>';
$vue['ad'] .= '<option value="AD-RT">AD-RT</option>';
$vue['ad'] .= '<option value="AD-ARS">AD-ARS</option>';
$vue['ad'] .= '</select><br />';

$vue['profession'] = "";
$vue['profession'] .= '<select name="profession">';
$vue['profession'] .= '<option value="">vide</option>';
$vue['profession'] .= '<option value="MC">MC</option>';
$vue['profession'] .= '<option value="CDC">CDC</option>';
$vue['profession'] .= '<option value="PHC">PHC</option>';
$vue['profession'] .= '<option value="MCCS">MCCS</option>';
$vue['profession'] .= '<option value="CDCCS">CDCCS</option>';
$vue['profession'] .= '<option value="PHCCS">PHCCS</option>';
$vue['profession'] .= '<option value="MCRA">MCRA</option>';
$vue['profession'] .= '<option value="MCR">MCR</option>';
$vue['profession'] .= '</select><br />';

$vue['region'] = "";
$vue['region'] .= '<select name="region">';
$vue['region'] .= '<option value="">vide</option>';
$vue['region'] .= '<option value="Alsace-Moselle">Alsace-Moselle</option>';
$vue['region'] .= '<option value="Aquitaine">Aquitaine</option>';
$vue['region'] .= '<option value="Auvergne">Auvergne</option>';
$vue['region'] .= '<option value="Bourgogne">Bourgogne</option>';
$vue['region'] .= '<option value="Bretagne">Bretagne</option>';
$vue['region'] .= '<option value="Centre">Centre</option>';
$vue['region'] .= '<option value="Nord-Est">Nord-Est</option>';
$vue['region'] .= '<option value="Midi-Pyrenees">Midi-Pyrenees</option>';
$vue['region'] .= '<option value="Languedoc">Languedoc</option>';
$vue['region'] .= '<option value="Centre-Ouest">Centre-Ouest</option>';
$vue['region'] .= '<option value="Nord-Picardie">Nord-Picardie</option>';
$vue['region'] .= '<option value="Normandie">Normandie</option>';
$vue['region'] .= '<option value="Ile-de-France">Ile-de-France</option>';
$vue['region'] .= '<option value="Pays-de-la-Loire">Pays-de-la-Loire</option>';
$vue['region'] .= '<option value="Paca">Paca</option>';
$vue['region'] .= '<option value="Rhone-Alpes">Rhone-Alpes</option>';
$vue['region'] .= '<option value="Antilles">Antilles</option>';
$vue['region'] .= '<option value="Reunion">Reunion</option>';
$vue['region'] .= '<option value="RSI">RSI</option>';
$vue['region'] .= '<option value="TN">TN</option>';
$vue['region'] .= '</select><br />';

$vue['region_compte'] = "";
$vue['region_compte'] .= '<select name="region">';
$vue['region_compte'] .= '<option value="">vide</option>';
$vue['region_compte'] .= '<option value="Alsace-Moselle">Alsace-Moselle</option>';
$vue['region_compte'] .= '<option value="Aquitaine">Aquitaine</option>';
$vue['region_compte'] .= '<option value="Auvergne">Auvergne</option>';
$vue['region_compte'] .= '<option value="Bourgogne">Bourgogne</option>';
$vue['region_compte'] .= '<option value="Bretagne">Bretagne</option>';
$vue['region_compte'] .= '<option value="Centre">Centre</option>';
$vue['region_compte'] .= '<option value="Nord-Est">Nord-Est</option>';
$vue['region_compte'] .= '<option value="Midi-Pyrenees">Midi-Pyrenees</option>';
$vue['region_compte'] .= '<option value="Languedoc">Languedoc</option>';
$vue['region_compte'] .= '<option value="Centre-Ouest">Centre-Ouest</option>';
$vue['region_compte'] .= '<option value="Nord-Picardie">Nord-Picardie</option>';
$vue['region_compte'] .= '<option value="Normandie">Normandie</option>';
$vue['region_compte'] .= '<option value="Ile-de-France">Ile-de-France</option>';
$vue['region_compte'] .= '<option value="Pays-de-la-Loire">Pays-de-la-Loire</option>';
$vue['region_compte'] .= '<option value="Paca">Paca</option>';
$vue['region_compte'] .= '<option value="Rhone-Alpes">Rhone-Alpes</option>';
$vue['region_compte'] .= '<option value="Antilles">Antilles</option>';
$vue['region_compte'] .= '<option value="Reunion">Reunion</option>';
$vue['region_compte'] .= '<option value="RSI">RSI</option>';
$vue['region_compte'] .= '<option value="TN">TN</option>';
$vue['region_compte'] .= '<option value="National">National</option>';
$vue['region_compte'] .= '</select><br />';

$vue['echelon'] = "<input type='text' name='echelon' maxlength='200'><br />";

$vue['bureau_nat'] = "";
$vue['bureau_nat'] .= '<select name="bureau_nat">';
$vue['bureau_nat'] .= '<option value="">vide</option>';
$vue['bureau_nat'] .= '<option value="1">1</option>';
$vue['bureau_nat'] .= '<option value="2">2</option>';
$vue['bureau_nat'] .= '</select><br />';

$vue['comite_nat'] = "";
$vue['comite_nat'] .= '<select name="comite_nat">';
$vue['comite_nat'] .= '<option value="">vide</option>';
$vue['comite_nat'] .= '<option value="1">1</option>';
$vue['comite_nat'] .= '<option value="2">2</option>';
$vue['comite_nat'] .= '</select><br />';

$vue['tel_port'] = "<input type='text' name='tel_port' maxlength='10'><br />";
$vue['tel_prof'] = "<input type='text' name='tel_prof' maxlength='10'><br />";
$vue['tel_dom'] = "<input type='text' name='tel_dom' maxlength='10'><br />";

$vue['fonc_nat'] = "";
$vue['fonc_nat'] .= '<select name="fonc_nat">';
$vue['fonc_nat'] .= '<option value="">vide</option>';
$vue['fonc_nat'] .= '<option value="PN">PN</option>';
$vue['fonc_nat'] .= '<option value="SN">SN</option>';
$vue['fonc_nat'] .= '<option value="TN">TN</option>';
$vue['fonc_nat'] .= '<option value="M">M</option>';
$vue['fonc_nat'] .= '<option value="SNA">SNA</option>';
$vue['fonc_nat'] .= '<option value="TNA">TNA</option>';
$vue['fonc_nat'] .= '<option value="VPN">VPN</option>';
$vue['fonc_nat'] .= '<option value="PH">PH</option>';
$vue['fonc_nat'] .= '<option value="TH">TH</option>';
$vue['fonc_nat'] .= '</select><br />';

$vue['fonc_nat_irp'] = "";
$vue['fonc_nat_irp'] .= '<select name="fonc_nat_irp">';
$vue['fonc_nat_irp'] .= '<option value="">vide</option>';
$vue['fonc_nat_irp'] .= '<option value="DS">DS</option>';
$vue['fonc_nat_irp'] .= '<option value="CCE-T">CCE-T</option>';
$vue['fonc_nat_irp'] .= '<option value="CCE-S">CCE-S</option>';
$vue['fonc_nat_irp'] .= '<option value="RS">RS</option>';
$vue['fonc_nat_irp'] .= '<option value="CE-SEC">CE-SEC</option>';
$vue['fonc_nat_irp'] .= '<option value="CE-TR">CE-TR</option>';
$vue['fonc_nat_irp'] .= '</select><br />';

$vue['fonc_reg'] = "";
$vue['fonc_reg'] .= '<select name="fonc_reg">';
$vue['fonc_reg'] .= '<option value="">vide</option>';
$vue['fonc_reg'] .= '<option value="P">P</option>';
$vue['fonc_reg'] .= '<option value="S">S</option>';
$vue['fonc_reg'] .= '<option value="T">T</option>';
$vue['fonc_reg'] .= '<option value="M">M</option>';
$vue['fonc_reg'] .= '<option value="SA">SA</option>';
$vue['fonc_reg'] .= '<option value="TA">TA</option>';
$vue['fonc_reg'] .= '<option value="VP">VP</option>';
$vue['fonc_reg'] .= '<option value="PH">PH</option>';
$vue['fonc_reg'] .= '</select><br />';

$vue['fonc_reg_irp'] = "";
$vue['fonc_reg_irp'] .= '<select name="fonc_reg_irp">';
$vue['fonc_reg_irp'] .= '<option value="">vide</option>';
$vue['fonc_reg_irp'] .= '<option value="DS">DS</option>';
$vue['fonc_reg_irp'] .= '<option value="DP-T">DP-T</option>';
$vue['fonc_reg_irp'] .= '<option value="CD-T">CD-T</option>';
$vue['fonc_reg_irp'] .= '<option value="DP-S">DP-S</option>';
$vue['fonc_reg_irp'] .= '<option value="CE-S">CE-S</option>';
$vue['fonc_reg_irp'] .= '<option value="RS">RS</option>';
$vue['fonc_reg_irp'] .= '<option value="CE-SEC">CE-SEC</option>';
$vue['fonc_reg_irp'] .= '<option value="CE-TR">CE-TR</option>';
$vue['fonc_reg_irp'] .= '</select><br />';

$vue['mail_priv'] = "<input type='text' name='mail_priv' maxlength='200'><br />";
$vue['mail_prof'] = "<input type='text' name='mail_prof' maxlength='200'><br />";
$vue['remarque_r'] = "<input type='text' name='remarque_r' maxlength='200'><br />";
$vue['remarque_n'] = "<input type='text' name='remarque_n' maxlength='200'><br />";

$vue['chsc_pc_r'] = "";
$vue['chsc_pc_r'] .= '<select name="chsc_pc_r">';
$vue['chsc_pc_r'] .= '<option value="">vide</option>';
$vue['chsc_pc_r'] .= '<option value="S">S</option>';
$vue['chsc_pc_r'] .= '<option value="T">T</option>';
$vue['chsc_pc_r'] .= '<option value="t">t</option>';
$vue['chsc_pc_r'] .= '<option value="s">s</option>';
$vue['chsc_pc_r'] .= '<option value="RS">RS</option>';
$vue['chsc_pc_r'] .= '</select><br />';

$vue['chsc_pc_n'] = "";
$vue['chsc_pc_n'] .= '<select name="chsc_pc_n">';
$vue['chsc_pc_n'] .= '<option value="">vide</option>';
$vue['chsc_pc_n'] .= '<option value="S">S</option>';
$vue['chsc_pc_n'] .= '<option value="T">T</option>';
$vue['chsc_pc_n'] .= '<option value="t">t</option>';
$vue['chsc_pc_n'] .= '<option value="s">s</option>';
$vue['chsc_pc_n'] .= '<option value="RS">RS</option>';
$vue['chsc_pc_n'] .= '</select><br />';

$vue['com_bud'] = "";
$vue['com_bud'] .= '<select name="com_bud">';
$vue['com_bud'] .= '<option value="">vide</option>';
$vue['com_bud'] .= '<option value="M">M</option>';
$vue['com_bud'] .= '<option value="R">R</option>';
$vue['com_bud'] .= '</select><br />';

$vue['com_com'] = "";
$vue['com_com'] .= '<select name="com_com">';
$vue['com_com'] .= '<option value="">vide</option>';
$vue['com_com'] .= '<option value="M">M</option>';
$vue['com_com'] .= '<option value="R">R</option>';
$vue['com_com'] .= '</select><br />';

$vue['com_cond'] = "";
$vue['com_cond'] .= '<select name="com_cond">';
$vue['com_cond'] .= '<option value="">vide</option>';
$vue['com_cond'] .= '<option value="M">M</option>';
$vue['com_cond'] .= '<option value="R">R</option>';
$vue['com_cond'] .= '</select><br />';

$vue['com_ce'] = "";
$vue['com_ce'] .= '<select name="com_ce">';
$vue['com_ce'] .= '<option value="">vide</option>';
$vue['com_ce'] .= '<option value="M">M</option>';
$vue['com_ce'] .= '<option value="R">R</option>';
$vue['com_ce'] .= '</select><br />';

$vue['com_dent'] = "";
$vue['com_dent'] .= '<select name="com_dent">';
$vue['com_dent'] .= '<option value="">vide</option>';
$vue['com_dent'] .= '<option value="M">M</option>';
$vue['com_dent'] .= '<option value="R">R</option>';
$vue['com_dent'] .= '</select><br />';

$vue['com_ffass'] = "";
$vue['com_ffass'] .= '<select name="com_ffass">';
$vue['com_ffass'] .= '<option value="">vide</option>';
$vue['com_ffass'] .= '<option value="M">M</option>';
$vue['com_ffass'] .= '<option value="R">R</option>';
$vue['com_ffass'] .= '</select><br />';

$vue['com_pharma'] = "";
$vue['com_pharma'] .= '<select name="com_pharma">';
$vue['com_pharma'] .= '<option value="">vide</option>';
$vue['com_pharma'] .= '<option value="M">M</option>';
$vue['com_pharma'] .= '<option value="R">R</option>';
$vue['com_pharma'] .= '</select><br />';

$vue['com_ret'] = "";
$vue['com_ret'] .= '<select name="com_ret">';
$vue['com_ret'] .= '<option value="">vide</option>';
$vue['com_ret'] .= '<option value="M">M</option>';
$vue['com_ret'] .= '<option value="R">R</option>';
$vue['com_ret'] .= '</select><br />';

$vue['naissance'] = "<input type='text' name='naissance' maxlength='10'><br />";
$vue['entree'] = "<input type='text' name='entree' maxlength='10'><br />";

$vue['abcd'] = "";
$vue['abcd'] .= '<select name="abcd">';
$vue['abcd'] .= '<option value="">vide</option>';
$vue['abcd'] .= '<option value="A">A</option>';
$vue['abcd'] .= '<option value="B">B</option>';
$vue['abcd'] .= '<option value="C">C</option>';
$vue['abcd'] .= '<option value="D">D</option>';
$vue['abcd'] .= '<option value="Z">Z</option>';
$vue['abcd'] .= '</select><br />';

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
$vue['identifiant'] = "<input type='text' name='identifiant' maxlength='32'><br />";
$vue['mot_de_passe'] = "<input type='text' name='mot_de_passe' maxlength='32'><br />";
