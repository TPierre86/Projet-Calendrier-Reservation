<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');

$sql = "
    SELECT r.id_reservation, r.salle_id, r.date_debut, r.date_fin, r.heure_debut, r.heure_fin,
        s.nom_salle
    FROM reservations r
    JOIN salles s ON r.salle_id = s.id_salle
";

$stmt = $pdo->query($sql);

// Récupération des réservations (remplace 'id' par 'id_reservation')

$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['date_debut'] . 'T' . $row['heure_debut'];
    $end = $row['date_fin'] . 'T' . $row['heure_fin'];
    $events[] = [
        'id' => $row['id_reservation'],
        'title' => '[' . $row['nom_salle'] . '] <a href="#" class="comment-link" data-id="' . $row['id_reservation'] . '"><i class="fa-solid fa-comments"></i></a>',
        'start' => $start,
        'end' => $end,
        'allDay' => false,
        'extendedProps' => [
            'id_reservation' => $row['id_reservation'],
            'date_debut' => $row['date_debut'],
            'date_fin' => $row['date_fin'],
            'heure_debut' => $row['heure_debut'],
            'heure_fin' => $row['heure_fin'],
            'attachments' => [], // Ajoutez ici les pièces jointes si nécessaire
            'salle_id' => $row['salle_id']
        ]
        
    ];
}

echo json_encode($events);