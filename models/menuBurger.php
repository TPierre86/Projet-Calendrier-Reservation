<?php
require_once('index.php');
?>


<nav id="nav-menu-open">
    <button id="btn-close-menu"><i class="fa-solid fa-xmark"></i></button>
    <ul>
        <?php if (isset($_SESSION['profil']) && $_SESSION['profil'] === 'Gestionnaire'): ?>
        <li><a class="list-menu" href="/Projet-Calendrier-Reservation/models/panneauAdmin/gestionUtilisateurs.php">Gestion des Utilisateurs</a></li>
        <li><a class="list-menu" href="/Projet-Calendrier-Reservation/models/panneauAdmin/gestionAssociations.php">Gestion des Associations</a></li>
        <li><a class="list-menu" href="/Projet-Calendrier-Reservation/models/panneauAdmin/gestionSalles.php">Gestion des Salles</a></li>
        <?php elseif (isset($_SESSION['profil']) && $_SESSION['profil'] === "Président d'association"): ?>
        <li><a class="list-menu" href="/Projet-Calendrier-Reservation/models/panneauAdmin/listingMembres.php">Listing des membres</a></li>
        <?php endif; ?>
    </ul>
</nav>
<article id="nav-menu-close">
    <button id="btn-open-menu"><i class="fa-solid fa-bars"></i></button>
</article>

<?php require_once('templates/footer.php');?>