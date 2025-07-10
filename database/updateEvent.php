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
      // Lire le corps JSON de la requête
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception("Données JSON invalides.");
    }
    // Extraire les champs attendus

    $id_reservation = $data['id_reservation'] ?? null;
    $startDate = $data['startDate'] ?? '';
    $endDate = $data['endDate'] ?? '';
    $startTime = $data['startTime'] ?? '';
    $endTime = $data['endTime'] ?? '';
    $roomSelect = $data['roomSelect'] ?? '';
    $recurrent = !empty($data['recurrence']) ? 1 : 0;
    $menageCheckbox = !empty($data['menageCheckbox']) ? 1 : 0;
    $menage = !empty($data['menage']) ? 1 : 0;


        // Récupération du user connecté
    $connectedUser = $_SESSION['connected_user'] ?? null;

    if (!$connectedUser) {
        throw new Exception("Utilisateur non connecté.");
    }

    if ($connectedUser == 2) {
        // Gestionnaire : récupère les infos de la requête
        $association_id = $data['association_id'] ?? null;
    }else{
        $utilisateur_id = $connectedUser;
        $association_id = $_SESSION['association_id'] ?? null;
    }
        // Utilisateur classique : utilise la session



    if (!$id_reservation || !$utilisateur_id) {
        throw new Exception("ID de réservation ou utilisateur manquant.");
    }


    // Pour cette version, pas de gestion de pièce jointe
    $attachments = isset($data['attachments']) ? $data['attachments'] : null;

    $success= $dao->updateReservation(
            $id_reservation, 
            $startDate, 
            $endDate, 
            $startTime, 
            $endTime,  
            $attachments, 
            $roomSelect,
            $recurrent,
            $utilisateur_id,
            $association_id,
            $menageCheckbox,
            $menage
        );
        
    if (!$success) {
        throw new Exception("Échec de la mise à jour.");
    }
    // Réponse JSON simplifiée (à adapter selon tes besoins)
    echo json_encode([
        'success' => true,
        'message' => 'Réservation mise à jour',
        'reservation_id' => $id_reservation
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}