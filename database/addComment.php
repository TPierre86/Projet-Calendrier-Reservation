<?php
require_once 'DAOReservation.php';
session_start();

if (!isset($_POST['reservation_id'], $_POST['comment']) || !isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['success' => false]);
    exit;
}

$dao = new DAOReservation();
$dao->connexion();

$reservation_id = intval($_POST['reservation_id']);
$utilisateur_id = $_SESSION['id_utilisateur'];
$comment = trim($_POST['comment']);

$success = $dao->NewComment($reservation_id, $utilisateur_id, $comment);

if ($success) {
    $user = $dao->getUtilisateursById($utilisateur_id);
    echo json_encode([
        'success' => true,
        'comment' => htmlspecialchars($comment),
        'nom_utilisateur' => htmlspecialchars($user['nom_utilisateur']),
        'heure_comment' => date('d/m/Y H:i')
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>