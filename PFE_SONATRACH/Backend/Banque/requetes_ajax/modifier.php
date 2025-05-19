<?php
require_once '../../../db_connection/db_conn.php';
header('Content-Type: application/json'); // Définit le type de contenu comme JSON

// Récupère les données JSON envoyées par le frontend
$data = json_decode(file_get_contents('php://input'), true);

// Vérifie si les champs requis sont présents
if (!$data || !isset($data['id'], $data['code'], $data['designation'])) {
    echo json_encode(["success" => false, "error" => "Données invalides reçues"]);
    exit;
}

$banqueId = intval($data['id']); // Convertit l'ID en entier
$code = trim($data['code']);
$label = trim($data['designation']);

// Valide les champs requis
if (empty($code) || empty($label)) {
    echo json_encode(["success" => false, "error" => "Le code et la désignation sont obligatoires."]);
    exit;
}

try {
    // Vérifie si le code ou la désignation existe déjà pour une autre banque (en excluant l'enregistrement actuel)
    $stmt = $pdo->prepare("SELECT id FROM banque WHERE (code = :code OR label = :label) AND id != :id");
    $stmt->execute(["code" => $code, "label" => $label, "id" => $banqueId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "error" => "Le code ou la désignation existe déjà pour une autre banque."]);
        exit;
    }

    // Met à jour la banque dans la base de données
    $updateStmt = $pdo->prepare("UPDATE banque SET code = :code, label = :label WHERE id = :id");
    $updateStmt->execute(["code" => $code, "label" => $label, "id" => $banqueId]);

    if ($updateStmt->rowCount() > 0) {
        echo json_encode(["success" => "Banque mise à jour avec succès."]);
    } else {
        echo json_encode(["success" => false, "error" => "Aucune modification effectuée."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur: " . $e->getMessage()]);
}
?>