<?php
header('Content-Type: application/json');

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');

// Récupération des réservations (remplace 'id' par 'id_reservation')
$stmt = $pdo->query("SELECT id_reservation, salle_id, date_debut, date_fin, heure_debut, heure_fin FROM reservations");
$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['date_debut'] . 'T' . $row['heure_debut'];
    $end = $row['date_fin'] . 'T' . $row['heure_fin'];
    $events[] = [
        'id' => $row['id_reservation'],
        'title' => '[Salle ' . $row['salle_id'] . '] <a href="">test</a>',
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