<?php

require_once "donnees.php";
require_once "member.php";

function display_header($title)
{
    $header = "";

    $header .= "<!DOCTYPE html>";
    $header .= "<html>";
    $header .= "<head>";
    $header .= "<meta http-equiv='content-type' content='text/html; charset=utf-8' />";
    $header .= "<link rel='stylesheet' href='style.css' />";
    $header .= "<title>$title</title>";
    $header .= "</head>";
    $header .= "<body>";

    echo $header;
}

function display_footer()
{
    $footer = "";

    $footer .= "</body>";
    $footer .= "</html>";

    echo $footer;
}

function display_navigation()
{
    $nav = "";

    $nav .= "<div id='nav'><table><tr>";

    $nav .= "<td><form action='liste_adherents.php' method='get'>";
    $nav .= "<input type='submit' value='Lister les adhérents'>";
    $nav .= "</form></td>";

    $nav .= "<td><form action='ajouter_adherent.php' method='get'>";
    $nav .= "<input type='submit' value='Ajouter un adhérent'>";
    $nav .= "</form></td>";

    if (is_privileged() == true)
    {
        $nav .= "<td><form action='import.php' method='get'>";
        $nav .= "<input type='submit' value='Importer au format Excel'>";
        $nav .= "</form></td>";
    }

    $nav .= "<td><form action='export.php' method='get'>";
    $nav .= "<input type='submit' value='Exporter au format Excel'>";
    $nav .= "</form></td>";

    if (is_privileged() == true)
    {
        $nav .= "<td><form action='national.php' method='get'>";
        $nav .= "<input type='submit' value='Gestion nationale'>";
        $nav .= "</form></td>";

        $nav .= "<td><form action='comptes.php' method='get'>";
        $nav .= "<input type='submit' value='Gestion des comptes'>";
        $nav .= "</form></td>";
    }

    $nav .= "<td><form action='index.php' method='post'>";
    $nav .= "<input type='hidden' name='deconnexion'>";
    $nav .= "<input type='submit' value='Déconnexion'>";
    $nav .= "</form></td>";

    $nav .= "</tr></table></div>";

    echo $nav;
}

function display_member_list($page, $type)
{
    global $colonnes;

    $submit = "";
    if ($type == "afficher")
    {
        $submit .= "<input type='hidden' name ='afficher'>";
        $submit .= "<input type='submit' value='Afficher'>";
    }
    elseif ($type == "supprimer")
    {
        $submit .= "<input type='hidden' name ='supprimer'>";
        $submit .= "<input type='submit' value='Supprimer'>";
    }
    else
    {
        return;
    }

    $entry = "";
    $entry .= "<div class='section'>";
    $entry .= "<h1>Liste des adhérents</h1>";

    $entry .= "<table>";
    $entry .= "<tr>";
    $entry .= "<th>Numéro d'adhérent</th><th>Nom</th><th>Prénom</th><th>Action</th>";
    $entry .= "</tr>";

    $member_list = member_get_list();
    foreach ($member_list as $member)
    {
        // Skip member that don't belong to the region
        if (($member['region'] != $_SESSION['region']) && (is_privileged() == false))
        {
            continue;
        }

        $entry .= "<tr><td>{$member["numero_adherent"]}</td><td>{$member["nom"]}</td><td>{$member["prenom"]}</td>";

        $entry .= "<td><form action='$page' method='post'>";
        foreach($colonnes as $colonne)
        {
            if (array_key_exists($colonne, $member) == false)
            {
                continue;
            }

            $entry .= "<input type='hidden' name='$colonne' value='{$member[$colonne]}'>";
        }
        $entry .= $submit;
        $entry .= "</form></td></tr>";
    }

    $entry .= "</table>";

    $entry .= "</div>";

    echo $entry;
}

$vue = array();

// HTML for member data
$vue["numero_adherent"] = "";
$vue["nom"] = "<input type='text' name='nom' maxlength='200'>";
$vue["prenom"] = "<input type='text' name='prenom' maxlength='200'>";

$vue["cotis_payee"] = "";
$vue["cotis_payee"] .= "<select name='cotis_payee'>";
$vue["cotis_payee"] .= "<option value=''>vide</option>";
$vue["cotis_payee"] .= "<option value='1'>1</option>";
$vue["cotis_payee"] .= "<option value='2'>2</option>";
$vue["cotis_payee"] .= "<option value='3'>3</option>";
$vue["cotis_payee"] .= "<option value='1/2'>1/2</option>";
$vue["cotis_payee"] .= "<option value='3/2'>3/2</option>";
$vue["cotis_payee"] .= "</select>";

$vue["date_paiement"] = "<input type='text' name='date_paiement' maxlength='10'>";

$vue["p_ou_rien"] = "";
$vue["p_ou_rien"] .= "<select name='p_ou_rien'>";
$vue["p_ou_rien"] .= "<option value=''>vide</option>";
$vue["p_ou_rien"] .= "<option value='p'>p</option>";
$vue["p_ou_rien"] .= "</select>";

$vue["adhesion"] = "";
$vue["adhesion"] .= "<select name='adhesion'>";
$vue["adhesion"] .= "<option value=''>vide</option>";
$vue["adhesion"] .= "<option value='1'>1</option>";
$vue["adhesion"] .= "<option value='2'>2</option>";
$vue["adhesion"] .= "<option value='3'>3</option>";
$vue["adhesion"] .= "<option value='1/2'>1/2</option>";
$vue["adhesion"] .= "<option value='3/2'>3/2</option>";
$vue["adhesion"] .= "</select>";

$vue["adresse_1"] = "<input type='text' name='adresse_1' maxlength='200'>";
$vue["adresse_2"] = "<input type='text' name='adresse_2' maxlength='200'>";
$vue["code_postal"] = "<input type='text' name='code_postal' maxlength='5'>";
$vue["commune"] = "<input type='text' name='commune' maxlength='200'>";

$vue["ad"] = "";
$vue["ad"] .= "<select name='ad'>";
$vue["ad"] .= "<option value=''>vide</option>";
$vue["ad"] .= "<option value='AD'>AD</option>";
$vue["ad"] .= "<option value='AD-RSI'>AD-RSI</option>";
$vue["ad"] .= "<option value='AD-RT'>AD-RT</option>";
$vue["ad"] .= "<option value='AD-ARS'>AD-ARS</option>";
$vue["ad"] .= "</select>";

$vue["profession"] = "";
$vue["profession"] .= "<select name='profession'>";
$vue["profession"] .= "<option value=''>vide</option>";
$vue["profession"] .= "<option value='MC'>MC</option>";
$vue["profession"] .= "<option value='CDC'>CDC</option>";
$vue["profession"] .= "<option value='PHC'>PHC</option>";
$vue["profession"] .= "<option value='MCCS'>MCCS</option>";
$vue["profession"] .= "<option value='CDCCS'>CDCCS</option>";
$vue["profession"] .= "<option value='PHCCS'>PHCCS</option>";
$vue["profession"] .= "<option value='MCRA'>MCRA</option>";
$vue["profession"] .= "<option value='MCR'>MCR</option>";
$vue["profession"] .= "</select>";

$vue["region"] = "";
$vue["region"] .= "<select name='region'>";
$vue["region"] .= "<option value=''>vide</option>";
$vue["region"] .= "<option value='Aura'>Aura</option>";
$vue["region"] .= "<option value='Nouvelle-Aquitaine'>Nouvelle-Aquitaine</option>";
$vue["region"] .= "<option value='Occitanie'>Occitanie</option>";
$vue["region"] .= "<option value='Grand-Est'>Grand-Est</option>";
$vue["region"] .= "<option value='Hauts-de-France'>Hauts-de-France</option>";
$vue["region"] .= "<option value='Alsace-Moselle'>Alsace-Moselle</option>";
$vue["region"] .= "<option value='Aquitaine'>Aquitaine</option>";
$vue["region"] .= "<option value='Auvergne'>Auvergne</option>";
$vue["region"] .= "<option value='Bourgogne'>Bourgogne</option>";
$vue["region"] .= "<option value='Bretagne'>Bretagne</option>";
$vue["region"] .= "<option value='Centre'>Centre</option>";
$vue["region"] .= "<option value='Nord-Est'>Nord-Est</option>";
$vue["region"] .= "<option value='Midi-Pyrenees'>Midi-Pyrenees</option>";
$vue["region"] .= "<option value='Languedoc'>Languedoc</option>";
$vue["region"] .= "<option value='Centre-Ouest'>Centre-Ouest</option>";
$vue["region"] .= "<option value='Nord-Picardie'>Nord-Picardie</option>";
$vue["region"] .= "<option value='Normandie'>Normandie</option>";
$vue["region"] .= "<option value='Ile-de-France'>Ile-de-France</option>";
$vue["region"] .= "<option value='Pays-de-la-Loire'>Pays-de-la-Loire</option>";
$vue["region"] .= "<option value='Paca'>Paca</option>";
$vue["region"] .= "<option value='Rhone-Alpes'>Rhone-Alpes</option>";
$vue["region"] .= "<option value='Antilles'>Antilles</option>";
$vue["region"] .= "<option value='Reunion'>Reunion</option>";
$vue["region"] .= "<option value='RSI'>RSI</option>";
$vue["region"] .= "<option value='TN'>TN</option>";
$vue["region"] .= "</select>";

$vue["region_compte"] = "";
$vue["region_compte"] .= "<select name='region'>";
$vue["region_compte"] .= "<option value=''>vide</option>";
$vue["region_compte"] .= "<option value='Aura'>Aura</option>";
$vue["region_compte"] .= "<option value='Nouvelle-Aquitaine'>Nouvelle-Aquitaine</option>";
$vue["region_compte"] .= "<option value='Occitanie'>Occitanie</option>";
$vue["region_compte"] .= "<option value='Grand-Est'>Grand-Est</option>";
$vue["region_compte"] .= "<option value='Hauts-de-France'>Hauts-de-France</option>";
$vue["region_compte"] .= "<option value='Alsace-Moselle'>Alsace-Moselle</option>";
$vue["region_compte"] .= "<option value='Aquitaine'>Aquitaine</option>";
$vue["region_compte"] .= "<option value='Auvergne'>Auvergne</option>";
$vue["region_compte"] .= "<option value='Bourgogne'>Bourgogne</option>";
$vue["region_compte"] .= "<option value='Bretagne'>Bretagne</option>";
$vue["region_compte"] .= "<option value='Centre'>Centre</option>";
$vue["region_compte"] .= "<option value='Nord-Est'>Nord-Est</option>";
$vue["region_compte"] .= "<option value='Midi-Pyrenees'>Midi-Pyrenees</option>";
$vue["region_compte"] .= "<option value='Languedoc'>Languedoc</option>";
$vue["region_compte"] .= "<option value='Centre-Ouest'>Centre-Ouest</option>";
$vue["region_compte"] .= "<option value='Nord-Picardie'>Nord-Picardie</option>";
$vue["region_compte"] .= "<option value='Normandie'>Normandie</option>";
$vue["region_compte"] .= "<option value='Ile-de-France'>Ile-de-France</option>";
$vue["region_compte"] .= "<option value='Pays-de-la-Loire'>Pays-de-la-Loire</option>";
$vue["region_compte"] .= "<option value='Paca'>Paca</option>";
$vue["region_compte"] .= "<option value='Rhone-Alpes'>Rhone-Alpes</option>";
$vue["region_compte"] .= "<option value='Antilles'>Antilles</option>";
$vue["region_compte"] .= "<option value='Reunion'>Reunion</option>";
$vue["region_compte"] .= "<option value='RSI'>RSI</option>";
$vue["region_compte"] .= "<option value='TN'>TN</option>";
$vue["region_compte"] .= "<option value='National'>National</option>";
$vue["region_compte"] .= "</select>";

$vue["echelon"] = "<input type='text' name='echelon' maxlength='200'>";

$vue["bureau_nat"] = "";
$vue["bureau_nat"] .= "<select name='bureau_nat'>";
$vue["bureau_nat"] .= "<option value=''>vide</option>";
$vue["bureau_nat"] .= "<option value='1'>1</option>";
$vue["bureau_nat"] .= "<option value='2'>2</option>";
$vue["bureau_nat"] .= "</select>";

$vue["comite_nat"] = "";
$vue["comite_nat"] .= "<select name='comite_nat'>";
$vue["comite_nat"] .= "<option value=''>vide</option>";
$vue["comite_nat"] .= "<option value='1'>1</option>";
$vue["comite_nat"] .= "<option value='2'>2</option>";
$vue["comite_nat"] .= "</select>";

$vue["tel_port"] = "<input type='text' name='tel_port' maxlength='10'>";
$vue["tel_prof"] = "<input type='text' name='tel_prof' maxlength='10'>";
$vue["tel_dom"] = "<input type='text' name='tel_dom' maxlength='10'>";

$vue["fonc_nat"] = "";
$vue["fonc_nat"] .= "<select name='fonc_nat'>";
$vue["fonc_nat"] .= "<option value=''>vide</option>";
$vue["fonc_nat"] .= "<option value='PN'>PN</option>";
$vue["fonc_nat"] .= "<option value='SN'>SN</option>";
$vue["fonc_nat"] .= "<option value='TN'>TN</option>";
$vue["fonc_nat"] .= "<option value='M'>M</option>";
$vue["fonc_nat"] .= "<option value='SNA'>SNA</option>";
$vue["fonc_nat"] .= "<option value='TNA'>TNA</option>";
$vue["fonc_nat"] .= "<option value='VPN'>VPN</option>";
$vue["fonc_nat"] .= "<option value='PH'>PH</option>";
$vue["fonc_nat"] .= "<option value='TH'>TH</option>";
$vue["fonc_nat"] .= "</select>";

$vue["fonc_nat_irp"] = "";
$vue["fonc_nat_irp"] .= "<select name='fonc_nat_irp'>";
$vue["fonc_nat_irp"] .= "<option value=''>vide</option>";
$vue["fonc_nat_irp"] .= "<option value='DS'>DS</option>";
$vue["fonc_nat_irp"] .= "<option value='CCE-T'>CCE-T</option>";
$vue["fonc_nat_irp"] .= "<option value='CCE-S'>CCE-S</option>";
$vue["fonc_nat_irp"] .= "<option value='RS'>RS</option>";
$vue["fonc_nat_irp"] .= "<option value='CE-SEC'>CE-SEC</option>";
$vue["fonc_nat_irp"] .= "<option value='CE-TR'>CE-TR</option>";
$vue["fonc_nat_irp"] .= "</select>";

$vue["fonc_reg"] = "";
$vue["fonc_reg"] .= "<select name='fonc_reg'>";
$vue["fonc_reg"] .= "<option value=''>vide</option>";
$vue["fonc_reg"] .= "<option value='P'>P</option>";
$vue["fonc_reg"] .= "<option value='S'>S</option>";
$vue["fonc_reg"] .= "<option value='T'>T</option>";
$vue["fonc_reg"] .= "<option value='M'>M</option>";
$vue["fonc_reg"] .= "<option value='SA'>SA</option>";
$vue["fonc_reg"] .= "<option value='TA'>TA</option>";
$vue["fonc_reg"] .= "<option value='VP'>VP</option>";
$vue["fonc_reg"] .= "<option value='PH'>PH</option>";
$vue["fonc_reg"] .= "</select>";

$vue["fonc_reg_irp"] = "";
$vue["fonc_reg_irp"] .= "<select name='fonc_reg_irp'>";
$vue["fonc_reg_irp"] .= "<option value=''>vide</option>";
$vue["fonc_reg_irp"] .= "<option value='DS'>DS</option>";
$vue["fonc_reg_irp"] .= "<option value='DP-T'>DP-T</option>";
$vue["fonc_reg_irp"] .= "<option value='CD-T'>CD-T</option>";
$vue["fonc_reg_irp"] .= "<option value='DP-S'>DP-S</option>";
$vue["fonc_reg_irp"] .= "<option value='CE-S'>CE-S</option>";
$vue["fonc_reg_irp"] .= "<option value='RS'>RS</option>";
$vue["fonc_reg_irp"] .= "<option value='CE-SEC'>CE-SEC</option>";
$vue["fonc_reg_irp"] .= "<option value='CE-TR'>CE-TR</option>";
$vue["fonc_reg_irp"] .= "</select>";

$vue["mail_priv"] = "<input type='text' name='mail_priv' maxlength='200'>";
$vue["mail_prof"] = "<input type='text' name='mail_prof' maxlength='200'>";
$vue["remarque_r"] = "<input type='text' name='remarque_r' maxlength='200'>";
$vue["remarque_n"] = "<input type='text' name='remarque_n' maxlength='200'>";

$vue["chsc_pc_r"] = "";
$vue["chsc_pc_r"] .= "<select name='chsc_pc_r'>";
$vue["chsc_pc_r"] .= "<option value=''>vide</option>";
$vue["chsc_pc_r"] .= "<option value='S'>S</option>";
$vue["chsc_pc_r"] .= "<option value='T'>T</option>";
$vue["chsc_pc_r"] .= "<option value='t'>t</option>";
$vue["chsc_pc_r"] .= "<option value='s'>s</option>";
$vue["chsc_pc_r"] .= "<option value='RS'>RS</option>";
$vue["chsc_pc_r"] .= "</select>";

$vue["chsc_pc_n"] = "";
$vue["chsc_pc_n"] .= "<select name='chsc_pc_n'>";
$vue["chsc_pc_n"] .= "<option value=''>vide</option>";
$vue["chsc_pc_n"] .= "<option value='S'>S</option>";
$vue["chsc_pc_n"] .= "<option value='T'>T</option>";
$vue["chsc_pc_n"] .= "<option value='t'>t</option>";
$vue["chsc_pc_n"] .= "<option value='s'>s</option>";
$vue["chsc_pc_n"] .= "<option value='RS'>RS</option>";
$vue["chsc_pc_n"] .= "</select>";

$vue["com_bud"] = "";
$vue["com_bud"] .= "<select name='com_bud'>";
$vue["com_bud"] .= "<option value=''>vide</option>";
$vue["com_bud"] .= "<option value='M'>M</option>";
$vue["com_bud"] .= "<option value='R'>R</option>";
$vue["com_bud"] .= "</select>";

$vue["com_com"] = "";
$vue["com_com"] .= "<select name='com_com'>";
$vue["com_com"] .= "<option value=''>vide</option>";
$vue["com_com"] .= "<option value='M'>M</option>";
$vue["com_com"] .= "<option value='R'>R</option>";
$vue["com_com"] .= "</select>";

$vue["com_cond"] = "";
$vue["com_cond"] .= "<select name='com_cond'>";
$vue["com_cond"] .= "<option value=''>vide</option>";
$vue["com_cond"] .= "<option value='M'>M</option>";
$vue["com_cond"] .= "<option value='R'>R</option>";
$vue["com_cond"] .= "</select>";

$vue["com_ce"] = "";
$vue["com_ce"] .= "<select name='com_ce'>";
$vue["com_ce"] .= "<option value=''>vide</option>";
$vue["com_ce"] .= "<option value='M'>M</option>";
$vue["com_ce"] .= "<option value='R'>R</option>";
$vue["com_ce"] .= "</select>";

$vue["com_dent"] = "";
$vue["com_dent"] .= "<select name='com_dent'>";
$vue["com_dent"] .= "<option value=''>vide</option>";
$vue["com_dent"] .= "<option value='M'>M</option>";
$vue["com_dent"] .= "<option value='R'>R</option>";
$vue["com_dent"] .= "</select>";

$vue["com_ffass"] = "";
$vue["com_ffass"] .= "<select name='com_ffass'>";
$vue["com_ffass"] .= "<option value=''>vide</option>";
$vue["com_ffass"] .= "<option value='M'>M</option>";
$vue["com_ffass"] .= "<option value='R'>R</option>";
$vue["com_ffass"] .= "</select>";

$vue["com_pharma"] = "";
$vue["com_pharma"] .= "<select name='com_pharma'>";
$vue["com_pharma"] .= "<option value=''>vide</option>";
$vue["com_pharma"] .= "<option value='M'>M</option>";
$vue["com_pharma"] .= "<option value='R'>R</option>";
$vue["com_pharma"] .= "</select>";

$vue["com_ret"] = "";
$vue["com_ret"] .= "<select name='com_ret'>";
$vue["com_ret"] .= "<option value=''>vide</option>";
$vue["com_ret"] .= "<option value='M'>M</option>";
$vue["com_ret"] .= "<option value='R'>R</option>";
$vue["com_ret"] .= "</select>";

$vue["naissance"] = "<input type='text' name='naissance' maxlength='10'>";
$vue["entree"] = "<input type='text' name='entree' maxlength='10'>";

$vue["abcd"] = "";
$vue["abcd"] .= "<select name='abcd'>";
$vue["abcd"] .= "<option value=''>vide</option>";
$vue["abcd"] .= "<option value='A'>A</option>";
$vue["abcd"] .= "<option value='B'>B</option>";
$vue["abcd"] .= "<option value='C'>C</option>";
$vue["abcd"] .= "<option value='D'>D</option>";
$vue["abcd"] .= "<option value='Z'>Z</option>";
$vue["abcd"] .= "</select>";

$vue["c1"] = "<input type='text' name='c1' maxlength='200'>";
$vue["c2"] = "<input type='text' name='c2' maxlength='200'>";
$vue["c3"] = "<input type='text' name='c3' maxlength='200'>";
$vue["c4"] = "<input type='text' name='c4' maxlength='200'>";
$vue["c5"] = "<input type='text' name='c5' maxlength='200'>";
$vue["c6"] = "<input type='text' name='c6' maxlength='200'>";
$vue["c7"] = "<input type='text' name='c7' maxlength='200'>";
$vue["c8"] = "<input type='text' name='c8' maxlength='200'>";
$vue["c9"] = "<input type='text' name='c9' maxlength='200'>";

// HTML for account data
$vue["identifiant"] = "<input type='text' name='identifiant' maxlength='32'>";
$vue["mot_de_passe"] = "<input type='text' name='mot_de_passe' maxlength='32'>";
