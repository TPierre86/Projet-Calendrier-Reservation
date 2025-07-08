document.getElementById('envoyer').addEventListener('click', function(e) {
  e.preventDefault();
  const input = document.getElementById('newCommentInput');
  const comment = input.value;
  const reservation_id = document.querySelector('input[name="reservation_id"]').value;

  if (comment.trim() === '') return;

  const formData = new FormData();
  formData.append('action', 'envoyer');
  formData.append('newCommentInput', comment);
  formData.append('reservation_id', reservation_id);

  fetch('', {
    method: 'POST',
    body: formData
  }).then(response => {
    if (response.ok) {
      location.reload(); // ou dynamiquement ajouter le commentaire
    } else {
      alert("Erreur lors de l'ajout du commentaire.");
    }
  });
});