<?php
session_start();
require_once(__DIR__ . '/DAO.php');
// require_once(__DIR__ . '/rights.php');
header('Content-Type: application/json');

// Récupération des données envoyées en JSON
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Validation des données
if (!$data || !isset($data['startDate'], $data['startTime'], $data['endTime'], $data['roomSelect'],$data['attachments'])) {
    echo json_encode(["success" => false, "error" => "Requête invalide ou données manquantes."]);
    exit;

}

$startDate = $data['startDate'];
$endDate = $data['endDate'];
$startTime = $data['startTime'];
$endTime = $data['endTime'];
$room = $data['roomSelect'];
$utilisateur_id = isset($_SESSION['connected_user']) ? $_SESSION['connected_user'] : null;
if (isset($data['association_id']) && $data['association_id'] && $data['association_id'] != "0") {
    $association_id = $data['association_id'];
} else {
    $association_id = isset($_SESSION['association_id']) ? $_SESSION['association_id'] : null;
}

$recurrence = !empty($data['recurrence']);
$weeks = isset($data['recurrenceWeeks']) ? (int)$data['recurrenceWeeks'] : 0;
$attachments = isset($data['attachments']) ? $data['attachments'] : 'NULL';


if (!$utilisateur_id) {
    echo json_encode(["success" => false, "error" => "Utilisateur non connecté."]);
    exit;
}

try {
    $dao = new DAOReservation();
    $dao->connexion();


    $menageCheckbox = !empty($data['menage']) ? 1 : 0;
    $menage = 0; // Le ménage n'est pas encore effectué lors de la création

    if ($recurrence && $weeks > 0) {
        $duration = (new DateTime($endDate))->diff(new DateTime($startDate))->days;
        $date = new DateTime($startDate, new DateTimeZone('Europe/Paris'));
        for ($i = 0; $i < $weeks; $i++) {
            $dateIter = clone $date;
            $dateIter->modify('+' . ($i * 2) . ' week'); // Récurrence toutes les 2 semaines
            $dateDebut = $dateIter->format('Y-m-d');
            $dateFin = (clone $dateIter)->modify('+' . $duration . ' days')->format('Y-m-d');

            $dao->NewReservation(
                $dateDebut,
                $dateFin,
                $startTime,
                $endTime,
                $attachments,
                $room,
                $utilisateur_id,
                $association_id, // association_id
                1, // recurrent = 1
                $menageCheckbox,
                $menage
            );
        }
    } else {
        $dao->NewReservation(
            $startDate, 
            $endDate, 
            $startTime, 
            $endTime, 
            $attachments,
            $room, 
            $utilisateur_id,
            $association_id, // association_id
            0, // recurrent = 0
            $menageCheckbox,
            $menage
        );
    }

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}