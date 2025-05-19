<?php
require_once("../../../db_connection/db_conn.php");


try {

    // Get data from POST request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['id']) || empty($data['code_fournisseur']) || empty($data['nom_fournisseur'])) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
        exit;
    }

    // Update query
    $stmt = $pdo->prepare("
        UPDATE fournisseur 
        SET code_fournisseur = :code_fournisseur, 
            nom_fournisseur = :nom_fournisseur, 
            raison_sociale = :raison_sociale, 
            pays_id = :pays_id 
        WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $data['id'],
        ':code_fournisseur' => $data['code_fournisseur'],
        ':nom_fournisseur' => $data['nom_fournisseur'],
        ':raison_sociale' => $data['raison_sociale'] ?? '',
        ':pays_id' => $data['pays_id'] ?? ''
    ]);

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Fournisseur modifié avec succès']);
} catch (PDOException $e) {
    // Check for duplicate entry error
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo json_encode(['status' => 'error', 'message' => 'Le code fournisseur existe déjà. Veuillez choisir un autre code.']);
    } else {
        // Handle other errors
        echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
    }
}
?>