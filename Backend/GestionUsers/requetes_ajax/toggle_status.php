<?php
session_start();
require_once("../../../db_connection/db_conn.php");

header('Content-Type: application/json');

// Récupération des données JSON brutes
$rawInput = file_get_contents("php://input");
error_log("Toggle status received data: " . $rawInput);

$input = json_decode($rawInput, true);
if (!$input || !isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant.']);
    exit;
}

$id = intval($input['id']);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide.']);
    exit;
}

try {
    // Récupérer le statut actuel
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé.']);
        exit;
    }

    $currentStatus = intval($user['status']);
    $newStatus = $currentStatus === 1 ? 0 : 1;

    // Mise à jour du statut
    $update = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $update->execute([$newStatus, $id]);

    echo json_encode([
        'success' => true,
        'newStatut' => $newStatus === 1 ? 'actif' : 'désactivé'
    ]);
} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}
?>
