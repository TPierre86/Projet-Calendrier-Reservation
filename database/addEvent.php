<?php
session_start();
require_once(__DIR__ . '/DAO.php');
header('Content-Type: application/json');

// Récupération des données envoyées en JSON
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Validation des données
if (!$data || !isset($data['startDate'], $data['startTime'], $data['endTime'], $data['roomSelect'])) {
    echo json_encode(["success" => false, "error" => "Requête invalide ou données manquantes."]);
    exit;
}

$startDate = $data['startDate'];
$endDate = $data['endDate'];
$startTime = $data['startTime'];
$endTime = $data['endTime'];
$comment = $data['commentInput'];
$room = $data['roomSelect'];
$utilisateur_id = isset($_SESSION['connected_user']) ? $_SESSION['connected_user'] : null;

$recurrence = !empty($data['recurrence']);
$weeks = isset($data['recurrenceWeeks']) ? (int)$data['recurrenceWeeks'] : 0;


if (!$utilisateur_id) {
    echo json_encode(["success" => false, "error" => "Utilisateur non connecté."]);
    exit;
}

try {
    $dao = new DAOReservation();
    $dao->connexion();

    if ($recurrence && $weeks > 0) {
        $date = new DateTime($startDate, new DateTimeZone('Europe/Paris'));
        for ($i = 0; $i < $weeks; $i++) {
            $dateIter = clone $date;
            $dateIter->modify('+' . ($i * 2) . ' week'); // Récurrence toutes les 2 semaines
            $dateString = $dateIter->format('Y-m-d');

            $dao->NewReservation(
                $dateString, 
                $dateString, 
                $startTime, 
                $endTime, 
                $comment, 
                null, 
                $room, 
                $utilisateur_id
            );
        }
    } else {
        $dao->NewReservation(
            $startDate, 
            $endDate, 
            $startTime, 
            $endTime, 
            $comment, 
            null, 
            $room, 
            $utilisateur_id
        );
    }

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}