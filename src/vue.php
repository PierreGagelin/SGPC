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
$vue["nom"] = "<input type='text' name='nom' maxlength='200' style='text-transform: uppercase;'>";
$vue["prenom"] = "<input type='text' name='prenom' maxlength='200'>";

$vue["cotis_payee"] = "";
$vue["cotis_payee"] .= "<select name='cotis_payee'>";
$vue["cotis_payee"] .= "<option value=''>vide</option>";
foreach($cotis_payee as $key)
{
    $vue["cotis_payee"] .= "<option value='$key'>$key</option>";
}
$vue["cotis_payee"] .= "</select>";

$vue["date_paiement"] = "<input type='text' name='date_paiement' maxlength='10'>";

$vue["p_ou_rien"] = "";
$vue["p_ou_rien"] .= "<select name='p_ou_rien'>";
$vue["p_ou_rien"] .= "<option value=''>vide</option>";
foreach($p_ou_rien as $key)
{
    $vue["p_ou_rien"] .= "<option value='$key'>$key</option>";
}
$vue["p_ou_rien"] .= "</select>";

$vue["cotis_date_premiere"] = "<input type='text' name='cotis_date_premiere' maxlength='4'>";
$vue["cotis_date_derniere"] = "<input type='text' name='cotis_date_derniere' maxlength='4'>";

$vue["cotis_region"] = "";
$vue["cotis_region"] .= "<select name='cotis_region'>";
$vue["cotis_region"] .= "<option value=''>vide</option>";
foreach($cotis_region as $key)
{
    $vue["cotis_region"] .= "<option value='$key'>$key</option>";
}
$vue["cotis_region"] .= "</select>";

$vue["cotis_payee_prec"] = "";
$vue["cotis_payee_prec"] .= "<select name='cotis_payee_prec'>";
$vue["cotis_payee_prec"] .= "<option value=''>vide</option>";
foreach($cotis_payee_prec as $key)
{
    $vue["cotis_payee_prec"] .= "<option value='$key'>$key</option>";
}
$vue["cotis_payee_prec"] .= "</select>";

$vue["adresse_1"] = "<input type='text' name='adresse_1' maxlength='200'>";
$vue["adresse_2"] = "<input type='text' name='adresse_2' maxlength='200'>";
$vue["code_postal"] = "<input type='text' name='code_postal' maxlength='5'>";
$vue["commune"] = "<input type='text' name='commune' maxlength='200' style='text-transform: uppercase;'>";

$vue["ad"] = "";
$vue["ad"] .= "<select name='ad'>";
$vue["ad"] .= "<option value=''>vide</option>";
foreach($ad as $key)
{
    $vue["ad"] .= "<option value='$key'>$key</option>";
}
$vue["ad"] .= "</select>";

$vue["profession"] = "";
$vue["profession"] .= "<select name='profession'>";
$vue["profession"] .= "<option value=''>vide</option>";
foreach($profession as $key)
{
    $vue["profession"] .= "<option value='$key'>$key</option>";
}
$vue["profession"] .= "</select>";

$vue["region"] = "";
$vue["region"] .= "<select name='region'>";
$vue["region"] .= "<option value=''>vide</option>";
foreach($region as $key)
{
    $vue["region"] .= "<option value='$key'>$key</option>";
}
$vue["region"] .= "</select>";

$vue["region_compte"] = "";
$vue["region_compte"] .= "<select name='region'>";
$vue["region_compte"] .= "<option value=''>vide</option>";
foreach($region_compte as $key)
{
    $vue["region_compte"] .= "<option value='$key'>$key</option>";
}
$vue["region_compte"] .= "</select>";

$vue["echelon"] = "<input type='text' name='echelon' maxlength='200'>";

$vue["bureau_nat"] = "";
$vue["bureau_nat"] .= "<select name='bureau_nat'>";
$vue["bureau_nat"] .= "<option value=''>vide</option>";
foreach($bureau_nat as $key)
{
    $vue["bureau_nat"] .= "<option value='$key'>$key</option>";
}
$vue["bureau_nat"] .= "</select>";

$vue["comite_nat"] = "";
$vue["comite_nat"] .= "<select name='comite_nat'>";
$vue["comite_nat"] .= "<option value=''>vide</option>";
foreach($comite_nat as $key)
{
    $vue["comite_nat"] .= "<option value='$key'>$key</option>";
}
$vue["comite_nat"] .= "</select>";

$vue["tel_port"] = "<input type='text' name='tel_port' maxlength='10'>";
$vue["tel_prof"] = "<input type='text' name='tel_prof' maxlength='10'>";
$vue["tel_dom"] = "<input type='text' name='tel_dom' maxlength='10'>";

$vue["fonc_nat_sgpc"] = "";
$vue["fonc_nat_sgpc"] .= "<select name='fonc_nat_sgpc'>";
$vue["fonc_nat_sgpc"] .= "<option value=''>vide</option>";
foreach($fonc_nat_sgpc as $key)
{
    $vue["fonc_nat_sgpc"] .= "<option value='$key'>$key</option>";
}
$vue["fonc_nat_sgpc"] .= "</select>";

$vue["fonc_nat_ccse"] = "";
$vue["fonc_nat_ccse"] .= "<select name='fonc_nat_ccse'>";
$vue["fonc_nat_ccse"] .= "<option value=''>vide</option>";
foreach($fonc_nat_ccse as $key)
{
    $vue["fonc_nat_ccse"] .= "<option value='$key'>$key</option>";
}
$vue["fonc_nat_ccse"] .= "</select>";

$vue["fonc_reg_sgpc"] = "";
$vue["fonc_reg_sgpc"] .= "<select name='fonc_reg_sgpc'>";
$vue["fonc_reg_sgpc"] .= "<option value=''>vide</option>";
foreach($fonc_reg_sgpc as $key)
{
    $vue["fonc_reg_sgpc"] .= "<option value='$key'>$key</option>";
}
$vue["fonc_reg_sgpc"] .= "</select>";

$vue["fonc_reg_cse"] = "";
$vue["fonc_reg_cse"] .= "<select name='fonc_reg_cse'>";
$vue["fonc_reg_cse"] .= "<option value=''>vide</option>";
foreach($fonc_reg_cse as $key)
{
    $vue["fonc_reg_cse"] .= "<option value='$key'>$key</option>";
}
$vue["fonc_reg_cse"] .= "</select>";

$vue["mail_priv"] = "<input type='text' name='mail_priv' maxlength='200'>";
$vue["mail_prof"] = "<input type='text' name='mail_prof' maxlength='200'>";
$vue["remarque_r"] = "<input type='text' name='remarque_r' maxlength='200'>";
$vue["remarque_n"] = "<input type='text' name='remarque_n' maxlength='200'>";

$vue["com_bud"] = "";
$vue["com_bud"] .= "<select name='com_bud'>";
$vue["com_bud"] .= "<option value=''>vide</option>";
foreach($com_bud as $key)
{
    $vue["com_bud"] .= "<option value='$key'>$key</option>";
}
$vue["com_bud"] .= "</select>";

$vue["com_com"] = "";
$vue["com_com"] .= "<select name='com_com'>";
$vue["com_com"] .= "<option value=''>vide</option>";
foreach($com_com as $key)
{
    $vue["com_com"] .= "<option value='$key'>$key</option>";
}
$vue["com_com"] .= "</select>";

$vue["com_cond"] = "";
$vue["com_cond"] .= "<select name='com_cond'>";
$vue["com_cond"] .= "<option value=''>vide</option>";
foreach($com_cond as $key)
{
    $vue["com_cond"] .= "<option value='$key'>$key</option>";
}
$vue["com_cond"] .= "</select>";

$vue["com_ce"] = "";
$vue["com_ce"] .= "<select name='com_ce'>";
$vue["com_ce"] .= "<option value=''>vide</option>";
foreach($com_ce as $key)
{
    $vue["com_ce"] .= "<option value='$key'>$key</option>";
}
$vue["com_ce"] .= "</select>";

$vue["com_dent"] = "";
$vue["com_dent"] .= "<select name='com_dent'>";
$vue["com_dent"] .= "<option value=''>vide</option>";
foreach($com_dent as $key)
{
    $vue["com_dent"] .= "<option value='$key'>$key</option>";
}
$vue["com_dent"] .= "</select>";

$vue["com_ffass"] = "";
$vue["com_ffass"] .= "<select name='com_ffass'>";
$vue["com_ffass"] .= "<option value=''>vide</option>";
foreach($com_ffass as $key)
{
    $vue["com_ffass"] .= "<option value='$key'>$key</option>";
}
$vue["com_ffass"] .= "</select>";

$vue["com_pharma"] = "";
$vue["com_pharma"] .= "<select name='com_pharma'>";
$vue["com_pharma"] .= "<option value=''>vide</option>";
foreach($com_pharma as $key)
{
    $vue["com_pharma"] .= "<option value='$key'>$key</option>";
}
$vue["com_pharma"] .= "</select>";

$vue["com_ret"] = "";
$vue["com_ret"] .= "<select name='com_ret'>";
$vue["com_ret"] .= "<option value=''>vide</option>";
foreach($com_ret as $key)
{
    $vue["com_ret"] .= "<option value='$key'>$key</option>";
}
$vue["com_ret"] .= "</select>";

$vue["naissance"] = "<input type='text' name='naissance' maxlength='10'>";
$vue["entree"] = "<input type='text' name='entree' maxlength='10'>";

$vue["abcd"] = "";
$vue["abcd"] .= "<select name='abcd'>";
$vue["abcd"] .= "<option value=''>vide</option>";
foreach($abcd as $key)
{
    $vue["abcd"] .= "<option value='$key'>$key</option>";
}
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
$vue["c10"] = "<input type='text' name='c10' maxlength='200'>";
$vue["c11"] = "<input type='text' name='c11' maxlength='200'>";
$vue["c12"] = "<input type='text' name='c12' maxlength='200'>";

// HTML for account data
$vue["identifiant"] = "<input type='text' name='identifiant' maxlength='32'>";
$vue["mot_de_passe"] = "<input type='text' name='mot_de_passe' maxlength='32'>";
