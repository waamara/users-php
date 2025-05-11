<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json');

if (!isset($_GET['query'])) {
    echo json_encode(['error' => 'No search query provided']);
    exit();
}

$searchTerm = '%' . $_GET['query'] . '%';

try {
    $stmt = $pdo->prepare("SELECT id, num_appel_offre, date_appel_offre FROM appel_offre WHERE num_appel_offre LIKE :term OR date_appel_offre LIKE :term ORDER BY id DESC");
    $stmt->execute(['term' => $searchTerm]);
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates to dd-mm-yyyy
    foreach ($offers as &$offer) {
        if (isset($offer['date_appel_offre']) && $offer['date_appel_offre']) {
            $date = new DateTime($offer['date_appel_offre']);
            $offer['date_appel_offre'] = $date->format('d-m-Y');
        }
    }
    
    echo json_encode($offers);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

