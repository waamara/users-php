<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No ID received']);
    exit();
}

$offerId = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT id, num_appel_offre, date_appel_offre FROM appel_offre WHERE id = :id");
    $stmt->execute(['id' => $offerId]);
    $offer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($offer) {
        echo json_encode($offer);
    } else {
        echo json_encode(['error' => 'Offer not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

