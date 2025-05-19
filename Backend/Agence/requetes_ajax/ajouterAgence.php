<?php
require_once '../../../db_connection/db_conn.php';

// Read JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is received
if (!$data || !isset($data["code"]) || !isset($data["label"]) || !isset($data["adresse"]) || !isset($data["banque_id"])) {
    echo json_encode(["success" => false, "error" => "Données invalides reçues"]);
    exit;
}

$code = trim($data["code"]);
$label = trim($data["label"]);
$adresse = trim($data["adresse"]);
$banque_id = $data["banque_id"]; // Retrieve the bank ID from the payload

// Validate required fields
if (empty($code) || empty($label) || empty($adresse) || empty($banque_id)) {
    echo json_encode(["success" => false, "error" => "Le code, le label, l'adresse et l'ID de la banque sont obligatoires."]);
    exit;
}

try {
    // Insert the new agence record
    $stmt = $pdo->prepare("INSERT INTO agence (code, label, adresse, banque_id) VALUES (:code, :label, :adresse, :banque_id)");
    $stmt->execute(["code" => $code, "label" => $label, "adresse" => $adresse, "banque_id" => $banque_id]);

    // Return success response
    echo json_encode(["success" => "Agence ajoutée avec succès."]);
} catch (PDOException $e) {
    // Handle database errors gracefully
    echo json_encode(["success" => false, "error" => "Erreur: " . $e->getMessage()]);
}
?>