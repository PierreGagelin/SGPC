<?php

//
// Management of metadata stored in the client session
//

require_once('donnees.php');

// Initialize the filter
if (array_key_exists('filtre', $_SESSION) == false)
{
    $_SESSION['filtre'] = array();
    foreach($colonnes as $colonne)
    {
        $_SESSION['filtre'][$colonne] = 'off';
    }
    $_SESSION['filtre']['numero_adherent'] = 'on';
    $_SESSION['filtre']['prenom'] = 'on';
    $_SESSION['filtre']['nom'] = 'on';
}

// Update the filter on POST
if ((empty($_POST) == false) && (array_key_exists('filtre', $_POST) == true))
{
    foreach($colonnes as $colonne)
    {
        if (array_key_exists("filtre_$colonne", $_POST) == true)
        {
            $_SESSION['filtre'][$colonne] = 'on';
        }
        else
        {
            $_SESSION['filtre'][$colonne] = 'off';
        }
    }
}

?>
