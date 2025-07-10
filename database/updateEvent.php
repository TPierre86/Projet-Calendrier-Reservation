<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('DAO.php');
$dao = new DAOReservation();
$dao->connexion();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception("Données JSON invalides.");
    }

    $id_reservation = $data['id_reservation'] ?? null;
    $startDate = $data['startDate'] ?? '';
    $endDate = $data['endDate'] ?? '';
    $startTime = $data['startTime'] ?? '';
    $endTime = $data['endTime'] ?? '';
    $roomSelect = $data['roomSelect'] ?? '';
    $recurrent = !empty($data['recurrence']) ? 1 : 0;
    $menageCheckbox = !empty($data['menageCheckbox']) ? 1 : 0;
    $menage = !empty($data['menage']) ? 1 : 0;

    $connectedUser = $_SESSION['connected_user'] ?? null;
    if (!$connectedUser) {
        throw new Exception("Utilisateur non connecté.");
    }

    // On garde la logique correcte : l'utilisateur d'origine ne change pas
    $resa = $dao->getReservationById($id_reservation);
    if (!$resa) {
        throw new Exception("Réservation introuvable.");
    }
    $utilisateur_id = $resa['utilisateur_id'];

    // Association id
    if ($connectedUser == 2) {
        $association_id = $data['association_id'] ?? null;
    } else {
        $association_id = $_SESSION['association_id'] ?? null;
    }

    if (!$id_reservation || !$utilisateur_id) {
        throw new Exception("ID de réservation ou utilisateur manquant.");
    }

    $attachments = isset($data['attachments']) ? $data['attachments'] : null;

    $success= $dao->updateReservation(
        $id_reservation, 
        $startDate, 
        $endDate, 
        $startTime, 
        $endTime,  
        $attachments, 
        $roomSelect,
        $utilisateur_id,
        $association_id,
        $recurrent,
        $menageCheckbox,
        $menage
    );
    
    if (!$success) {
        throw new Exception("Échec de la mise à jour.");
    }
    echo json_encode([
        'success' => true,
        'message' => 'Réservation mise à jour',
        'reservation_id' => $id_reservation
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}