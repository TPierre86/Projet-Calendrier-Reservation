<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');

$sql = "
    SELECT r.id_reservation, r.commentaire, r.date_debut, r.date_fin, r.heure_debut, r.heur_fin,
        s.nom_salle
    FROM reservations r
    JOIN salles s ON r.salle_id = s.id_salle
";

$stmt = $pdo->query($sql);

$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['date_debut'] . 'T' . $row['heure_debut'];
    $end = $row['date_fin'] . 'T' . $row['heur_fin'];
    $events[] = [
        'id' => $row['id_reservation'],
        'title' => '[' . $row['nom_salle'] . '] ' . $row['commentaire'],
        'start' => $start,
        'end' => $end,
        'allDay' => false
    ];
}

echo json_encode($events);