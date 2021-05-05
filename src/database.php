<?php

$DB_CONFIG_FILE = "database.json";
$DB_CONFIG = array();

function db_init()
{
    global $DB_CONFIG_FILE;
    global $DB_CONFIG;

    $json = file_get_contents($DB_CONFIG_FILE);
    if ($json == false)
    {
        die("Failed to read file=$DB_CONFIG_FILE");
    }

    // Decode as an associative array
    $DB_CONFIG = json_decode($json, true);
    if ($DB_CONFIG == null)
    {
        die("Failed to decode JSON string=$json");
    }
}

function db_open()
{
    global $DB_CONFIG;

    $db = new mysqli($DB_CONFIG["address"], $DB_CONFIG["username"], $DB_CONFIG["password"], $DB_CONFIG["database"]);

    if ($db->connect_errno != 0)
    {
        die("Failed to connect to mysql errno=$db->connect_errno error=$db->connect_error");
    }

    if ($db->set_charset("utf8") == false)
    {
        die("Failed to set charset to UTF-8");
    }

    return $db;
}

function db_close($db)
{
    $db->close();
}

function db_query($db, $req)
{
    $rep = $db->query($req);
    if ($rep == false)
    {
        echo "Failed to execute request errno=$db->errno error=$db->error <br />";
    }

    return $rep;
}

db_init();

//
// Code samples
//

// $db = db_open();
// $keys = array_keys($MEMBER_ARRAY);
// foreach ($keys as $key)
// {
//     $member = $MEMBER_ARRAY[$key];

//     $numero_adherent = $member["numero_adherent"];
//     $region = $member["region"];
//     $nom = $member["nom"];
//     $prenom = $member["prenom"];
//     $info = json_encode($member);

//     $req = "";
//     $req .= "INSERT INTO members (numero_adherent, region, nom, prenom, info) ";
//     $req .= "VALUES ('$numero_adherent', '$region', '$nom', '$prenom', '$info')";

//     db_query($db, $req);
// }
// db_close($db);

// $db = db_open();
// $req = "SELECT * FROM members ORDER BY numero_adherent DESC";
// $rep = db_query($db, $req);
// $members = $rep->fetch_all(MYSQLI_ASSOC);
// foreach ($members as $member)
// {
//     $MEMBER_ARRAY[$member["numero_adherent"]] = $member;
// }
// db_close($db);

?>
