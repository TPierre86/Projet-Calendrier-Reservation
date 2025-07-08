<?php 
require_once('database/DAO.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //Recuperer les données
  $startDate = isset($_POST['startDate'])?($_POST['startDate']):"";
  $endDate = isset($_POST['endDate'])?($_POST['endDate']):"";
  $startTime =isset($_POST['startTime'])?($_POST['startTime']):"";
  $endTime = isset($_POST['endTime'])?($_POST['endTime']):"";
  $commentInput = isset($_POST['commentInput'])?($_POST['commentInput']):"";
  $roomSelect = isset($_POST['roomSelect'])?($_POST['roomSelect']):"";

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

  // Gérer le utilisateur_id
if (isset($_SESSION["connected_user"])) {
    $utilisateur_id = $_SESSION["connected_user"];
    //echo "Utilisateur connecté avec l'ID : " . $utilisateur_id;
} else {
    //echo "Aucun utilisateur connecté.";
}

// --------commande pour New Reservation--------------
$dao = new DAOReservation();
$dao->connexion();
$reservationExist=false;
$reservations = $dao->getReservation();
foreach ($reservations as $reservation) {
  if ($startDate == $reservation["date_debut"] && $startTime == $reservation["heure_debut"] && $roomSelect == $reservation["salle_id"] || $startDate == $reservation["date_debut"] && ($startTime < $reservation["heure_fin"]) && $roomSelect == $reservation["salle_id"]) {
    $reservationExist = true;
    break;
  }
}

  if ($reservationExist) {
      $_SESSION['message'] = "Reservation Impossible. Salle déjà occupée.";
      header('Location: /Projet-Calendrier-Reservation/controllers.php');
      exit;
  } else {
      $success = $dao->NewReservation($startDate, $endDate, $startTime, $endTime, $commentInput, $attachments, $roomSelect, $utilisateur_id);
      if ($success) {
          $_SESSION['message'] = "Reservation réussi";
          header('Location: /Projet-Calendrier-Reservation/controllers.php');
          exit;
      }
  }
}
?>


  <main class="container mt-4">
  <?php
  if (isset($_SESSION['message'])) {
      echo "<script type='text/javascript'>alert('{$_SESSION['message']}');</script>";
      unset($_SESSION['message']);
  }
  ?>
  <button id="exportBtn" type="button"><i class="fa-solid fa-file-excel"></i></button>
  <button id="gcalExportBtn" type="button"><i class="fa-brands fa-google"></i></button>
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
          <form id="formulaire" method="POST">
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
