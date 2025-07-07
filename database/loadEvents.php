<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');

$sql = "
    SELECT r.id_reservation, r.commentaire, r.date_debut, r.date_fin, r.heure_debut, r.heure_fin,
        s.nom_salle
    FROM reservations r
    JOIN salles s ON r.salle_id = s.id_salle
";

$stmt = $pdo->query($sql);

$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['date_debut'] . 'T' . $row['heure_debut'];
    $end = $row['date_fin'] . 'T' . $row['heure_fin'];
    $events[] = [
        'id' => $row['id_reservation'],
        'title' => '[' . $row['nom_salle'] . '] ' . $row['commentaire'],
        'start' => $start,
        'end' => $end,
        'allDay' => false
    ];
}

echo json_encode($events);