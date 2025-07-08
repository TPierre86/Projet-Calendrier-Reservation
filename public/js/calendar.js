
const calendarEl = document.getElementById("calendar");
const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
const startTime = document.getElementById("startTime");
const endTime = document.getElementById("endTime");
const roomSelect = document.getElementById("roomSelect");
const saveBtn = document.getElementById("saveBtn");
const deleteBtn = document.getElementById("deleteBtn");
const recurrenceCheckbox = document.getElementById("recurrenceCheckbox");
const recurrenceWeeksInput = document.getElementById("recurrenceWeeks");
const recurrenceDaySelect = document.getElementById("recurrenceDay");
const recurrenceOptions = document.getElementById("recurrenceOptions");
const canCreate = window.canCreate;
const canEdit = window.canEdit;
const canDelete = window.canDelete;
const canCreateAdmin = window.canCreateAdmin;
const canEditAdmin = window.canEditAdmin;
const canDeleteAdmin = window.canDeleteAdmin;
const canComment = window.canComment;
const associationColors = {
  1: '#e74c3c', // rouge
  2: '#3498db', // bleu
  3: '#27ae60', // vert
  4: '#f39c12', // orange
  5: '#9b59b6', // violet
  6: '#1abc9c', // turquoise
  7: '#34495e', // gris foncé
  8: '#e67e22', // orange foncé
  9: '#c0392b'  // rouge foncé
};


// Variables pour stocker l'événement sélectionné ou en cours de modification
let currentEvent = null; //enregistre la selection fait par l'utilisateur
let selectedRangeStart = null; // en cas de selection multiple enregistre la date de début de l'évênement
let selectedRangeEnd = null; // en cas de selection multiple enregistre la date de fin de l'évênement

// Affiche/masque les options de récurrence
recurrenceCheckbox.addEventListener("change", () => { // fonction qui modifie le "display :none" dans la partie récurrence du form
recurrenceOptions.style.display = recurrenceCheckbox.checked ? "block" : "none"; 
});

// Initialisation du calendrier FullCalendar
window.calendar = new FullCalendar.Calendar(calendarEl, { // permet l'affichage du calendrier lors du lancement de la page
  initialView: "dayGridMonth", //vue par défault "grid" par mois
  locale: "fr", //configuer le calendrier en français
  firstDay : 1, // fais commencer le calendrier le lundi
  selectable: canCreate, // permet la selection des cases du calendrier pour créer des évênements
  headerToolbar: { // partie au dessus du calendrier
    left: "prev,next today",
    center: "title",
    right: "dayGridMonth,timeGridWeek,timeGridDay",
  },
  buttonText: { //bouton au dessus du calendrier
    today: "Aujourd’hui",
    month: "Mois",
    week: "Semaine",
    day: "Jour",
  },

  eventContent: function(arg) {
    // Crée un conteneur temporaire pour parser le HTML du title
        const container = document.createElement('span');
        container.innerHTML = arg.event.title; // Injecte le HTML du title (avec le <a>)
    // Récupère le nom de la salle entre crochets
        const title = arg.event.title;
        const roomMatch = title.match(/\[(.*?)\]/);
        const roomName = roomMatch ? roomMatch[1] : '';

    // Récupère le lien <a> s'il existe
      const link = container.querySelector('a');
        if (link) {

        link.onclick = function(e) {
      e.stopPropagation(); // Empêche l'ouverture de la modale
            // Récupère l'id de réservation
        const reservationId = link.getAttribute('data-id');
        fetch(`/projet-calendrier-reservation/database/getComments.php?reservation_id=${reservationId}`)
          .then(response => {
              if (!response.ok) throw new Error('Réponse réseau incorrecte');
              return response.json();
            })
          .then(comments => {
            if (!Array.isArray(comments)) {
            throw new Error('Données reçues non valides : pas un tableau');
            }

            const commentsData = document.getElementById('commentsData');
            commentsData.innerHTML = '';
            comments.forEach(comment => {
              commentsData.innerHTML += `
                <article class="comment">
                  <section class="commentHeader">
                    <strong class="commentAuthor">${comment.nom_utilisateur}:</strong>
                    <p class="commentText">${comment.comment}</p>
                  </section>
                    <span class="commentDate">${comment.heure_comment}</span>
                </article>
                `;
              });
            })
            .catch(error => {
    console.error("Erreur lors du chargement des commentaires :", error);
    alert("Erreur lors du chargement des commentaires.");
  });
        // Met à jour un champ caché dans la modale commentaire si besoin
        const input = document.querySelector('#filComments input[name="reservation_id"]');
        if (input) input.value = reservationId;
        // Ouvre la modale commentaire (Bootstrap 5)
        const filCommentsModal = new bootstrap.Modal(document.getElementById('filCommentsModal'));
        filCommentsModal.show(); 
      return false;
    };
      // Ajoute le nom de la salle avant le lien
    container.innerHTML = `<strong>${roomName}</strong> `;
    container.appendChild(link);
  } else {
    // Si pas de lien, affiche juste le nom de la salle
    container.textContent = roomName;
  }
  // Retourne le DOM custom à FullCalendar
  return { domNodes: [container] };
},

  // Lorsqu'on sélectionne un créneau (plage horaire uniquement)
  select: function (info) {
    if (!canCreate) {
      alert("Vous n'avez pas les droits pour créer un événement.");
      calendar.unselect();
      return;
    }
    currentEvent = null; // On prépare la création d'un nouvel événement (pas d'édition)
    let startDate = new Date(info.start); // Date de début sélectionnée
    let endDate = new Date(info.end);     // Date de fin sélectionnée

function formatDateLocal(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // mois de 0 à 11
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

selectedRangeStart = formatDateLocal(startDate);
selectedRangeEnd = formatDateLocal(new Date(endDate.getTime() - 24*60*60*1000));

    // Remplit automatiquement les heures
    startTime.value = info.start.toISOString().substring(11, 16); // Heure de début (HH:MM)
    endTime.value = info.end.toISOString().substring(11, 16);     // Heure de fin (HH:MM)

    // Réinitialise les champs de la modale pour repartir d'un formulaire vierge
    roomSelect.selectedIndex = 0;      // Remet la sélection de salle à zéro
    recurrenceCheckbox.checked = false;// Décoche la récurrence
    recurrenceOptions.style.display = "none"; // Masque les options de récurrence
    deleteBtn.style.display = "none";         // Cache le bouton de suppression (nouvel événement)

    // Affiche la fenêtre modale pour permettre à l'utilisateur de saisir les détails de la réservation
    eventModal.show();
  },
events: {
  url: '/Projet-Calendrier-Reservation/database/loadEvents.php',
  method: 'GET',
  failure: function() { alert('Erreur de chargement !'); },
  // eventDataTransform ici tu peux ajouter d'autres props si besoin, mais ce n'est pas obligatoire
},
eventDidMount: function(info) {
  let assocId = info.event.extendedProps.association_id;
  let color = associationColors[assocId] || '#607d8b'; // gris par défaut

  // Appliquer la couleur au style CSS de l'événement
  info.el.style.backgroundColor = color;
  info.el.style.borderColor = color;

  // Facultatif : forcer la couleur du texte si besoin
  info.el.style.color = 'white';
},
  /**
   * ! on aura surement un problème pour ajouter tel évênement à tel association
   */
});

// Lorsqu'on clique sur un événement existant 
/** 
 *? Fonction qui a pour but de modifier ou supprimer un événement existant*/
window.calendar.on('eventClick', function (info) { //fonction qui sers d'EventListener dans calendar
  if (!canEdit) {
    alert("vous n'avez pas le droit de modifier cette réservation.");
    return;
  }
  currentEvent = info.event; // Objet événement FullCalendar correspondant à l'événement cliqué par l'utilisateur
  document.querySelector('input[name="id_reservation"]').value = currentEvent.id;
    // Utilise les propriétés étendues pour remplir les champs
  document.getElementById('startDate').value = currentEvent.extendedProps.date_debut;
  document.getElementById('endDate').value = currentEvent.extendedProps.date_fin;
  document.getElementById('startTime').value = currentEvent.extendedProps.heure_debut;
  document.getElementById('endTime').value = currentEvent.extendedProps.heure_fin;
  document.getElementById('roomSelect').value = currentEvent.extendedProps.salle_id;


  // Masquer les options de récurrence lors d'une édition
  recurrenceCheckbox.checked = false;
  recurrenceOptions.style.display = "none";

  // Affiche le bouton de suppression
  deleteBtn.style.display = "inline-block";

  deleteBtn.style.display = canDelete ? 'inline-block' : 'none';

  // Affiche la modale
  eventModal.show();
});

// Affiche le calendrier
window.calendar.render();


// Ajouter un nouveau commentaire

  document.getElementById('newComment').addEventListener('submit', function (e) {
    e.preventDefault();

    const commentText = document.getElementById('comment').value.trim();
    const reservationId = document.getElementById('reservation_id').value;

    if (commentText === '') {
        alert("Le commentaire est vide !");
        return;
    }

    fetch('/projet-calendrier-reservation/database/addComment.php?reservation_id='+ reservationId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            reservation_id: reservationId,
            comment: commentText
        })
    })
    .then(response => response.json())
    .then(data => {

      
   
        if (data.success) {
            // Ajout du nouveau commentaire dans la liste
            const commentsContainer = document.getElementById('commentsData');
            const newComment = document.createElement('article');
            newComment.innerHTML = `
            <article class="comment">
              <section class="commentHeader">
                    <strong class="commentAuthor">${data.nom_utilisateur}</strong>
                    <p class="commentText"> ${data.comment}</p>
                </section>
                    <span class="commentDate">${data.heure_comment}</span>
                </article>
            `;
            commentsContainer.prepend(newComment);
            document.getElementById('comment').value = '';
        } else {
            alert("Erreur : " + (data.error || 'Impossible d’ajouter le commentaire.'));
        }
    })
    .catch(error => {
        console.error('Erreur réseau :', error);
        alert("Erreur réseau.");
    });
  }),

// Enregistrement d'un nouvel événement ou mise à jour
saveBtn.addEventListener("click", (e) => {
  e.preventDefault();
  if (!canCreate && !canEdit) {
    alert("Vous n'avez pas les droits pour créer ou modifier un événement.");
    return;
  }
  const start = startTime.value;
  const end = endTime.value;
  const room = roomSelect.value;

  // *! Récupère les dates depuis les inputs de la modale (toujours prioritaire)
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;

  // Vérifie les champs obligatoires
  if (!start || !end || !room || !startDate || !endDate) {
    alert("Merci de remplir tous les champs.");
    return;
  }
  if (start >= end) {
    alert("L'heure de fin doit être après l'heure de début.");
    return;
  }

  // Informations sur la récurrence
  const recurrence = recurrenceCheckbox.checked;
  const recurrenceWeeks = recurrenceWeeksInput.value;

  fetch('/Projet-Calendrier-Reservation/database/addEvent.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      startDate: startDate,
      endDate: endDate,
      startTime: start,
      endTime: end,
      roomSelect: room,
      recurrence: recurrence,
      recurrenceWeeks: recurrenceWeeks
      // ajoute utilisateur_id si besoin
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      window.calendar.refetchEvents();
      eventModal.hide();
    } else {
      alert("Erreur lors de l'enregistrement !");
    }
  });
});

// Suppression d'un événement existant
deleteBtn.addEventListener("click", () => {
  if (!canDelete) {
    alert("Vous n'avez pas les droits pour supprimer cet événement.");
    return;
  }

  if (currentEvent) {
    currentEvent.remove();
    eventModal.hide();
  }
});


//button pour exporter les réservations du calendrier en tableau Excell//
document.addEventListener('DOMContentLoaded', function () {
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!window.calendar) {
                alert("Le calendrier n'est pas chargé !");
                return;
            }
            const events = window.calendar.getEvents();
            if (events.length === 0) {
                alert("Aucune réservation à exporter !");
                return;
            }
            const data = [
                ["Début", "Fin", "Salle"]
            ];
            events.forEach(ev => {
                  const match = ev.title.match(/^\[(.*?)\]\s*(.*)$/);
    const salle = match ? match[1] : "";

        const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };

    const startStr = new Date(ev.start).toLocaleString("fr-FR", options);
    const endStr = new Date(ev.end).toLocaleString("fr-FR", options);
                data.push([
                    startStr,
                    endStr,
                    salle,
                ]);
            });
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Réservations");
    XLSX.writeFile(wb, "reservations.xlsx");
        });
    }
})

//button pour exporter les réservations du calendrier sur googleCalendar//
document.addEventListener('DOMContentLoaded', function () {
    const gcalExportBtn = document.getElementById('gcalExportBtn');

    if (gcalExportBtn) {
        gcalExportBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (!window.calendar) {
                alert("Le calendrier n'est pas chargé !");
                return;
            }

            const events = window.calendar.getEvents();

            if (events.length === 0) {
                alert("Aucune réservation à exporter !");
                return;
            }

            events.forEach(ev => {
                const match = ev.title.match(/^\[(.*?)\]\s*(.*)$/);
                const salle = match ? match[1] : "";

                // Dates au format ISO pour Google Calendar (UTC, sans millisecondes)
                const start = new Date(ev.start).toISOString().replace(/[-:]|\.\d{3}/g, '');
                const end = new Date(ev.end).toISOString().replace(/[-:]|\.\d{3}/g, '');

                // Générer le lien
                const url = `https://calendar.google.com/calendar/render?action=TEMPLATE` +
                    `&text=${encodeURIComponent('Réservation: ' + salle)}` +
                    `&location=${encodeURIComponent(salle)}` +
                    `&dates=${start}/${end}`;

                // Ouvrir dans un nouvel onglet
                window.open(url, '_blank');
            });
        });
    }
});

