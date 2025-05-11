<?php
require_once("../../db_connection/db_conn.php");

// Définir l'en-tête pour renvoyer du JSON
header('Content-Type: application/json');

// Récupérer les données JSON de la requête
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['field']) || !isset($data['value'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Données invalides.'
    ]);
    exit;
}

$field = $data['field'];
$value = $data['value'];

// Valider et assainir l'entrée
if (empty($value)) {
    echo json_encode([
        'success' => false,
        'message' => 'Ce champ est obligatoire.'
    ]);
    exit;
}

try {
    // Vérifier l'unicité en fonction du type de champ
    if ($field === 'num') {
        // Vérifier si le numéro de libération existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM liberation WHERE num = :value");
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Ce numéro de libération existe déjà.'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Numéro de libération valide.'
            ]);
        }
    } elseif ($field === 'nom_document') {
        // Vérifier si le nom du document existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_liberation WHERE nom_document = :value");
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Un document avec ce nom existe déjà.'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Nom de document valide.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Champ non reconnu.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}