<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json');

if (!isset($_GET['query'])) {
    echo json_encode(['error' => 'Aucun terme de recherche reçu']);
    exit();
}

$searchQuery = trim($_GET['query']); // Get and sanitize the search term

try {
    // Search across multiple fields: id, code, and label
    $stmt = $pdo->prepare("
        SELECT id, code, label 
        FROM banque 
        WHERE id LIKE :query OR code LIKE :query OR label LIKE :query
    ");
    $stmt->execute(['query' => "%$searchQuery%"]); // Use wildcards for partial matches
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo json_encode([]); // Return an empty array if no results are found
    } else {
        echo json_encode($results); // Return the results as JSON
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>