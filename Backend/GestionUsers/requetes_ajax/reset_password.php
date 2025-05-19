<?php
session_start();
require_once("../../../db_connection/db_conn.php");

header('Content-Type: application/json');

// Log incoming data for debugging
$rawInput = file_get_contents("php://input");
error_log("Reset password received data: " . $rawInput);

$input = json_decode($rawInput, true);
if (!$input || !isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$id = intval($input['id']);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
    exit;
}

// Générer un mot de passe temporaire
$tempPassword = bin2hex(random_bytes(4)); // 8 caractères
$hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $success = $stmt->execute([$hashedPassword, $id]);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'tempPassword' => $tempPassword
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la réinitialisation du mot de passe']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
?>