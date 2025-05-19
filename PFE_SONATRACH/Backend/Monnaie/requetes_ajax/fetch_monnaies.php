<?php
session_start();
require_once '../../../db_connection/db_conn.php';
ob_clean(); // Clear any previous output

header('Content-Type: application/json');

try {
    // Prepare the SQL query to get all monnaies
    $sql = "SELECT * FROM monnaie ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Fetch all results
    $monnaies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($monnaies);
} catch (PDOException $e) {
    // Return error in case of failure
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

