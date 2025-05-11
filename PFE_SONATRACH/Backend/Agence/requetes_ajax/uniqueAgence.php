<?php
require_once '../../../db_connection/db_conn.php';

// Read JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

// Check if required fields are present
if (!isset($data['field']) || !isset($data['value'])) {
    echo json_encode(["success" => false, "error" => "Données invalides reçues"]);
    exit;
}

$field = $data['field']; // The field to check (e.g., 'code' or 'label')
$value = trim($data['value']); // The value to check
$currentId = $data['currentId'] ?? null; // Optional: ID of the current record (for updates)

// Validate the field name
if (!in_array($field, ['code', 'label'])) {
    echo json_encode(["success" => false, "error" => "Champ invalide."]);
    exit;
}

try {
    // Build the SQL query
    $sql = "SELECT COUNT(*) AS count FROM agence WHERE $field = :value";
    $params = [":value" => $value];

    // Exclude the current record if updating
    if ($currentId) {
        $sql .= " AND id != :id";
        $params[":id"] = $currentId;
    }

    // Execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the value is unique
    if ($result['count'] > 0) {
        echo json_encode(["success" => false, "error" => "Le $field existe déjà."]);
    } else {
        echo json_encode(["success" => true]);
    }
} catch (PDOException $e) {
    // Handle database errors gracefully
    echo json_encode(["success" => false, "error" => "Erreur: " . $e->getMessage()]);
}
?>