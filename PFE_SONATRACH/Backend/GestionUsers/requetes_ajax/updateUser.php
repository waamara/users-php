<?php
session_start();
require_once("../../../db_connection/db_conn.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log incoming data for debugging
$rawInput = file_get_contents("php://input");
error_log("Received data: " . $rawInput);

$input = json_decode($rawInput, true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Données invalides: ' . json_last_error_msg()]);
    exit;
}

$id = intval($input['id'] ?? 0);
$nomComplet = trim($input['nomComplet'] ?? '');
$userName = trim($input['userName'] ?? '');
$compte = $input['compte'] ?? '';
$structure = intval($input['structure'] ?? 0);

// Log processed data
error_log("Processed data - ID: $id, Nom: $nomComplet, Username: $userName, Compte: $compte, Structure: $structure");

if ($id <= 0 || empty($nomComplet) || empty($userName) || !in_array($compte, ['actif', 'desactive'])) {
    echo json_encode(['success' => false, 'message' => 'Champs invalides']);
    exit;
}

// Séparer nom et prénom (simplification, à adapter)
$parts = explode(' ', $nomComplet, 2);
$nom = $parts[0];
$prenom = $parts[1] ?? '';

$status = ($compte === 'actif') ? 1 : 0;

try {
    // Mettre à jour la table users
    $stmt = $pdo->prepare("UPDATE users SET nom_user = ?, prenom_user = ?, username = ?, status = ?, structure = ? WHERE id = ?");
    $success = $stmt->execute([$nom, $prenom, $userName, $status, $structure, $id]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . implode(', ', $stmt->errorInfo())]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
?>