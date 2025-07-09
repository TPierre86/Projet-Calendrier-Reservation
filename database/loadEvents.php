<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT r.id_reservation, r.salle_id, r.date_debut, r.date_fin, r.heure_debut, r.heure_fin,
            r.recurrent, r.menageCheckbox, r.menage,
            s.nom_salle,
            u.association_id
        FROM reservations r
        JOIN salles s ON r.salle_id = s.id_salle
        JOIN utilisateurs u ON r.utilisateur_id = u.id_utilisateur
        ORDER BY r.date_debut, r.heure_debut
    ";

    $stmt = $pdo->query($sql);

// Récupération des réservations (remplace 'id' par 'id_reservation')

    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $start = $row['date_debut'] . 'T' . $row['heure_debut'];
        $end = $row['date_fin'] . 'T' . $row['heure_fin'];

        $events[] = [
            'id' => $row['id_reservation'],
            'title' => '[' . $row['nom_salle'] . '] <a href="#" class="comment-link" data-id="' . $row['id_reservation'] . '"><i class="fa-solid fa-comments"></i></a> &nbsp; 
            <a href="#" class="attachment-link" data-id="' . $row['id_reservation'] . '"><i class="fa-solid fa-file-arrow-up"></i></a>',
            'start' => $start,
            'end' => $end,
            'allDay' => false,
            'association_id' => (int)$row['association_id'],
            'extendedProps' => [
                'id_reservation' => $row['id_reservation'],
                'date_debut' => $row['date_debut'],
                'date_fin' => $row['date_fin'],
                'heure_debut' => $row['heure_debut'],
                'heure_fin' => $row['heure_fin'],
                'attachments' => [],
                'salle_id' => $row['salle_id'],
                'recurrence' => isset($row['recurrent']) ? (bool)$row['recurrent'] : false,
                'menageCheckbox' => isset($row['menageCheckbox']) ? (bool)$row['menageCheckbox'] : false,
                'menage' => isset($row['menage']) ? (bool)$row['menage'] : false,
            ]
        ];
    }

    echo json_encode($events);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur base de données : ' . $e->getMessage()]);
}