const calendarEl = document.getElementById("calendar");
const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
const startTime = document.getElementById("startTime");
const endTime = document.getElementById("endTime");
const roomSelect = document.getElementById("roomSelect");
const saveBtn = document.getElementById("saveBtn");
const saveModifBtn = document.getElementById("saveModifBtn");
const deleteBtn = document.getElementById("deleteBtn");
const recurrenceCheckbox = document.getElementById("recurrenceCheckbox");
const recurrenceWeeksInput = document.getElementById("recurrenceWeeks");
const recurrenceDaySelect = document.getElementById("recurrenceDay");
const recurrenceOptions = document.getElementById("recurrenceOptions");
const menageOptions = document.getElementById("menageOptions");
const recurrenceCheckboxSection = document.getElementById("recurrenceCheckboxSection");
const reservationAssociation = document.getElementById("reservationAssociation");
const canCreate = window.canCreate;
const canEdit = window.canEdit;
const canDelete = window.canDelete;
const canComment = window.canComment;
const canDownload = window.canDownload;
const role = window.currentUser.role;
const userAssocId = window.currentUser.associationId;
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

function canEditEvent(event) {
const eventAssocId = event.extendedProps.association_id;
    return role === 'Gestionnaire' || (role === "Président d'association" && userAssocId && userAssocId == eventAssocId);
}

function canDeleteEvent(event) {
const eventAssocId = event.extendedProps.association_id;
    return role === 'Gestionnaire' || (role === "Président d'association" && userAssocId && userAssocId == eventAssocId);
}

function canCreateEventForAssoc(assocId) {
    return role === 'Gestionnaire' || (role === "Président d'association" && userAssocId && userAssocId == assocId);
}

// Variables pour stocker l'événement sélectionné ou en cours de modification
let currentEvent = null; //enregistre la selection fait par l'utilisateur
let selectedRangeStart = null; // en cas de selection multiple enregistre la date de début de l'évênement
let selectedRangeEnd = null; // en cas de selection multiple enregistre la date de fin de l'évênement

// Affiche/masque les options de récurrence
if (recurrenceCheckbox) {
    recurrenceCheckbox.addEventListener("change", () => {
    if (recurrenceOptions) {
    recurrenceOptions.style.display = recurrenceCheckbox.checked ? "block" : "none";
    }});
}

// Initialisation du calendrier FullCalendar
window.calendar = new FullCalendar.Calendar(calendarEl, { // permet l'affichage du calendrier lors du lancement de la page
  initialView: "dayGridMonth", //vue par défault "grid" par mois
  locale: "fr", //configuer le calendrier en français
  firstDay : 1, // fais commencer le calendrier le lundi
  selectable: window.canCreate, // permet la selection des cases du calendrier pour créer des évênements
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
        const link = container.querySelector('.comment-link');
        const attachment = container.querySelector('.attachment-link');
        if (link) {

        link.onclick = function(e) {
      e.stopPropagation(); // Empêche l'ouverture de la modale
            // DROITS COMMENTAIRE
            // 1. Interdire aux visiteurs
            if (role === 'visitor') {
              alert("Vous devez être connecté pour voir ou ajouter des commentaires.");
              return false;
            }
            // 2. Interdire aux membres d'association d'accéder aux commentaires d'une autre association
            const eventAssocId = arg.event.extendedProps.association_id;
            if (
              role === 'Membre' &&
              userAssocId &&
              eventAssocId &&
              userAssocId != eventAssocId
            ) {
              alert("Vous ne pouvez commenter que les réservations de votre association.");
              return false;
            }
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
    // Ajoute un espace visuel entre les deux liens
    const space = document.createElement('span');
    space.style.display = 'inline-block';
    space.style.width = '10px';
    container.appendChild(space);
    container.appendChild(attachment);
    // Dans eventContent, affiche la checkbox si menageCheckbox est vrai
    if (arg.event.extendedProps.menageCheckbox) {
        const menageCheckbox = document.createElement('input');
        menageCheckbox.type = 'checkbox';
        menageCheckbox.checked = !!arg.event.extendedProps.menage;
        menageCheckbox.disabled = (role !== "Ménage");
        menageCheckbox.style.marginLeft = '8px';
        const menageLabel = document.createElement('label');
        menageLabel.textContent = 'Ménage';
        menageLabel.style.marginLeft = '4px';
        container.appendChild(menageCheckbox);
        container.appendChild(menageLabel);

        if (role === "Ménage") {
        menageCheckbox.addEventListener('change', function() {
            fetch('/Projet-Calendrier-Reservation/models/setMenage.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${encodeURIComponent(arg.event.id)}&checked=${menageCheckbox.checked ? 1 : 0}`
            })
            .then(res => res.json())
            .then(data => {
            if (data.success) {
                window.calendar.refetchEvents();
            } else {
                alert('Erreur lors de la mise à jour du ménage');
            }
            });
        });
        }
    }

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
    selectedRangeEnd = formatDateLocal(new Date(endDate.getTime() - 24 * 60 * 60 * 1000));

  // Remplit automatiquement les heures
    startTime.value = info.start.toISOString().substring(11, 16); // Heure de début (HH:MM)
    endTime.value = info.end.toISOString().substring(11, 16);     // Heure de fin (HH:MM)

  // Réinitialise les champs de la modale pour repartir d'un formulaire vierge
    roomSelect.selectedIndex = 0;          // Remet la sélection de salle à zéro
    recurrenceCheckbox.checked = false;    // Décoche la récurrence
    recurrenceOptions.style.display = "none"; // Masque les options de récurrence
    deleteBtn.style.display = "none";     // Cache le bouton de suppression (nouvel événement)

  // Gérer la visibilité des options selon le rôle
    if (recurrenceCheckbox && recurrenceOptions && menageOptions) {
    recurrenceCheckbox.style.display = (role === "Gestionnaire") ? "inline-block" : "none";
    if (role !== "Gestionnaire") recurrenceCheckbox.checked = false;
    }
    if (recurrenceOptions) {
    recurrenceOptions.style.display = (role === "Gestionnaire") ? "block" : "none";
    }
    if (menageOptions) {
    const menageCheckbox = document.getElementById('menageCheckbox');
    if (menageCheckbox) menageCheckbox.checked = false;
    menageOptions.style.display = (role === "Gestionnaire") ? "block" : "none";
    } 
    if (deleteBtn) {
    deleteBtn.style.display = "none";
    }
    if (recurrenceCheckboxSection) recurrenceCheckboxSection.style.display = (role === "Gestionnaire") ? "block" : "none";
    if (reservationAssociation) reservationAssociation.style.display = (role === "Gestionnaire") ? "block" : "none";
    if (recurrenceOptions) recurrenceOptions.style.display = (role === "Gestionnaire") ? "block" : "none";
    if (menageOptions) menageOptions.style.display = (role === "Gestionnaire") ? "block" : "none";

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

    if (info.event.extendedProps.recurrence) {
    const icon = document.createElement('i');
    icon.className = 'fa-solid fa-repeat'; // 📛 Icône FontAwesome "repeat"
    icon.style.marginRight = '5px';
    icon.title = 'Événement récurrent';

    const titleElement = info.el.querySelector('.fc-event-title') || info.el;
    if (titleElement) {
        titleElement.prepend(icon);
    }
    }
},


});


// Lorsqu'on clique sur un événement existant 
/** 
 *? Fonction qui a pour but de modifier ou supprimer un événement existant*/
window.calendar.on('eventClick', function (info) {
    if (role === "Ménage") {
    return;
    }
    if (!canEditEvent(info.event)) {
    alert("Vous n'avez pas le droit de modifier cette réservation.");
    return;
    }
  currentEvent = info.event; // Objet événement FullCalendar correspondant à l'événement cliqué par l'utilisateur
  // Récupère les infos de l'événement
    const props = currentEvent.extendedProps;
    document.getElementById("id_reservation").value = currentEvent.id;
    console.log("ID de réservation sélectionné :", currentEvent.id);
    // Utilise les propriétés étendues pour remplir les champs
    document.getElementById('startDate').value = currentEvent.extendedProps.date_debut;
    document.getElementById('endDate').value = currentEvent.extendedProps.date_fin;
    document.getElementById('startTime').value = currentEvent.extendedProps.heure_debut;
    document.getElementById('endTime').value = currentEvent.extendedProps.heure_fin;
    document.getElementById('roomSelect').value = currentEvent.extendedProps.salle_id;

  // Affiche uniquement si rôle = 'Gestionnaire'
    if (role === "Gestionnaire") {
        if (recurrenceOptions) {
            recurrenceOptions.style.display = recurrenceCheckbox.checked ? "block" : "none";
        }
        if (recurrenceCheckboxSection) recurrenceCheckboxSection.style.display = "block";
        if (reservationAssociation) {
            reservationAssociation.style.display = "block";
            // Sélectionne la première association si aucune n'est sélectionnée
            const select = document.getElementById("id_association");
            if (select && (!select.value || select.value === "0")) {
                if (select.options.length > 1) {
                    select.selectedIndex = 1; // saute l'option --Choisir Association--
                }
            }
        }
    } else {
        if (recurrenceCheckboxSection) recurrenceCheckboxSection.style.display = "none";
        if (recurrenceOptions) recurrenceOptions.style.display = "none";
        if (recurrenceCheckbox) recurrenceCheckbox.checked = false;
        if (reservationAssociation) reservationAssociation.style.display = "none";
    }
    if (menageOptions) menageOptions.style.display = "none";

  // Affiche ou cache le bouton supprimer selon droits
    deleteBtn.style.display = canDeleteEvent(currentEvent) ? 'inline-block' : 'none';

  //Afficher bouton enregistrer les modification

    saveBtn.style.display = "none";
  saveModifBtn.style.display = "inline-block"; // Affiche le bouton de modification

  // Affiche le bouton de suppression

deleteBtn.style.display = canDeleteEvent(currentEvent) ? 'inline-block' : 'none';

    eventModal.show();
});



// Affiche le calendrier
window.calendar.render();

document.addEventListener("DOMContentLoaded", function () {
    const recurrenceCheckbox = document.getElementById("recurrenceCheckbox");
    const recurrenceOptions = document.getElementById("recurrenceOptions");

    if (recurrenceCheckbox && recurrenceOptions) {
    recurrenceCheckbox.addEventListener("change", () => {
        recurrenceOptions.style.display = recurrenceCheckbox.checked ? "block" : "none";
    });
    }
});

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
            alert("Erreur : " + (data.error || 'Impossible d’ajouter le commentaire, veuillez vous connecter.'));
        }
    })
    .catch(error => {
        console.error('Erreur réseau :', error);
        alert("Erreur réseau.");
    });
    }),

// Enregistrement d'un nouvel événement
saveBtn.addEventListener("click", (e) => {
    e.preventDefault();
    if (currentEvent && !canEditEvent(currentEvent)) {
    alert("Vous n'avez pas les droits pour modifier cet événement.");
    return;
    }

    if (!currentEvent && !canCreate) {
    alert("Vous n'avez pas les droits pour créer un événement.");
    return;
    }
    if (saveBtn) {
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
  // const newStart = new Date(`${startDate}T${start}`);
  // const newEnd = new Date(`${endDate}T${end}`);

const events = window.calendar.getEvents();

const newStart = new Date(`${startDate}T${start}`);
const newEnd = new Date(`${endDate}T${end}`);

const conflict = events.some(ev => {
  // Si on édite un événement, on ne le compare pas à lui-même
    if (currentEvent && ev === currentEvent) return false;

    const sameRoom = ev.extendedProps.salle_id == room;
    const evStart = new Date(ev.start);
    const evEnd = new Date(ev.end);

    return sameRoom && newStart < evEnd && newEnd > evStart;
});

if (conflict) {
    alert("Il existe déjà une réservation sur ce créneau et cette salle.");
    return;
}

if(role === 'Gestionnaire'){
  association_id = document.getElementById("id_association").value;
} else {
  association_id = userAssocId; // Utilise l'association de l'utilisateur connecté
}
  
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
      recurrenceWeeks: recurrenceWeeks,
      menage: document.getElementById('menageCheckbox').checked,
      association_id: association_id
    })
    })

    .then(response => response.json())
    .then(data => {
    if (data.success) {
        window.calendar.refetchEvents();
        eventModal.hide();
      saveBtn.value = "enregistrer"; // Réinitialise le bouton pour une nouvelle création
    } else {
        alert("Erreur lors de l'enregistrement !");
    }
    });
}
});
// Enregistrement d'une modification d'événement
saveModifBtn.addEventListener("click", (e) => {
    e.preventDefault();
    if (!canCreate && !canEdit) {
    alert("Vous n'avez pas les droits pour créer ou modifier un événement.");
    return;
    }
if (saveModifBtn) {
  // Modification d'un événement existant
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
    const id_reservation = document.getElementById("id_reservation").value;

    if(role === 'Gestionnaire') {
      association_id = document.getElementById("id_association").value;
      if (!association_id || association_id === '0') {
        alert("Merci de sélectionner une association.");
        return;
      }
    } else {
      association_id = userAssocId; // Utilise l'association de l'utilisateur connecté
    }
  fetch('/Projet-Calendrier-Reservation/database/updateEvent.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        id_reservation: id_reservation,
        startDate: startDate,
        endDate: endDate,
        startTime: start,
        endTime: end,
      attachments: null, // Conserver l'attachment si nécessaire
      roomSelect: room,
      recurrent: recurrence,
      association_id: association_id,
    })
    })
    .then(response => response.json())
    .then(data => {
    if (data.success) {
        alert('Réservation enregistrée');

    // Optionnel : reset form
    document.getElementById('formulaire').reset();
    saveBtn.style.display = "inline-block"; // Affiche le bouton de création
    saveModifBtn.style.display = "none"; // Cache le bouton de modification
        window.calendar.refetchEvents();
        eventModal.hide();
    } else {
        alert("Erreur lors de la modification !");
    }
    });
    }
});
// Suppression d'un événement existant
deleteBtn.addEventListener("click", () => {
    if (!currentEvent || !canDeleteEvent(currentEvent)) {
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