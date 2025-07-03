// Active le formulaire de creation d'un nouveau salle
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
                boutonNouveau.textContent = "Nouvelle salle";
            }
        });
    }
});
//Active le formulaire de modification d'une salle
  // Gérer les clics sur les boutons "Modifier"

  const modifierButtons = document.querySelectorAll(".modifier-btn");
  const formModifier = document.getElementById("form-modifier");

  if (formModifier && modifierButtons) {
    modifierButtons.forEach(button => {
      button.addEventListener("click", () => {
        
        

        // Remplir le formulaire avec les infos salle
        formModifier.querySelector("input[name='name']").value = button.dataset.name;

        // Ajouter un champ caché pour l'ID salle
        let idInput = formModifier.querySelector("input[name='id_salle']");
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
