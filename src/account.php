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
        die("Failed to add account user=$user");
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
        die("Failed to delete account user=$user");
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

    if (($account == null) || ($account == false))
    {
        die("Failed to get account: user does not exist user=$user");
    }

    # Fix privilege type
    $account["privileged"] = ($account["privileged"] == 1) ? true : false;

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
