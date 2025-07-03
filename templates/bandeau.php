<?php

$userName = '';
if (isset($_SESSION['prenom'])) {
    $userName = htmlspecialchars($_SESSION['prenom']);
}
?>
<body id="bodyHeader">   
    <header>
        <section id="titreSite">
            <article id="titre">
                <h1 id="titre1">Comité Monestiés</h1>
                <h2 id="titre2">Calendrier de réservation de la salle des fêtes</h2>
            <article>
        </section>
        <article class="connect" id="connectStatus" data-logged="<?php echo isset($_SESSION['connected_user']) ? '1' : '0'; ?>">
            <a class="co" href="/Projet-Calendrier-Reservation/models/connexion.php">Se connecter |</a>
            <a class="co" href="/Projet-Calendrier-Reservation/models/inscription.php">S'inscrire</a>
        </article>
        <script>
            const connectStatus = document.getElementById('connectStatus');
            const userName = "<?php echo $userName; ?>";
            if (connectStatus.dataset.logged === "1") {
                connectStatus.innerHTML = `<span id="bienvenu"> Bienvenue, ${userName} !</span> <br><a id="deco" href='/Projet-Calendrier-Reservation/models/membre.php'>Se déconnecter</a>`;
            }else {
                connectStatus.innerHTML = "Pas Bienvenue";
            }
            </script>
    </header>
<body> 
