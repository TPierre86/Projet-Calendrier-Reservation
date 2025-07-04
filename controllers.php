<?php
session_start();
require_once(__DIR__ . '/database/DAO.php');

// Redirections ou contrôles AVANT tout affichage HTML
if (!isset($_SESSION['profil'])) {
    header('Location: /Projet-Calendrier-Reservation/models/connexion.php');
    exit;
}



require_once("templates/headers.php");
require_once("templates/bandeau.php");

switch ($_SESSION['profil']){
    case 'Membre':
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
        require_once("models/visiteur.php"); //On verra pars la suite

}

require_once("templates/footer.php");



