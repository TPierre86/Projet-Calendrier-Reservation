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
    <link rel="stylesheet" href="../public/styles/layour/headers&footers.css" />
    <a href="//img.icons8.com/plumpy/24/circled-menu--v1.png>"></a>
  </head>
  <body>
    <header>
        <section class="head">
            <article class="titre">
                <h1 class="titre1">Comité Monestiés</h1>
                <h2 class="titre2">Calendrier de réservation de la salle des fêtes</h2>
            <article>
        </section>
        <nav class="menue">
            <img width="24" height="24" src="https://img.icons8.com/plumpy/24/circled-menu--v1.png" alt="circled-menu--v1"/>Menue
        </nav>
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


