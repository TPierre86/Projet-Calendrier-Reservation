<?php
require_once(__DIR__ . '/../database/DAO.php');
header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$checked = isset($_POST['checked']) ? intval($_POST['checked']) : 0;

if ($id > 0) {
    $dao = new DAOReservation();
    $dao->connexion();
    $success = $dao->setMenage($id, $checked);
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
}