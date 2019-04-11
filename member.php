<?php

//
// Library for member management
//

$MEMBER_FILE = 'adherents.json';
$MEMBER_ARRAY = array();

// Load the members
function member_load()
{
    global $MEMBER_FILE;
    global $MEMBER_ARRAY;

    $json = file_get_contents($MEMBER_FILE);
    if ($json == false)
    {
        die("Echec de la lecture du fichier [fichier=$MEMBER_FILE]");
    }

    // Decode as an associative array
    $MEMBER_ARRAY = json_decode($json, true);
    if ($MEMBER_ARRAY == null)
    {
        die("Echec du décodage JSON [chaîne=$json]");
    }
}

// Store the members
function member_store()
{
    global $MEMBER_FILE;
    global $MEMBER_ARRAY;

    $json = json_encode($MEMBER_ARRAY, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json == false)
    {
        die("Echec de l'encodage JSON [object=$MEMBER_ARRAY]");
    }

    $res = file_put_contents($MEMBER_FILE, $json);
    if ($res == false)
    {
        die("Echec de l'écriture du fichier [fichier=$MEMBER_FILE]");
    }
}

function member_get_last_id()
{
    global $MEMBER_ARRAY;

    $keys = array_keys($MEMBER_ARRAY);
    $max = max($keys);

    return $max;
}

function member_exist($id)
{
    global $MEMBER_ARRAY;

    $exists = array_key_exists($id, $MEMBER_ARRAY);

    return $exists;
}

function member_update($id, $column, $value, $update = true)
{
    global $MEMBER_ARRAY;

    $MEMBER_ARRAY[$id][$column] = $value;

    if ($update == true)
    {
        member_store();
    }

    return $id;
}

function member_add()
{
    global $MEMBER_ARRAY;

    $id = member_get_last_id();

    // Alphanumerical incrementation
    $id++;

    $MEMBER_ARRAY[$id] = array(
        "numero_adherent" => $id,
    );

    member_store();

    return $id;
}

function member_del($id)
{
    global $MEMBER_ARRAY;

    if (member_exist($id) == false)
    {
        return;
    }

    unset($MEMBER_ARRAY[$id]);
    member_store();
}

function member_attr_del($id, $column)
{
    global $MEMBER_ARRAY;

    if (member_exist($id) == false)
    {
        return;
    }

    $member = member_get($id);
    if (array_key_exists($column, $member) == false)
    {
        return;
    }

    unset($MEMBER_ARRAY[$id][$column]);
    member_store();
}

function member_get($id)
{
    global $MEMBER_ARRAY;

    return $MEMBER_ARRAY[$id];
}

function member_get_list()
{
    global $MEMBER_ARRAY;

    return array_keys($MEMBER_ARRAY);
}

// Initial load of members
member_load();

?>
