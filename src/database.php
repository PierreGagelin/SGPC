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
        die("Erreur : échec de la lecture du fichier des paramètres de connexion");
    }

    // Decode as an associative array
    $DB_CONFIG = json_decode($json, true);
    if ($DB_CONFIG == null)
    {
        die("Erreur : échec du décodage JSON des paramètres de connexion");
    }
}

function db_open()
{
    global $DB_CONFIG;

    $db = new mysqli($DB_CONFIG["address"], $DB_CONFIG["username"], $DB_CONFIG["password"], $DB_CONFIG["database"]);

    if ($db->connect_errno != 0)
    {
        die("Erreur : échec de la connexion MySQL erreur_numéro=$db->connect_errno erreur_message=$db->connect_error");
    }

    if ($db->set_charset("utf8") == false)
    {
        die("Erreur : impossible d'utiliser l'encodage UTF-8");
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
        echo "<p>Erreur : échec de l'exécution de la requête erreur_numéro=$db->errno erreur_message=$db->error requête=$req</p>";
    }

    return $rep;
}

db_init();

?>
