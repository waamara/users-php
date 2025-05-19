<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['code'])) {
    echo json_encode(['error' => 'Code non fourni']);
    exit();
}

$code = trim($data['code']);
$id = isset($data['id']) ? intval($data['id']) : 0; // Get ID if provided, otherwise use 0

try {
    // Check if code exists, excluding the current record if ID is provided
    $sql = "SELECT id FROM appel_offre WHERE num_appel_offre = :code";
    if ($id > 0) {
        $sql .= " AND id != :id";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':code', $code);
    
    if ($id > 0) {
        $stmt->bindParam(':id', $id);
    }
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['error' => 'Ce code existe déjà']);
    } else {
        echo json_encode(['success' => 'Code unique']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

