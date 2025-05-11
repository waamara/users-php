<?php
require_once '../../../db_connection/db_conn.php';

// Read JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is received
if (!$data || !isset($data["code"]) || !isset($data["designation"])) {
    echo json_encode(["success" => false, "error" => "Données invalides reçues"]);
    exit;
}

$code = trim($data["code"]);
$label = trim($data["designation"]);

// Validate required fields
if (empty($code) || empty($label)) {
    echo json_encode(["success" => false, "error" => "Le code et la désignation sont obligatoires."]);
    exit;
}

try {
    // Check if the code already exists
    $stmt = $pdo->prepare("SELECT id FROM banque WHERE code = :code");
    $stmt->execute(["code" => $code]);
    
    if ($stmt->rowCount() > 0) {
        // KEEP THIS EXACT ERROR MESSAGE - it's matched in JavaScript
        echo json_encode(["success" => false, "error" => "Le code existe déjà."]);
        exit;
    }
    
    // Check if the label already exists
    $stmt = $pdo->prepare("SELECT id FROM banque WHERE label = :label");
    $stmt->execute(["label" => $label]);
    
    if ($stmt->rowCount() > 0) {
        // KEEP THIS EXACT ERROR MESSAGE - it's matched in JavaScript
        echo json_encode(["success" => false, "error" => "La désignation existe déjà."]);
        exit;
    }

    // Insert the new bank record
    $stmt = $pdo->prepare("INSERT INTO banque (code, label) VALUES (:code, :label)");
    $stmt->execute(["code" => $code, "label" => $label]);

    echo json_encode(["success" => "Banque ajoutée avec succès."]);
} catch (PDOException $e) {
    // Check for duplicate key error (just in case)
    if ($e->getCode() == '23000') { // Integrity constraint violation
        // KEEP THIS EXACT ERROR MESSAGE - it's matched in JavaScript
        echo json_encode(["success" => false, "error" => "Le code ou la désignation existe déjà."]);
    } else {
        echo json_encode(["success" => false, "error" => "Erreur: " . $e->getMessage()]);
    }
}
?>