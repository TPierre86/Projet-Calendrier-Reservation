const calendarEl = document.getElementById("calendar");
const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
const startTime = document.getElementById("startTime");
const endTime = document.getElementById("endTime");
const commentInput = document.getElementById("commentInput");
const roomSelect = document.getElementById("roomSelect");
const saveBtn = document.getElementById("saveBtn");
const deleteBtn = document.getElementById("deleteBtn");
const recurrenceCheckbox = document.getElementById("recurrenceCheckbox");
const recurrenceWeeksInput = document.getElementById("recurrenceWeeks");
const recurrenceDaySelect = document.getElementById("recurrenceDay");
const recurrenceOptions = document.getElementById("recurrenceOptions");

// Variables pour stocker l'événement sélectionné ou en cours de modification
let currentEvent = null; //enregistre la selection fait par l'utilisateur
let selectedRangeStart = null; // en cas de selection multiple enregistre la date de début de l'évênement
let selectedRangeEnd = null; // en cas de selection multiple enregistre la date de fin de l'évênement

// Affiche/masque les options de récurrence
recurrenceCheckbox.addEventListener("change", () => { // fonction qui modifie le "display :none" dans la partie récurrence du form
recurrenceOptions.style.display = recurrenceCheckbox.checked ? "block" : "none"; 
});

// Initialisation du calendrier FullCalendar
const calendar = new FullCalendar.Calendar(calendarEl, { // permet l'affichage du calendrier lors du lancement de la page
  initialView: "dayGridMonth", //vue par défault "grid" par mois
  locale: "fr", //configuer le calendrier en français
  firstDay : 1, // fais commencer le calendrier le lundi
  selectable: true, // permet la selection des cases du calendrier pour créer des évênements
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

  // Lorsqu'on sélectionne un créneau (plage horaire uniquement)
  select: function (info) {
    currentEvent = null; // On prépare la création d'un nouvel événement (pas d'édition)
    let startDate = new Date(info.start); // Date de début sélectionnée
    let endDate = new Date(info.end);     // Date de fin sélectionnée

function formatDateLocal(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // mois de 0 à 11
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

// let endDate = new Date(info.end);
// endDate.setDate(endDate.getDate() - 1); // soustrait 1 jour pour avoir la date réelle de fin
// selectedRangeEnd = formatDateLocal(endDate);

selectedRangeStart = formatDateLocal(startDate);
selectedRangeEnd = formatDateLocal(endDate);    // Stocke la date de fin au même format

    // Remplit automatiquement les heures
    startTime.value = info.start.toISOString().substring(11, 16); // Heure de début (HH:MM)
    endTime.value = info.end.toISOString().substring(11, 16);     // Heure de fin (HH:MM)

    // Réinitialise les champs de la modale pour repartir d'un formulaire vierge
    commentInput.value = "";           // Vide le commentaire
    roomSelect.selectedIndex = 0;      // Remet la sélection de salle à zéro
    recurrenceCheckbox.checked = false;// Décoche la récurrence
    recurrenceOptions.style.display = "none"; // Masque les options de récurrence
    deleteBtn.style.display = "none";         // Cache le bouton de suppression (nouvel événement)

    // Affiche la fenêtre modale pour permettre à l'utilisateur de saisir les détails de la réservation
    eventModal.show();
  },
  events: '/Projet-Calendrier-Reservation/database/loadEvents.php', 
  /**
   * ! on aura surement un problème pour ajouter tel évênement à tel association
   */
});

// Lorsqu'on clique sur un événement existant 
/** 
 *? Fonction qui a pour but de modifier ou supprimer un événement existant*/
calendar.on('eventClick', function (info) { //fonction qui sers d'EventListener dans calendar
  currentEvent = info.event; // Objet événement FullCalendar correspondant à l'événement cliqué par l'utilisateur
  selectedRangeStart = currentEvent.startStr.substring(0, 10); //garde uniquement la date de début en format YYYY-MM-DD
  selectedRangeEnd = selectedRangeStart;

  commentInput.value = currentEvent.title.replace(/\[.*?\]\s*/, "");// retire le nom de la salle du titre de l'évênement

  // Remplit les heures
  startTime.value = currentEvent.start.toISOString().substring(11, 16); // Récupère l'heure de début au format HH:MM
  endTime.value = currentEvent.end
  currentEvent.end.toISOString().substring(11, 16) // Récupère l'heure de fin au format HH:MM
  // Active les champs horaires
  startTime.disabled = false;
  endTime.disabled = false;

  /** Récupère la salle depuis le titre ([Salle])
   *! fonction à modifier / supprimer à terme pour aller chercher directement dans la BdD le nom des salles  */ 
  const roomMatch = currentEvent.title.match(/\[(.*?)\]/);
  if (roomMatch) {
    const roomName = roomMatch[1];
    for (let i = 0; i < roomSelect.options.length; i++) {
      if (roomSelect.options[i].text === roomName) {
        roomSelect.selectedIndex = i;
        break;
      }
    }
  }


  // Masquer les options de récurrence lors d'une édition
  recurrenceCheckbox.checked = false;
  recurrenceOptions.style.display = "none";

  // Affiche le bouton de suppression
  deleteBtn.style.display = "inline-block";

  // Affiche la modale
  eventModal.show();
});

// Affiche le calendrier
calendar.render();

// Enregistrement d'un nouvel événement ou mise à jour
saveBtn.addEventListener("click", (e) => {
  e.preventDefault();
  const start = startTime.value;
  const end = endTime.value;
  const comment = commentInput.value.trim();
  const room = roomSelect.value;

  // Vérifie les champs obligatoires
  if (!start || !end || !comment || !room) {
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
      startDate: selectedRangeStart,
      endDate: selectedRangeEnd,
      startTime: start,
      endTime: end,
      commentInput: comment,
      roomSelect: room,
      recurrence: recurrence,
      recurrenceWeeks: recurrenceWeeks
      // ajoute utilisateur_id si besoin
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      calendar.refetchEvents();
      eventModal.hide();
    } else {
      alert("Erreur lors de l'enregistrement !");
    }
  });
});

// Suppression d'un événement existant
deleteBtn.addEventListener("click", () => {
  if (currentEvent) {
    const eventId = currentEvent.id;

    fetch('/Projet-Calendrier-Reservation/database/deleteEvents.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: eventId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        currentEvent.remove(); // Supprime du calendrier
        eventModal.hide();     // Ferme la modale
      } else {
        alert("Erreur lors de la suppression : " + (data.error || ""));
      }
    })
    .catch(error => {
      console.error("Erreur réseau :", error);
      alert("Une erreur réseau s’est produite.");
    });
  }
});