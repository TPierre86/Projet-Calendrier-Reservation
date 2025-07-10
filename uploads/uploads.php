<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $filename = uniqid() . '_' . basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        echo json_encode(['success' => true, 'filePath' => 'uploads/' . $filename]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'upload.']);
    }
    exit;
    
}
echo json_encode(['success' => false, 'error' => 'Aucun fichier reçu.']);





