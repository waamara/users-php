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
$code = trim($data['code']);
$label = trim($data['label']);
$symbole = trim($data['symbole']);

try {
    // Prepare the SQL query to insert the data
    $sql = "INSERT INTO monnaie (code, label, symbole) VALUES (:code, :label, :symbole)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':label', $label);
    $stmt->bindParam(':symbole', $symbole);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Monnaie successfully inserted']);
    } else {
        echo json_encode(['error' => 'Failed to insert monnaie']);
    }
} catch (PDOException $e) {
    // Return error in case of failure
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

