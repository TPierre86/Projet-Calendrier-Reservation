<?php 
require_once('database/DAO.php');
        $dao = new DAOReservation();
        $dao->connexion();
    $reservation_id = $_POST['id_reservation'] ?? null;
    $comments = [];
    if (!empty($reservation_id)) {
    }
    //verification de l'id utilisateur connecté
if (isset($_SESSION['connected_user'])) {
    // L'utilisateur est connecté
    $utilisateur_id = $_SESSION['connected_user'];
    // Pour debug :
    //echo "Utilisateur connecté : " . $utilisateur_id;
} else {
    // L'utilisateur n'est pas connecté
    // echo "Aucun utilisateur connecté.";
}
//========Gestion des droits========
include 'droit.php';

$userRole = getUserRole();

echo "<script>window.userRole = ".json_encode($userRole).";</script>";
//==================================


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    
    if($action === 'enregistrer') {
          //Recuperer les données
          $startDate = isset($_POST['startDate'])?($_POST['startDate']):"";
          $endDate = isset($_POST['endDate'])?($_POST['endDate']):"";
          $startTime =isset($_POST['startTime'])?($_POST['startTime']):"";
          $endTime = isset($_POST['endTime'])?($_POST['endTime']):"";
          $roomSelect = isset($_POST['roomSelect'])?($_POST['roomSelect']):"";

          // Gérer la pièce jointe (si envoyée)
            $attachments = null;
            if (isset($_FILES['attachments']) && $_FILES['attachments']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        }
                $fileName = basename($_FILES['attachments']['name']);
                $targetPath = $uploadDir . time() . "_" . $fileName;

        

        if (move_uploaded_file($_FILES['attachments']['tmp_name'], $targetPath)) {
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
        $reservationExist=false;
        $reservations = $dao->getReservation();
        foreach ($reservations as $reservation) {
if ($startDate == $reservation["date_debut"] && $roomSelect == $reservation["salle_id"] &&
      (
        ($startTime >= $reservation["heure_debut"] && $startTime < $reservation["heure_fin"]) ||
        ($endTime > $reservation["heure_debut"] && $endTime <= $reservation["heure_fin"]) ||
        ($startTime <= $reservation["heure_debut"] && $endTime >= $reservation["heure_fin"])
      )) {
            $reservationExist = true;
            break;
          }
        }

        if ($reservationExist) {
            $_SESSION['message'] = "Reservation Impossible. Salle déjà occupée.";
            header('Location: /Projet-Calendrier-Reservation/controllers.php');
            exit;
        } else {
            $success = $dao->NewReservation($startDate,$endDate,$startTime,$endTime,$attachments,$roomSelect,$utilisateur_id);
            if ($success) {
                $_SESSION['message'] = "Reservation réussi";
                header('Location: /Projet-Calendrier-Reservation/controllers.php');
                exit;
            }
        }
    } 
    if ($action === 'supprimer') {
        $id_reservation = $_POST['id_reservation'];
        $dao->deleteReservation($id_reservation);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } 
    if ($action === 'modifier') {
        $id_reservation = $_POST['id_reservation'];
        
        $reservation= $dao->getReservation();
            $startDate = isset($_POST['startDate'])?($_POST['startDate']):"";
            $endDate = isset($_POST['endDate'])?($_POST['endDate']):"";
            $startTime = isset($_POST['startTime'])?($_POST['startTime']):"";
            $endTime = isset($_POST['endTime'])?($_POST['endTime']):"";
            $commentInput = isset($_POST['commentInput'])?($_POST['commentInput']):"";
            $attachments = isset($_POST['attachments'])?($_POST['attachments']):"";
            $roomSelect = isset($_POST['roomSelect'])?($_POST['roomSelect']):"";    
            $id_utilisateur = $_POST['id_utilisateur'];
        $dao->updateReservation(
            $id_reservation, 
            $startDate, 
            $endDate, 
            $startTime, 
            $endTime,  
            $attachments, 
            $roomSelect
        );
        // Redirection ou traitement du formulaire de modification
        header("Location: controllers.php");
        exit;    
      } 
}

//bouton export cacher si pas connecté//
$peutAfficher = false;

if (isset($_SESSION['connected_user']) && isset($_SESSION['profil'])) {
            $peutAfficher = true;
    }else{
            $peutAfficher = false;
    }
?>
  <main class="container mt-4">
  <?php
  if (isset($_SESSION['message'])) {
      echo "<script type='text/javascript'>alert('{$_SESSION['message']}');</script>";
      unset($_SESSION['message']);
  }
  ?>
  <button id="exportBtn" type="button"<?php if (!$peutAfficher) echo 'style="display: none;"'; ?> style="background:#2b3d4f;border:2px solid #2b3d4f;color:#ffffff;border-radius:8px;padding:4px 12px;margin:0 8px;font-size:1.1rem;cursor:pointer;">
  <i class="fa-solid fa-file-excel"></i>
</button>
<button id="gcalExportBtn" type="button"<?php if (!$peutAfficher) echo 'style="display: none;"'; ?> style="background:#2b3d4f;border:2px solid #2b3d4f;color:#ffffff;border-radius:8px;padding:4px 12px;margin:0 8px;font-size:1.1rem;cursor:pointer;">
  <i class="fa-brands fa-google"></i>
</button>
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
          <form id="formulaire" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id_reservation" id="id_reservation" value="<?= htmlspecialchars($reservation_id) ?>">
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
            <label for="attachments">Fichier PDF:</label>
            <input type="file" id="attachments" name="attachments" accept=".pdf">
          </section>
          <section class="mb-3">
            <label for="roomSelect" class="form-label">Salle</label>
            <select class="form-select" id="roomSelect" name="roomSelect">
              <option value="" disabled>--</option>
              <option value="1">Salle de réunion</option>
              <option value="2">Bar</option>
              <option value="3">Réfectoire</option>
            </select>
          <section class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name ="recurrence" id="recurrenceCheckbox" />
            <label class="form-check-label" for="recurrenceCheckbox">Récurrence</label>
          <section id="recurrenceOptions" style="display: none;" class="mb-3">
            <label for="recurrenceWeeks">Nombre de réservations (toutes les 2 semaines) :</label>
            <input type="number" name="recurrenceWeeks" id="recurrenceWeeks" value="3" min="1" max="52">
          </section>
          </section>
        </section>
        </section>
        
        <section class="modal-footer">
          <button name="action" value="supprimer" id="deleteBtn" class="btn btn-danger me-auto" style="display: none;">Supprimer</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button name="action" value="enregistrer" id="saveBtn" class="btn btn-primary">Enregistrer</button>
        </section>
        </form> 
      </article>
    </article>
  </section>
  <!-- //modal pour les commentaires -->
  <section class="modal fade" id="filCommentsModal" tabindex="-1" aria-labelledby="filCommentsLabel" aria-hidden="true" style="display = none;">
    <article class="modal-dialog">
      <article class="modal-content bg-dark text-white">
        <header class="modal-header">
          <h5 class="modal-title" id="filCommentsLabel">Commentaires</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </header>
        <section class="modal-body" id="filComments">
          <article id="commentsData">
          </article>

              <form id="newComment" onsubmit="return false;">
                  <input type="hidden" name="reservation_id" id="reservation_id" value="">
                  <input name="newCommentInput" type="text" id="newCommentInput" required></input>
                  <button id="envoyer" name="action" value="envoyer" type="submit"><i class="fa-solid fa-paper-plane"></i></button>
              </form>
        </section>
      </article>
    </article>
  </section>
<script>
  window.userRole = <?php echo json_encode($_SESSION["profil"] ?? null); ?>;
  window.canEdit = <?php echo json_encode(canEdit()); ?>;
  window.canDelete = <?php echo json_encode(canDelete()); ?>;
  window.canCreate = <?php echo json_encode(canCreate()); ?>;
  window.canEdit = <?php echo json_encode(canEditAdmin()); ?>;
  window.canDelete = <?php echo json_encode(canDeleteAdmin()); ?>;
  window.canCreate = <?php echo json_encode(canCreateAdmin()); ?>;
  window.canComment = <?php echo json_encode(canComment()); ?>;
  window.canView = true; 
</script>