<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Comité monestiés</title>
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/refresh.css"">
  </head>
  <body id="bodyRefresh">
    <article>
      <p id="good">✅ Inscription réussie !</p>
      <p>Redirection en cours vers la page de connexion<span class="progress"></span></p>
    </article>
    <p id="bug">
      Si la redirection automatique ne fonctionne pas :
      <a href="http://localhost/Projet-Calendrier-Reservation/models/connexion.php">connexion</a>
    </p>
    <script>
      setTimeout(() => {
        window.location.href = "connexion.php";
      }, 2000); // 2 secondes
    </script>
  </body>
</html>
