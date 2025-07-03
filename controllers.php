<?php

session_start();

require_once("templates/headers.php");


switch ($_SESSION['profil']){
    case'Membre':
        require_once("models/membre.php");
        break;
    case'Gestionnaire':
        require_once("models/gestionnaire.php");
        break;
    case'Menage':
        require_once("models/menage.php");
        break;
    case'Président':
        require_once("models/association.php");
    default:
        require_once("index") //On verra pars la suite

}










require_once("templates/footer.php");
