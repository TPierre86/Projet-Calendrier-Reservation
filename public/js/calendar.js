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
window.calendar = new FullCalendar.Calendar(calendarEl, { // permet l'affichage du calendrier lors du lancement de la page
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

    selectedRangeStart = startDate.toISOString().slice(0, 10); // Stocke la date de début au format AAAA-MM-JJ
    selectedRangeEnd = endDate.toISOString().slice(0, 10);     // Stocke la date de fin au même format

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
window.calendar.render();

// Lorsqu'on clique sur un événement existant 
/** 
 *? Fonction qui a pour but de modifier ou supprimer un événement existant*/
window.calendar.on('eventClick', function (info) { //fonction qui sers d'EventListener dans calendar
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
window.calendar.render();

// Enregistrement d'un nouvel événement ou mise à jour
saveBtn.addEventListener("click", () => {
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
  const recurrenceWeeks = parseInt(recurrenceWeeksInput.value);
  const targetDay = parseInt(recurrenceDaySelect.value);

  // Construction des dates ISO
  const baseStartDate = new Date(selectedRangeStart);
  const baseEndDate = new Date(selectedRangeEnd);
  const startTimeStr = "T" + start;
  const endTimeStr = "T" + end;

  // Si on modifie un événement existant
  if (currentEvent) {
    currentEvent.setProp("title", `[${room}] ${comment}`);
    // Utilise la date d'origine de l'événement pour start et end
    const eventDate = currentEvent.startStr.substring(0, 10);
    currentEvent.setStart(eventDate + startTimeStr);
    currentEvent.setEnd(eventDate + endTimeStr);
  } else {
    // Cas d'une récurrence sur plusieurs semaines
    if (recurrence) {
      let recurDate = new Date(baseStartDate);
      // Trouve le premier jour correspondant à la récurrence
      while (recurDate.getDay() !== targetDay) {
        recurDate.setDate(recurDate.getDate() + 1);
      }

      // Ajoute un événement pour chaque semaine
      for (let i = 0; i < recurrenceWeeks; i++) {
        const d = new Date(recurDate);
        d.setDate(d.getDate() + i * 14);
        const dStr = d.toISOString().slice(0, 10);
        window.calendar.addEvent({
          title: `[${room}] ${comment}`,
          start: dStr + startTimeStr,
          end: dStr + endTimeStr,
          allDay: false,
        });
      }
    } else {
      // Ajout d’un événement simple (pas récurrent)
      window.calendar.addEvent({
        title: `[${room}] ${comment}`,
        start: selectedRangeStart + startTimeStr,
        end: selectedRangeStart + endTimeStr,
        allDay: false,
      });
    }
  }
  // Ferme la modale après enregistrement
  eventModal.hide();
});

// Suppression d'un événement existant
deleteBtn.addEventListener("click", () => {
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
                ["Début", "Fin", "Salle", "Commentaire"]
            ];
            events.forEach(ev => {
                  const match = ev.title.match(/^\[(.*?)\]\s*(.*)$/);
    const salle = match ? match[1] : "";
    const commentaire = match ? match[2] : "";

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
                    commentaire,
                ]);
            });
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Réservations");
            XLSX.writeFile(wb, "reservations.xlsx");
        });
    }
})