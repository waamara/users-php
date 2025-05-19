<?php
session_start();
require_once '../../../db_connection/db_conn.php';
ob_clean(); // Clear any previous output

header('Content-Type: application/json');

// Check if query parameter is provided
if (!isset($_GET['query'])) {
    echo json_encode(['error' => 'No search query provided']);
    exit();
}

$searchTerm = '%' . $_GET['query'] . '%';

try {
    // Prepare the SQL query to search monnaies
    $sql = "SELECT * FROM monnaie WHERE code LIKE :term OR label LIKE :term OR symbole LIKE :term ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':term', $searchTerm);
    $stmt->execute();
    
    // Fetch all results
    $monnaies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($monnaies);
} catch (PDOException $e) {
    // Return error in case of failure
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

