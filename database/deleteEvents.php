<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $pdo = new PDO('mysql:host=localhost;dbname=reservation-salles', 'root', '');
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id_reservation = ?");
        $stmt->execute([$data['id']]);

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "ID manquant"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Requête invalide"]);
}