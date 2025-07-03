<?php
require_once(__DIR__ . '/../database/DAO.php');
session_start();

$userName = htmlspecialchars($_SESSION['prenom']);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Comité monestié</title>
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/calendar.css" />
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/layout/headers&footers.css" />
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/menuBurger.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/cerulean/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  </head>
  <body>
    <header>
        <section class="head">
            <article class="titre">
                <h1 class="titre1">Comité Monestiés</h1>
                <h2 class="titre2">Calendrier de réservation de la salle des fêtes</h2>
            <article>
        </section>
        <article class="connect" id="connectStatus" data-logged="<?php echo isset($_SESSION['connected_user']) ? '1' : '0'; ?>">
          <span>Se connecter |</span>
          <span>S'inscrire</span>
        </article>
        <script>
  const connectStatus = document.getElementById('connectStatus');
  if (connectStatus.dataset.logged === "1") {
    connectStatus.innerHTML = "Bienvenue, <?php echo $userName ; ?> !<br> <a href='/Projet-Calendrier-Reservation/models/membre.php'>Se déconnecter</a>";
  }
</script>
    </header>



