<?php
require_once '../../../db_connection/db_conn.php';

// Récupère les paramètres de la requête
$query = $_GET['query'] ?? '';
$banque_id = $_GET['banque_id'] ?? null;

if (!$banque_id) {
    echo json_encode(["error" => "ID de banque manquant."]);
    exit;
}

try {
    // Construit la requête SQL avec filtre de recherche
    $sql = "SELECT * FROM agence WHERE banque_id = :banque_id";
    if (!empty($query)) {
        $sql .= " AND (code LIKE :query OR label LIKE :query OR adresse LIKE :query)";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":banque_id", $banque_id, PDO::PARAM_INT);
    if (!empty($query)) {
        $stmt->bindValue(":query", "%$query%", PDO::PARAM_STR);
    }

    $stmt->execute();
    $agences = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourne les données sous forme de JSON
    echo json_encode($agences);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur: " . $e->getMessage()]);
}
?>