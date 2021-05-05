<?php

//
// Library for account management
//

require_once("database.php");

// Add an account
function account_add($user, $password, $region)
{
    $db = db_open();
    $rep = db_query($db, "INSERT INTO account VALUES ('$user', '$password', '$region', 0);");
    db_close($db);

    if ($rep != true)
    {
        die("Erreur : échec de l'ajout du compte identifiant=$user");
    }
}

// Delete an account
function account_del($user)
{
    $db = db_open();
    $rep = db_query($db, "DELETE FROM account WHERE user = '$user';");
    db_close($db);

    if ($rep != true)
    {
        die("Erreur : échec de la suppression du compte identifiant=$user");
    }
}

// Get an account
function account_get($user)
{
    $db = db_open();
    $rep = db_query($db, "SELECT * FROM account WHERE user = '$user';");
    $account = $rep->fetch_array(MYSQLI_ASSOC);
    $rep->close();
    db_close($db);

    if ($account != null)
    {
        # Fix privilege type
        $account["privileged"] = ($account["privileged"] == 1) ? true : false;
    }

    return $account;
}

// Get a list of every accounts
function account_get_list()
{
    $db = db_open();
    $rep = db_query($db, "SELECT * FROM account;");
    $account_list = $rep->fetch_all(MYSQLI_ASSOC);
    $rep->close();
    db_close($db);

    return $account_list;
}

?>
