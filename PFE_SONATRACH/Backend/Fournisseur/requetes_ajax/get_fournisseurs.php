<?php

require_once("../../../db_connection/db_conn.php");


try {

    // Query to fetch all fournisseurs with pays label and pays_id
    $query = "
        SELECT 
            fournisseur.id,
            fournisseur.code_fournisseur,
            fournisseur.nom_fournisseur,
            fournisseur.raison_sociale,
            fournisseur.pays_id, -- Include pays_id
            pays.label AS pays_label
        FROM 
            fournisseur
        LEFT JOIN 
            pays
        ON 
            fournisseur.pays_id = pays.id
        ORDER BY 
            fournisseur.id DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch data as an associative array
    $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $fournisseurs]);
} catch (Exception $e) {
    // Return error message as JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>