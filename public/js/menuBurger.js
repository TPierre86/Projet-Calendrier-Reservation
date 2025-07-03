const menuOpen = document.getElementById('nav-menu-open');
const iconOpen = document.getElementById('btn-open-menu');
const iconClose = document.getElementById('btn-close-menu');

// Affiche le menu burger
function showResponsiveMenu() {
    menuOpen.style.left = "0";
}

// Cache le menu burger
function hideResponsiveMenu() {
    menuOpen.style.left = "-250px";
}

// Ouvre le menu quand on clique sur l'icône burger
iconOpen.addEventListener('click', showResponsiveMenu);

// Ferme le menu quand on clique sur la croix
iconClose.addEventListener('click', hideResponsiveMenu);
