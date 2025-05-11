<?php
session_start();
require_once '../../../db_connection/db_conn.php';


// Ensure the response is JSON
header('Content-Type: application/json');

try {
    // Fetch all records from the banque table
    $sql = "SELECT id, code, label FROM banque ORDER BY id DESC"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all results
    $banques = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($banques);
} catch (PDOException $e) {
    // Handle database errors gracefully
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>