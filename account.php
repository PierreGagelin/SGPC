<?php

//
// Library for account management
//

$ACCOUNT_FILE = 'comptes.json';
$ACCOUNT_ARRAY = array();

// Load the accounts
function account_load()
{
    global $ACCOUNT_FILE;
    global $ACCOUNT_ARRAY;

    $json = file_get_contents($ACCOUNT_FILE);
    if ($json == false)
    {
        die("Echec de la lecture du fichier [fichier=$ACCOUNT_FILE]");
    }

    // Decode as an associative array
    $ACCOUNT_ARRAY = json_decode($json, true);
    if ($ACCOUNT_ARRAY == null)
    {
        die("Echec du décodage JSON [chaîne=$json]");
    }
}

// Store the accounts
function account_store()
{
    global $ACCOUNT_FILE;
    global $ACCOUNT_ARRAY;

    $json = json_encode($ACCOUNT_ARRAY, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json == false)
    {
        die("Echec de l'encodage JSON [object=$ACCOUNT_ARRAY]");
    }

    $res = file_put_contents($ACCOUNT_FILE, $json, LOCK_EX);
    if ($res == false)
    {
        die("Echec de l'écriture du fichier [fichier=$ACCOUNT_FILE]");
    }
}

// Check wether an account exists
function account_exist($user)
{
    global $ACCOUNT_ARRAY;

    if (array_key_exists($user, $ACCOUNT_ARRAY) == false)
    {
        return false;
    }
    return true;
}

// Check wether an account with a specific password exists
function account_password_exist($user, $password)
{
    global $ACCOUNT_ARRAY;

    if (array_key_exists($user, $ACCOUNT_ARRAY) == false)
    {
        return false;
    }

    if ($ACCOUNT_ARRAY[$user]["password"] != $password)
    {
        return false;
    }

    return true;
}

// Add an account
function account_add($user, $password, $region_name)
{
    global $ACCOUNT_ARRAY;

    $ACCOUNT_ARRAY[$user] = array(
        "password" => $password,
        "region" => $region_name,
        "priviledged" => false
    );
    account_store();
}

// Delete an account
function account_del($user)
{
    global $ACCOUNT_ARRAY;

    if (account_exist($user) == true)
    {
        unset($ACCOUNT_ARRAY[$user]);
        account_store();
    }
}

// Get an a specific account: it has to exist
function account_get($user)
{
    global $ACCOUNT_ARRAY;

    $account = array();

    $account["user"] = $user;
    $account["region"] = $ACCOUNT_ARRAY[$user]["region"];
    $account["password"] = $ACCOUNT_ARRAY[$user]["password"];
    $account["priviledged"] = $ACCOUNT_ARRAY[$user]["priviledged"];

    return $account;
}

function account_get_list()
{
    global $ACCOUNT_ARRAY;

    return array_keys($ACCOUNT_ARRAY);
}

// Initial load of accounts
account_load();

?>
