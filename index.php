<?php 
require_once('database/DAO.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  //Recuperer les données
  $startDate = isset($_GET['startDate'])?($_GET['startDate']) : "";
  $endDate = $_GET['endDate'];
  $startTime =$_GET['startTime'];
  $endTime = $_GET['endTime'];
  $commentInput = $_GET['commentInput'];
  $attachments = $_GET['attachments'];
  $roomSelect = $_GET['roomSelect'];

  // Gérer la pièce jointe (si envoyée)
    $attachments = null;
    if (isset($_FILES['attachments']) && $_FILES['attachments']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['attachments']['name']);
        $targetPath = $uploadDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES['pieces_jointe']['tmp_name'], $targetPath)) {
            $attachments = $targetPath; // Stocke le chemin pour la BDD
        }
    }
}  
$dao = new DAOReservation();
$dao->connection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Calendrier de Réservations</title>

  <!-- Bootstrap Darkly Theme -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/cerulean/bootstrap.min.css" />

  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="public/styles/components/calendar.css" />
</head>
<body>
  <main class="container mt-4">
    <section id="calendar"></section>
  </main>

  <!-- Modal pour les réservations -->
  <section class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <article class="modal-dialog">
      <article class="modal-content bg-dark text-white">
        <header class="modal-header">
          <h5 class="modal-title" id="eventModalLabel">Nouvelle Réservation</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </header>
        <section class="modal-body">
          <form id="formulaire" method="GET">
          <!-- <p id="selectedDateText"></p> -->
          <section class="mb-3">
            <label for="startDate" class="form-label">Date de début</label>
            <input type="date" class="form-control" id="startDate" name="startDate">
          </section>
          <section class="mb-3">
            <label for="endDate" class="form-label">Date de fin</label>
            <input type="date" class="form-control" id="endDate" name="endDate">
          </section>
          <section class="mb-3">
            <label for="startTime" class="form-label">Heure de début</label>
            <input type="time" class="form-control" id="startTime" name="startTime" />
          </section>
          <section class="mb-3">
            <label for="endTime" class="form-label">Heure de fin</label>
            <input type="time" class="form-control" id="endTime" name="endTime"/>
          </section>
          <section class="mb-3">
            <label for="commentInput" class="form-label">Commentaire</label>
            <input type="text" class="form-control" id="commentInput" name="commentInput" />
          </section>
          <section class="mb-3">
            <label for="attachments">Fichier PDF:</label>
            <input type="file" id="attachments" name="attachments" accept=".pdf">
          </section>
          <section class="mb-3">
            <label for="roomSelect" class="form-label">Salle</label>
            <select class="form-select" id="roomSelect" name="roomSelect">
              <option disabled>--</option>
              <option value="1">Salle de réunion</option>
              <option value="2">Bar</option>
              <option value="3">Réfectoire</option>
            </select>
          </section>
          <section class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="recurrenceCheckbox" />
            <label class="form-check-label" for="recurrenceCheckbox">Récurrence</label>
          </section>
          <section id="recurrenceOptions" style="display: none;">
            <section class="mb-2">
              <label for="recurrenceWeeks" class="form-label">Nombre de semaines</label>
              <input type="number" class="form-control" id="recurrenceWeeks" min="1" value="2" />
            </section>
            <section class="mb-2">
              <label for="recurrenceDay" class="form-label">Jour de la semaine</label>
              <select class="form-select" id="recurrenceDay">
                <option value="1">Lundi</option>
                <option value="2">Mardi</option>
                <option value="3">Mercredi</option>
                <option value="4">Jeudi</option>
                <option value="5">Vendredi</option>
                <option value="6">Samedi</option>
                <option value="7">Dimanche</option>
              </select>
            </section>
          </section>
        </section>
        <footer class="modal-footer">
          <button id="deleteBtn" class="btn btn-danger me-auto" style="display: none;">Supprimer</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" id="saveBtn" class="btn btn-primary">Enregistrer</button>
        </footer>
        </form>
      </article>
    </article>
  </section>

  <!-- Librairies JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Ton script personnalisé -->
  <script src="public/js/calendar.js"></script>
</body>
</html>