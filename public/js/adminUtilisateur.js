document.addEventListener('DOMContentLoaded', () => {
    const boutonNouveau = document.getElementById('nouveau');
    const formulaire = document.getElementById('form-nouveau');

    if (boutonNouveau && formulaire) {
        boutonNouveau.addEventListener('click', () => {
            // Toggle l'affichage du formulaire
            if (formulaire.style.display === 'none') {
                formulaire.style.display = 'block';
                boutonNouveau.textContent = "Annuler";
            } else {
                formulaire.style.display = 'none';
                boutonNouveau.textContent = "Nouveau Utilisateur";
            }
        });
    }
});