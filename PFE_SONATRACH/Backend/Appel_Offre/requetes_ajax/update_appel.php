<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['code'], $data['dateAO'])) {
    echo json_encode(['error' => 'Données incomplètes']);
    exit();
}

$offerId = intval($data['id']);
$code = trim($data['code']);
$dateAO = trim($data['dateAO']);

try {
    // ✅ Check if Code Already Exists (but exclude the current offer being updated)
    $checkStmt = $pdo->prepare("SELECT id FROM appel_offre WHERE num_appel_offre = :code AND id != :id");
    $checkStmt->execute(['code' => $code, 'id' => $offerId]);

    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['error' => 'Ce code existe déjà']);
        exit();
    }

    // ✅ Update Offer
    $stmt = $pdo->prepare("UPDATE appel_offre SET num_appel_offre = :code, date_appel_offre = :dateAO WHERE id = :id");
    $stmt->execute(['id' => $offerId, 'code' => $code, 'dateAO' => $dateAO]);

    echo json_encode(['success' => 'Appel d\'offre mis à jour avec succès']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

