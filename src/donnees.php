<?php

require_once "member.php";

$colonnes = array(
    "numero_adherent",
    "nom",
    "prenom",
    "region",
    "cotis_payee",
    "date_paiement",
    "p_ou_rien",
    "cotis_payee_prec",
    "cotis_date_premiere",
    "cotis_date_derniere",
    "cotis_region",
    "adresse_1",
    "adresse_2",
    "code_postal",
    "commune",
    "ad",
    "profession",
    "echelon",
    "bureau_nat",
    "comite_nat",
    "tel_port",
    "tel_prof",
    "tel_dom",
    "fonc_nat_sgpc",
    "fonc_nat_ccse",
    "fonc_reg_sgpc",
    "fonc_reg_cse",
    "mail_priv",
    "mail_prof",
    "remarque_r",
    "remarque_n",
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
    "c9",
    "c10",
    "c11",
    "c12",
);

$verification = array(
    "numero_adherent" => array(
        "regex" => "#^[A-Z]{2}[0-9]{3}$#",
        "erreur" => "<p>Erreur : seules les entrées de la forme 'AB012' sont autorisées</p>"
    ),
    "nom" => array(
        "regex" => "#^[\\p{L}'. \\\-]+$#u",
        "erreur" => "<p>Erreur : seuls les caractères ci-dessous sont autorisés</p>" .
                    "<ul>" .
                    "<li>des lettres (majuscules, minuscules, accentuées)</li>" .
                    "<li>des points</li>" .
                    "<li>des espaces</li>" .
                    "<li>des apostrophes</li>" .
                    "<li>des tirets</li>" .
                    "</ul>"
    ),
    "code_postal" => array(
        "regex" => "#^[0-9]{5}$#",
        "erreur" => "<p>Erreur : seules les entrées de la forme '01234' sont autorisées</p>"
    ),
    "commune" => array(
        "regex" => "#^['A-Z\\\, /-]+$#",
        "erreur" => "<p>Erreur : seuls les caractères ci-dessous sont autorisés</p>" .
                    "<ul>" .
                    "<li>des lettres de 'A' à 'Z'</li>" .
                    "<li>des apostrophes</li>" .
                    "<li>des virgules</li>" .
                    "<li>des espaces</li>" .
                    "<li>des slashs</li>" .
                    "<li>des tirets</li>" .
                    "</ul>"
    ),
    "cotis_date_premiere" => array(
        "regex" => "#^[12][0-9]{3}$#",
        "erreur" => "<p>Erreur : seules les entrées de la forme 'AAAA' sont autorisées</p>"
    ),
    "date_paiement" => array(
        "regex" => "#^[0123][0-9]/[01][0-9]/[12][0-9]{3}$#",
        "erreur" => "<p>Erreur : seules les entrées de la forme 'JJ/MM/AAAA' sont autorisées</p>"
    ),
    "tel_port" => array(
        "regex" => "#^0[1-9][0-9]{8}$#",
        "erreur" => "<p>Erreur : seules les entrées de la forme '0123456789' sont autorisées</p>"
    ),
    "mail_priv" => array(
        "regex" => "#^[a-z0-9_.-]+@[a-z0-9_.-]+\\.[a-z]+$#",
        "erreur" => "<p>Erreur : seules les entrées de la forme 'XX@XX.Y' sont autorisées</p>" .
                    "<p>X acceptant les caractères ci-dessous</p>" .
                    "<ul>" .
                    "<li>des lettres de 'a' à 'z'</li>" .
                    "<li>des chiffres</li>" .
                    "<li>des underscores</li>" .
                    "<li>des points</li>" .
                    "<li>des tirets</li>" .
                    "</ul>" .
                    "<p>Y acceptant les caractères ci-dessous</p>" .
                    "<ul>" .
                    "<li>des lettres de 'a' à 'z'</li>" .
                    "</ul>"
    ),
    "remarque_r" => array(
        "regex" => "#^[\\p{L}0-9\\\,_'. /-]+$#u",
        "erreur" => "<p>Erreur : seuls les caractères ci-dessous sont autorisés</p>" .
                    "<ul>" .
                    "<li>des lettres (majuscules, minuscules, accentuées)</li>" .
                    "<li>des chiffres</li>" .
                    "<li>des virgules</li>" .
                    "<li>des espaces</li>" .
                    "<li>des apostrophes</li>" .
                    "<li>des underscores</li>" .
                    "<li>des slashs</li>" .
                    "<li>des points</li>" .
                    "<li>des tirets</li>" .
                    "</ul>"
    ),
);

$verification["adresse_1"] = $verification["remarque_r"];
$verification["adresse_2"] = $verification["remarque_r"];
$verification["prenom"] = $verification["nom"];
$verification["cotis_date_derniere"] = $verification["cotis_date_premiere"];
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
$verification["c10"] = $verification["remarque_r"];
$verification["c11"] = $verification["remarque_r"];
$verification["c12"] = $verification["remarque_r"];
$verification["identifiant"] = $verification["remarque_r"];
$verification["mot_de_passe"] = $verification["remarque_r"];

$cotis_payee = array(
    "1",
    "2",
    "3",
    "1/2",
    "3/2",
);

$p_ou_rien = array(
    "p",
);

$cotis_payee_prec = $cotis_payee;

$ad = array(
    "AD",
    "AD-RSI",
    "AD-RT",
    "AD-ARS",
);

$profession = array(
    "MC",
    "CDC",
    "PHC",
    "MCCS",
    "CDCCS",
    "PHCCS",
    "MCRA",
    "MCR",
);

$region = array(
    "Aura",
    "Nouvelle-Aquitaine",
    "Occitanie",
    "Grand-Est",
    "Hauts-de-France",
    "Bourgogne",
    "Bretagne",
    "Centre",
    "Normandie",
    "Ile-de-France",
    "Pays-de-la-Loire",
    "Paca",
    "Antilles",
    "Reunion",
    "TN",
    "GN",
);

$region_compte = array(
    "Aura",
    "Nouvelle-Aquitaine",
    "Occitanie",
    "Grand-Est",
    "Hauts-de-France",
    "Bourgogne",
    "Bretagne",
    "Centre",
    "Normandie",
    "Ile-de-France",
    "Pays-de-la-Loire",
    "Paca",
    "Antilles",
    "Reunion",
    "National",
);

$cotis_region = $region;

$bureau_nat = array(
    "1",
    "2",
);

$comite_nat = $bureau_nat;

$fonc_nat_sgpc = array(
    "PN",
    "SN",
    "TN",
    "SNA",
    "TNA",
    "VPN",
    "PH",
    "TH",
);

$fonc_nat_ccse = array(
    "DS",
    "RS",
    "CCSE-titulaire",
    "CCSE-suppleant",
    "CCSE-tresorier",
    "CCSE-secretaire",
);

$fonc_reg_sgpc = array(
    "P",
    "S",
    "T",
    "M",
    "SA",
    "TA",
    "VP",
    "PH",
);

$fonc_reg_cse = array(
    "DS",
    "RS",
    "CSE-titulaire",
    "CSE-suppleant",
    "CSE-tresorier",
    "CSE-secretaire"
);

$com_bud = array(
    "M",
    "R",
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
    "Z",
);

// Tell if the session is connected
function is_connected()
{
    if ((empty($_SESSION) == true) ||
        (array_key_exists("user", $_SESSION) == false) ||
        (array_key_exists("region", $_SESSION) == false) ||
        (array_key_exists("privileged", $_SESSION) == false))
    {
        return false;
    }

    return true;
}

// Tell if the session has root priviledges
function is_privileged()
{
    if (is_connected() == false)
    {
        return false;
    }

    return $_SESSION["privileged"];
}

// Check column data consistency
function check_column_data($colonne, $valeur)
{
    global $verification;

    try
    {
        global ${$colonne};
    }
    catch(Exception $e)
    {
        echo "<p>Erreur : variable introuvable erreur_message={$e->getMessage()}</p>";
    }

    if (isset($verification[$colonne]) == true)
    {
        if (preg_match($verification[$colonne]["regex"], $valeur) == false)
        {
            $erreur = "";
            $erreur .= "<p>Erreur : échec de la vérification de l'entrée colonne=$colonne valeur=$valeur</p>";
            $erreur .= $verification[$colonne]["erreur"];
            die($erreur);
        }
    }
    elseif (empty(${$colonne}) == false)
    {
        if (in_array($valeur, ${$colonne}) == false)
        {
            $erreur = "";
            $erreur .= "<p>Erreur : échec de la vérification de l'entrée colonne=$colonne valeur=$valeur</p>";
            $erreur .= "<p>Erreur : seules les entrées ci-dessous sont autorisées</p>";
            $erreur .= "<ul>";
            foreach(${$colonne} as $entree)
            {
                $erreur .= "<li>$entree</li>";
            }
            $erreur .= "</ul>";
            die($erreur);
        }
    }
    else
    {
        die("<p>Erreur : aucune vérification implémentée colonne=$colonne</p>");
    }
}

?>
