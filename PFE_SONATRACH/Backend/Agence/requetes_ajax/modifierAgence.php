<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json'); // Définit le type de contenu comme JSON

// Récupère les données JSON envoyées par le frontend
$data = json_decode(file_get_contents('php://input'), true);

// Vérifie si les champs requis sont présents
if (!$data || !isset($data['id'], $data['code'], $data['label'], $data['adresse'])) {
    echo json_encode(["success" => false, "error" => "Données invalides reçues"]);
    exit;
}

$agenceId = intval($data['id']); // Convertit l'ID en entier
$code = trim($data['code']);
$label = trim($data['label']);
$adresse = trim($data['adresse']);

// Valide les champs requis
if (empty($code) || empty($label) || empty($adresse)) {
    echo json_encode(["success" => false, "error" => "Le code, le label et l'adresse sont obligatoires."]);
    exit;
}

try {
    // Vérifie si le code ou le label existe déjà pour une autre agence (en excluant l'enregistrement actuel)
    $stmt = $pdo->prepare("SELECT id FROM agence WHERE (code = :code OR label = :label) AND id != :id");
    $stmt->execute(["code" => $code, "label" => $label, "id" => $agenceId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "error" => "Le code ou le label existe déjà pour une autre agence."]);
        exit;
    }

    // Met à jour l'agence dans la base de données
    $updateStmt = $pdo->prepare("UPDATE agence SET code = :code, label = :label, adresse = :adresse WHERE id = :id");
    $updateStmt->execute(["code" => $code, "label" => $label, "adresse" => $adresse, "id" => $agenceId]);

    if ($updateStmt->rowCount() > 0) {
        echo json_encode(["success" => "Agence mise à jour avec succès."]);
    } else {
        echo json_encode(["success" => false, "error" => "Aucune modification effectuée."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur: " . $e->getMessage()]);
}
?>