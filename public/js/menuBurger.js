const menuOpen = document.getElementById('nav-menu-open');
const iconOpen = document.getElementById('btn-open-menu');
const iconClose = document.getElementById('btn-close-menu');

// Affiche le menu burger
function showResponsiveMenu() {
    menuOpen.style.left = "0";
    if (iconOpen) iconOpen.style.display = "none"; // Cache le bouton burger

}

// Cache le menu burger
function hideResponsiveMenu() {
    menuOpen.style.left = "-250px";
    if (iconOpen) iconOpen.style.display = "inline-block";
}

// Ouvre le menu quand on clique sur l'icône burger
if(iconOpen) iconOpen.addEventListener('click', showResponsiveMenu);

// Ferme le menu quand on clique sur la croix
if(iconClose) iconClose.addEventListener('click', hideResponsiveMenu);
