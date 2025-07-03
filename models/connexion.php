<?php
require_once('../database/DAO.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mailUtilisateur = isset($_POST['mail'])?($_POST['mail']) : '';
    $motDePasse = isset($_POST['pwd'])?($_POST['pwd']) : '';

    $dao = new DAOReservation();
    $dao->connexion();

    $utilisateurs = $dao->getMail($mailUtilisateur);

    foreach ($utilisateurs as $utilisateur){
    if ($utilisateur && password_verify($motDePasse, $utilisateur["password"]) || $motDePasse===$utilisateur["password"] ) {
            $_SESSION['profil'] = $utilisateur["profil"];
            $_SESSION["connected_user"]=$utilisateur["id_utilisateur"];
            header('Location: ../index.php');
            exit;
        } 
            $message = "identifiant ou mot de passe incorrect";
            echo "<script type='text/javascript'>alert('$message');</script>";
            
}
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription</title>
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/inscription.css" />
    </head>
    <body>
    <article>
        <h1>Connexion</h1>
    <form method="POST" class="form">
        <label for="mail" class="txt">Adresse e-mail</label>
        <input type="text" id="mail" name="mail" class="form1" required />
        <label for="pwd" class="txt">Mots de passe</label>
        <input type="password" id="pwd" name="pwd" class="form1" required />
        <button type="submit">
        <span class="circle1"></span>
        <span class="circle2"></span>
        <span class="circle3"></span>
        <span class="circle4"></span>
        <span class="circle5"></span>
        <span class="text">Connexion</span>
        </button>
    </form>
    </article>
    </body>
</html>