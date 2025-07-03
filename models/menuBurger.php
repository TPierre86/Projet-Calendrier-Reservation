<section id="burger-menu">
    <nav id="nav-menu-open">
        <button id="btn-close-menu"><i class="fa-solid fa-xmark"></i></button>
        <ul>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire') { ?>
                <li><a class="list-menu" href="">Gestion des Associations</a></li>
                <li><a class="list-menu" href="">Gestion des membres des Associations</a></li>
                <li><a class="list-menu" href="">Gestion des Utilisateurs</a></li>
                <li><a class="list-menu" href="">Gestion des Salles</a></li>
            <?php } else if (isset($_SESSION['role']) && $_SESSION['role'] === 'president') { ?>
                <li><a class="list-menu" href="">Voir les membres de l'association</a></li>
            <?php } ?>
        </ul>
    </nav>
    <article href="" id="nav-menu-close">
        <button id="btn-menu"><i class="fa-solid fa-bars"></i></button>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </article>
</section>
