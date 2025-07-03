<?php
require_once('../database/DAO.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mailUtilisateur = isset($_POST['mail'])?($_POST['mail']) : '';
    $motDePasse = isset($_POST['pwd'])?($_POST['pwd']) : '';

    $dao = new DAOReservation();
    $dao->connexion();

$utilisateurs = $dao->getMail($mailUtilisateur);

foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur && (password_verify($motDePasse, $utilisateur["password"]) || $motDePasse === $utilisateur["password"])) {  
        // Connexion OK
        $_SESSION['profil'] = $utilisateur["profil"];          
        $_SESSION["connected_user"] = $utilisateur["id_utilisateur"];
        $_SESSION["prenom"] = $utilisateur["prenom_utilisateur"];
        header('Location: ../controllers.php');
        exit;
    }
}

// Si aucun utilisateur n’a matched, afficher erreur
$message = "identifiant ou mot de passe incorrect";
echo "<script type='text/javascript'>alert('$message');</script>";
};
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription</title>
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/inscription.css" />
    </head>
    <body id="bodyFormInscription">
    <article id="formInscription">
        <h1 id="titreFormInscription">Connexion</h1>
    <form method="POST" id="formulaireInscription">
        <label for="mail" class="txt">Adresse e-mail</label>
        <input type="text" id="mail" name="mail" class="inputForm" required />
        <label for="pwd" class="txt">Mots de passe</label>
        <input type="password" id="pwd" name="pwd" class="inputForm" required />
        <button type="submit" id="submitButton">
        <span class="circle1"></span>
        <span class="circle2"></span>
        <span class="circle3"></span>
        <span class="circle4"></span>
        <span class="circle5"></span>
        <span>Connexion</span>
        </button>
    </form>
    </article>
    </body>
</html>