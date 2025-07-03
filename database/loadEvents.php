<?php
header('Content-Type: application/json');

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');

// Récupération des réservations (remplace 'id' par 'id_reservation')
$stmt = $pdo->query("SELECT id_reservation, salle_id, commentaire, date_debut, date_fin, heure_debut, heure_fin FROM reservations");
$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['date_debut'] . 'T' . $row['heure_debut'];
    $end = $row['date_fin'] . 'T' . $row['heure_fin'];
    $events[] = [
        'id' => $row['id_reservation'],
        'title' => '[Salle ' . $row['salle_id'] . '] ' . $row['commentaire'],
        'start' => $start,
        'end' => $end,
        'allDay' => false
    ];
}
echo json_encode($events);