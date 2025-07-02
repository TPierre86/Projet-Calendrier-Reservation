const calendarEl = document.getElementById("calendar");
const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
const selectedDateText = document.getElementById("selectedDateText");
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
let currentEvent = null;
let selectedRangeStart = null;
let selectedRangeEnd = null;

// Affiche/masque les options de récurrence
recurrenceCheckbox.addEventListener("change", () => {
recurrenceOptions.style.display = recurrenceCheckbox.checked ? "block" : "none";
});

// Initialisation du calendrier FullCalendar
const calendar = new FullCalendar.Calendar(calendarEl, {
initialView: "dayGridMonth",
locale: "fr",
selectable: true,
headerToolbar: {
    left: "prev,next today",
    center: "title",
    right: "dayGridMonth,timeGridWeek,timeGridDay",
},
buttonText: {
    today: "Aujourd’hui",
    month: "Mois",
    week: "Semaine",
    day: "Jour",
},

  // Lorsqu'on sélectionne un créneau (jour entier ou plage horaire) (vue jour)
select: function (info) {
    currentEvent = null;
    const isAllDay = info.allDay;
    let startDate = new Date(info.start);
    let endDate = new Date(info.end);
        if (isAllDay) { // En vue mois (allDay), on ajuste la date de fin
            endDate.setDate(endDate.getDate() - 1);
        }
        selectedRangeStart = startDate.toISOString().slice(0, 10);
        selectedRangeEnd = endDate.toISOString().slice(0, 10);

        selectedDateText.textContent = isAllDay ? `Période : ${selectedRangeStart} → ${selectedRangeEnd}`
            : `Date : ${selectedRangeStart}`;

        // Remplit les heures automatiquement si ce n’est pas une sélection de journée
        if (!isAllDay) {
            startTime.value = info.start.toISOString().substring(11, 16);
            endTime.value = info.end.toISOString().substring(11, 16);
        } else {
            startTime.value = "";
            endTime.value = "";
        }

    // Réinitialisation des champs de la modale
    commentInput.value = "";
    roomSelect.selectedIndex = 0;
    recurrenceCheckbox.checked = false;
    recurrenceOptions.style.display = "none";
    deleteBtn.style.display = "none";

    // Affichage de la modale
    eventModal.show();
},
  // Lorsqu'on clique sur un événement existant
eventClick: function (info) {
    currentEvent = info.event;
    selectedRangeStart = currentEvent.startStr.substring(0, 10);
    selectedRangeEnd = selectedRangeStart;

    selectedDateText.textContent = `Date sélectionnée : ${selectedRangeStart}`;
    commentInput.value = currentEvent.title.replace(/\[.*?\]\s*/, "");
    startTime.value = currentEvent.start.toISOString().substring(11, 16);
    endTime.value = currentEvent.end
      ? currentEvent.end.toISOString().substring(11, 16)
      : "";

    // Récupère la salle depuis le titre ([Salle])
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
  },

  // Liste des événements (réservations)
  events: [],
});

// Affiche le calendrier
calendar.render();

// Enregistrement d'un nouvel événement ou mise à jour
saveBtn.addEventListener("click", () => {
  const start = startTime.value;
  const end = endTime.value;
  const comment = commentInput.value.trim();
  const room = roomSelect.value;

  // Vérifie les champs obligatoires
  if (!start || !end || !comment) {
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
    currentEvent.setStart(selectedRangeStart + startTimeStr);
    currentEvent.setEnd(selectedRangeStart + endTimeStr);
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
        calendar.addEvent({
          title: `[${room}] ${comment}`,
          start: dStr + startTimeStr,
          end: dStr + endTimeStr,
          allDay: false,
        });
      }
    } else {
      // Ajout d’un événement simple (pas récurrent)
      calendar.addEvent({
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
