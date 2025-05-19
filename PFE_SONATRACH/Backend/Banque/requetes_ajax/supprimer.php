<?php
require_once '../../../db_connection/db_conn.php';

// Lit les données JSON envoyées par le frontend
$data = json_decode(file_get_contents("php://input"), true);

// Vérifie si les données sont reçues et si l'ID est présent
if (!$data || !isset($data["id"])) {
    echo json_encode(["success" => false, "error" => "Données invalides reçues"]);
    exit;
}

$banqueId = intval(trim($data["id"])); // Convertit l'ID en entier pour éviter les injections SQL

// Valide que l'ID n'est pas vide
if (empty($banqueId)) {
    echo json_encode(["success" => false, "error" => "L'ID est obligatoire."]);
    exit;
}

try {
    // Vérifie si la banque existe dans la base de données
    $stmt = $pdo->prepare("SELECT id FROM banque WHERE id = :id");
    $stmt->execute(["id" => $banqueId]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(["success" => false, "error" => "La banque spécifiée n'existe pas."]);
        exit;
    }

    // Supprime la banque correspondante
    $stmt = $pdo->prepare("DELETE FROM banque WHERE id = :id");
    $stmt->execute(["id" => $banqueId]);

    echo json_encode(["success" => "Banque supprimée avec succès."]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur: " . $e->getMessage()]);
}
?>