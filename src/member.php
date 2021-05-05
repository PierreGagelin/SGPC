<?php

//
// Library for member management
//

require_once("database.php");

function member_add($lastname, $firstname, $region)
{
    $db = db_open();

    // Lock database to prevent primary key to be incremented by someone else
    $rep = db_query($db, "LOCK TABLES member WRITE;");
    if ($rep == false)
    {
        goto end;
    }

    // Get last member id
    $rep = db_query($db, "SELECT * FROM member ORDER BY numero_adherent DESC LIMIT 1;");
    if ($rep == false)
    {
        goto end;
    }
    $account = $rep->fetch_array(MYSQLI_ASSOC);
    if ($account == null)
    {
        // Table is empty, we initialize it
        $id = "AA000";
    }
    else
    {
        $id = $account["numero_adherent"];
    }
    $rep->close();

    // Alphanumeric incrementation
    $id++;

    $query = "";
    $query .= "INSERT INTO member (numero_adherent, nom, prenom, region) ";
    $query .= "VALUES ('$id', '$lastname', '$firstname', '$region');";
    $rep = db_query($db, $query);

end:
    db_query($db, "UNLOCK TABLES;");
    db_close($db);

    if ($rep == false)
    {
        die("Erreur : échec de la création de l'adhérent nom=$lastname prenom=$firstname region=$region");
    }

    return $id;
}

function member_del($id)
{
    $db = db_open();
    $rep = db_query($db, "DELETE FROM member WHERE numero_adherent = '$id';");
    db_close($db);

    if ($rep != true)
    {
        die("Erreur : échec de la suppression de l'adhérent numero_adherent=$id");
    }
}

function member_update($id, $column, $value)
{
    $db = db_open();
    $rep = db_query($db, "UPDATE member SET $column = '$value' WHERE numero_adherent = '$id';");
    db_close($db);

    if ($rep != true)
    {
        die("Erreur : échec de la mise à jour de l'adhérent numero_adherent=$id colonne=$column valeur=$value");
    }
}

function member_attr_del($id, $column)
{
    $db = db_open();
    $rep = db_query($db, "UPDATE member SET $column = NULL WHERE numero_adherent = '$id';");
    db_close($db);

    if ($rep != true)
    {
        die("Erreur : échec de la suppression de la colonne de l'adhérent numero_adherent=$id colonne=$column");
    }
}

function member_get($id)
{
    $db = db_open();
    $rep = db_query($db, "SELECT * FROM member WHERE numero_adherent = '$id';");
    $member = $rep->fetch_array(MYSQLI_ASSOC);
    $rep->close();
    db_close($db);

    if ($member == null)
    {
        die("Erreur : échec de la consultation de l'adhérent numero_adherent=$id");
    }

    return $member;
}

function member_get_list()
{
    $db = db_open();
    $rep = db_query($db, "SELECT * FROM member;");
    $member_list = $rep->fetch_all(MYSQLI_ASSOC);
    $rep->close();
    db_close($db);

    return $member_list;
}

?>
