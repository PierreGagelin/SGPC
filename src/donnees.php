<?php

require_once("member.php");

// tableau de toutes les colonnes
$colonnes = array(
    'numero_adherent',
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
    "c9",
);

// Tell if the session is connected
function is_connected()
{
    if (empty($_SESSION) == true)
    {
        return false;
    }

    if (array_key_exists("identifiant", $_SESSION) == false)
    {
        return false;
    }

    if (array_key_exists("region", $_SESSION) == false)
    {
        return false;
    }

    if (array_key_exists("priviledged", $_SESSION) == false)
    {
        return false;
    }

    return true;
}

// Tell if the session has root priviledges
function is_priviledged()
{
    if (is_connected() == false)
    {
        return false;
    }

    return $_SESSION["priviledged"];
}

// tableau qui permet les vérifications
//   - contient une expression régulière
//   - un message d'erreur si la regex ne match pas
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
    ),
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
function erreur_verification($tableau)
{
    $erreur = "Erreur : les entrées autorisées sont :<br />";
    foreach($tableau as $entree)
    {
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
    "3/2",
);
$p_ou_rien = array(
    "p",
);
$adhesion = $cotis_payee;
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
);
$region_compte = array(
    "Aura",
    "Nouvelle-Aquitaine",
    "Occitanie",
    "Grand-Est",
    "Hauts-de-France",
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
    "National",
);
$bureau_nat = array(
    "1",
    "2",
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
    "TH",
);
$fonc_nat_irp = array(
    "DS",
    "CCE-T",
    "CCE-S",
    "RS",
    "CE-SEC",
    "CE-TR",
);
$fonc_reg = array(
    "P",
    "S",
    "T",
    "M",
    "SA",
    "TA",
    "VP",
    "PH",
);
$fonc_reg_irp = array(
    "DS",
    "DP-T",
    "CD-T",
    "DP-S",
    "CE-S",
    "RS",
    "CE-SEC",
    "CE-TR",
);
$chsc_pc_r = array(
    "S",
    "T",
    "t",
    "s",
    "RS",
);
$chsc_pc_n = $chsc_pc_r;
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

// vérifie que la valeur est cohérente selon la colonne
function verifier($colonne, $valeur)
{
    global $verification;

    try
    {
        global ${$colonne};
    }
    catch(Exception $e)
    {
        echo $e->getMessage() ; "<br />";
    }

    if (isset($verification[$colonne]))
    {
        $verif_col = $verification[$colonne];
        if (!isset($verif_col['regex']) || !isset($verif_col['erreur']))
        {
            die("Erreur : tableau des vérifications corrompu !");
        }
        $regex = $verif_col['regex'];
        $erreur = $verif_col['erreur'];
        if (!preg_match($regex, $valeur))
        {
            die($erreur);
        }
    }
    elseif (!empty(${$colonne}))
    {
        if (!in_array($valeur, ${$colonne}))
        {
            erreur_verification(${$colonne});
        }
    }
    else
    {
        die("Erreur : aucune vérification implémentée pour $colonne");
    }
}

// Add a new SGPC member and return its identifier
function sgpc_member_add($lastname, $firstname, $region_name)
{
    verifier("prenom", $firstname);
    verifier("nom", $lastname);
    verifier("region", $region_name);

    $numero_adherent = member_add();

    member_update($numero_adherent, "prenom", $firstname);
    member_update($numero_adherent, "nom", $lastname);
    member_update($numero_adherent, "region", $region_name);

    return $numero_adherent;
}

// Update SGPC member attribute
function sgpc_member_update($numero_adherent, $colonne, $valeur)
{
    if ($colonne == "numero_adherent")
    {
        // Cannot update member identifier
        return;
    }

    verifier($colonne, $valeur);

    member_update($numero_adherent, $colonne, $valeur);
}

// vide l'intégralité de la colonne $col
// DANGEREUX !!!
function supprimer_colonne($col)
{
    global $colonnes;

    if (is_priviledged() == false)
    {
        die("Erreur : vous n'avez pas le droit de faire cette opération !");
    }

    if (in_array($col, $colonnes) == false)
    {
        return;
    }

    $id_list = member_get_list();
    foreach($id_list as $id)
    {
        member_attr_del($id, $col);
    }
}

// copie la colonne $col1 vers la colonne $col2
// DANGEREUX !!!
function copier_colonne($col1, $col2)
{
    global $colonnes;

    if (is_priviledged() == false)
    {
        die("Erreur : vous n'avez pas le droit de faire cette opération !");
    }

    if ((in_array($col1, $colonnes) == false) || (in_array($col2, $colonnes) == false))
    {
        return;
    }

    $id_list = member_get_list();
    foreach ($id_list as $id)
    {
        $member = member_get($id);

        if (array_key_exists($col1, $member) == true)
        {
            member_update($id, $col2, $member[$col1]);
        }
        else
        {
            member_attr_del($id, $col2);
        }
    }
}

// effectue le basculement annuel des cotisations :
//   - copie des cotisations de l'année vers le bilan de l'année précédente :
//     - colonne "cotis_payee" copiée dans "adhesion"
//   - remise à zéro des cotisations de l'année :
//     - suppression de la colonne "cotis_payee"
function basculer_cotisations()
{
    if (is_priviledged() == false)
    {
        die("Erreur : vous n'avez pas le droit de faire cette opération !");
    }

    copier_colonne("cotis_payee", "adhesion");
    supprimer_colonne("cotis_payee");
}

?>
