// Active le formulaire de creation d'un nouveau association
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
                boutonNouveau.textContent = "Nouvelle association";
            }
        });
    }
});
//Active le formulaire de modification d'une association
  // Gérer les clics sur les boutons "Modifier"

  const modifierButtons = document.querySelectorAll(".modifier-btn");
  const formModifier = document.getElementById("form-modifier");

  if (formModifier && modifierButtons) {
    modifierButtons.forEach(button => {
      button.addEventListener("click", () => {
        
        

        // Remplir le formulaire avec les infos association
        formModifier.querySelector("input[name='name']").value = button.dataset.name;

        // Ajouter un champ caché pour l'ID association
        let idInput = formModifier.querySelector("input[name='id_association']");
        if (!idInput) {
          idInput = document.createElement("input");
          idInput.type = "hidden";
          idInput.name = "id_salle";
          formModifier.appendChild(idInput);
        }
        idInput.value = button.dataset.id;
        //Afficher le formulaire
        formModifier.style.display = "block";
        window.scrollTo({ top: formModifier.offsetTop, behavior: "smooth" });
    });
    });
  }
