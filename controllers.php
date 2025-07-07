<?php
session_start();
require_once(__DIR__ . '/database/DAO.php');

// Redirections ou contrôles AVANT tout affichage HTML



require_once("templates/headers.php");
require_once("templates/bandeau.php");

if (!isset($_SESSION['profil'])) {
    require_once("models/visiteur.php");
    require_once("templates/footer.php");
    exit;
}

switch ($_SESSION['profil']){
    case 'Membres':
        require_once("models/membre.php");
        break;
    case 'Gestionnaire':
        require_once("models/gestionnaire.php");
        break;
    case 'Menage':
        require_once("models/menage.php");
        break;
    case "Président d'association":
        require_once("models/association.php");
        break;
    default:
        require_once("models/visiteur.php"); 

}

require_once("templates/footer.php");



