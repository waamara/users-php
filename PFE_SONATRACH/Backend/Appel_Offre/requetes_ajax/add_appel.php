<?php
session_start();
require_once '../../../db_connection/db_conn.php';
ob_clean(); // Clear any previous output

header('Content-Type: application/json');

// Receive the data from the frontend (AJAX)
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data was received
if (!$data) {
    echo json_encode(['error' => 'No data received']);
    exit();
}

$code = trim($data['code']);
$dateAO = trim($data['dateAO']);

try {
    // Prepare the SQL query to insert the data
    $sql = "INSERT INTO appel_offre (num_appel_offre, date_appel_offre) VALUES (:code, :dateAO)";
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to the query
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':dateAO', $dateAO);

    // Execute the statement to insert the data
    if ($stmt->execute()) {
        // If insertion is successful, send a success response
        echo json_encode(['success' => 'Data successfully inserted']);
    } else {
        // If insertion failed, send an error response
        echo json_encode(['error' => 'Failed to insert data']);
    }
} catch (PDOException $e) {
    // If there is an issue with the query or database, catch the exception and return the error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

