<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'DAO.php';
session_start();
header('Content-Type: application/json');

if (!isset($_GET['reservation_id'], $_POST['comment']) || !isset($_SESSION['connected_user'])) {
    echo json_encode(['success' => false]);
    exit;
}

$dao = new DAOReservation();
$dao->connexion();

$reservation_id = intval($_GET['reservation_id']);
$utilisateur_id = $_SESSION['connected_user'];
$comment = trim($_POST['comment']);

$success = $dao->NewComment($reservation_id, $utilisateur_id, $comment);

if ($success) {
    $user = $dao->getUtilisateursById($utilisateur_id);
    echo json_encode([
        'success' => true,
        'comment' => htmlspecialchars($comment),
        'nom_utilisateur' => htmlspecialchars($user['nom_utilisateur']),
        'heure_comment' => date('H:i', time()),
    ]);
    exit;
} else {
    echo json_encode(['success' => false]);
    exit;
}

?>
