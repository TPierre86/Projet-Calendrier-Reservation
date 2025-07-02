<?php
require_once('../database/DAO.php');



if ($_SERVER['REQUEST_METHOD'] === 'GET') { //a changer
    $mailUtilisateur = isset($_GET['mail'])?($_GET['mail']) : '';
    $motDePasse = isset($_GET['pwd'])?($_GET['pwd']) : '';

    $dao = new DAOReservation();
    $dao->connection();

    $utilisateur = $dao->getMail($mailUtilisateur);

    if ($utilisateur) {
        $pwd = $utilisateur["password"];
        if (password_verify($motDePasse, $pwd)) {
            header('Location: /Projet-Calendrier-Reservation/public/index.php');
            exit;
        } else {
            $message = "identifiant incorrect";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    } else {
        $message = "identifiant incorrect";
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
    <form method="get" class="form">
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


