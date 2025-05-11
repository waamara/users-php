<?php
session_start();
require_once '../../../db_connection/db_conn.php';
ob_clean(); // Clear any previous output

header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID non fourni']);
    exit();
}

$id = intval($_GET['id']);

try {
    // Prepare the SQL query to get the monnaie details
    $sql = "SELECT * FROM monnaie WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    // Fetch the result
    $monnaie = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($monnaie) {
        echo json_encode($monnaie);
    } else {
        echo json_encode(['error' => 'Monnaie non trouvée']);
    }
} catch (PDOException $e) {
    // Return error in case of failure
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

