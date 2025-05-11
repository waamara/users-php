<?php
session_start();
require_once '../../../db_connection/db_conn.php';
ob_clean(); // Clear any previous output

header('Content-Type: application/json');

// Receive the data from the frontend (AJAX)
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is received
if (!$data) {
    echo json_encode(['error' => 'No data received']);
    exit();
}

// Trim and extract data
$id = intval($data['id']);
$code = trim($data['code']);
$label = trim($data['label']);
$symbole = trim($data['symbole']);

try {
    // Check if Code Already Exists (but exclude the current monnaie being updated)
    $checkCodeStmt = $pdo->prepare("SELECT id FROM monnaie WHERE code = :code AND id != :id");
    $checkCodeStmt->execute(['code' => $code, 'id' => $id]);

    if ($checkCodeStmt->rowCount() > 0) {
        echo json_encode(['error' => 'Ce code existe déjà']);
        exit();
    }
    
    // Check if Label Already Exists (but exclude the current monnaie being updated)
    $checkLabelStmt = $pdo->prepare("SELECT id FROM monnaie WHERE label = :label AND id != :id");
    $checkLabelStmt->execute(['label' => $label, 'id' => $id]);

    if ($checkLabelStmt->rowCount() > 0) {
        echo json_encode(['error' => 'Ce label existe déjà']);
        exit();
    }

    // Prepare the SQL query to update the data
    $sql = "UPDATE monnaie SET code = :code, label = :label, symbole = :symbole WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':label', $label);
    $stmt->bindParam(':symbole', $symbole);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Monnaie mise à jour avec succès']);
    } else {
        echo json_encode(['error' => 'Échec de la mise à jour de la monnaie']);
    }
} catch (PDOException $e) {
    // Return error in case of failure
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

