// Active le formulaire de creation d'un nouveau utilisateur
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
//Active le formulaire de modification d'un utilisateur
  // Gérer les clics sur les boutons "Modifier"

  const modifierButtons = document.querySelectorAll(".modifier-btn");
  const formModifier = document.getElementById("form-modifier");

  if (formModifier && modifierButtons) {
    modifierButtons.forEach(button => {
      button.addEventListener("click", () => {
        
        

        // Remplir le formulaire avec les infos utilisateur
        formModifier.querySelector("input[name='name']").value = button.dataset.name;
        formModifier.querySelector("input[name='firstName']").value = button.dataset.firstname;
        formModifier.querySelector("input[name='mail']").value = button.dataset.email;
        formModifier.querySelector("input[name='tel']").value = button.dataset.tel;
        formModifier.querySelector("input[name='pwd']").value = button.dataset.password;
        const associationSelect = formModifier.querySelector("select[name='association_id']");
        const associationValue = button.dataset.associationId;
        
        // Debug : afficher la valeur et les options
        console.log('Valeur association à sélectionner:', associationValue);
        console.log('Options disponibles:', Array.from(associationSelect.options).map(o => o.value));

        // Forcer la sélection directement
        associationSelect.value = associationValue;
        // Si la valeur n'est pas trouvée, on sélectionne la première option valide
        if (associationSelect.value !== associationValue) {
            associationSelect.selectedIndex = 0;
        }



        // Ajouter un champ caché pour l'ID utilisateur
        let idInput = formModifier.querySelector("input[name='id_utilisateur']");
        if (!idInput) {
          idInput = document.createElement("input");
          idInput.type = "hidden";
          idInput.name = "id_utilisateur";
          formModifier.appendChild(idInput);
        }
        idInput.value = button.dataset.id;
        //Afficher le formulaire
        formModifier.style.display = "block";
        window.scrollTo({ top: formModifier.offsetTop, behavior: "smooth" });
    });
    });
  }
