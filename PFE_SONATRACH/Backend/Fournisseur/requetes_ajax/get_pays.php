<?php

require_once("../../../db_connection/db_conn.php");

try {
    // Fetch all countries from the pays table
    $stmt = $pdo->query("SELECT id, label FROM pays");
    $countries = $stmt->fetchAll();

    // Return the data as JSON
    echo json_encode(['status' => 'success', 'data' => $countries]);
} catch (Exception $e) {
    // Handle errors
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>