<?php

// Masquer les erreurs PHP dans la réponse (elles casseraient le JSON)
ini_set('display_errors', 0);
error_reporting(0);
try {
    header('Content-Type: application/json');

    require_once 'DAO.php';

    $reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : 0;

    if (!$reservation_id) {
        echo json_encode([]);
        exit;
    }

    $dao = new DAOReservation();
    $dao->connexion();
    $comments = $dao->getCommentsByReservationId($reservation_id);
    echo json_encode($comments);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
}

?>