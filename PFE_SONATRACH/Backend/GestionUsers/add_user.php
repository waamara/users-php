<?php
require_once("../../../db_connection/db_conn.php");

// Get the JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["message" => "Aucune donnée reçue."]);
    exit;
}

// Extract and sanitize data
$nom = trim($data["nomComplet"]);
$prenom = ""; // (you can change this logic if needed)
$username = trim($data["userName"]);
$compte = trim($data["compte"]);
$motdepasse = trim($data["motDePasse"]);
$structure = trim($data["structure"]);
$role = "user"; // default role
$status = "actif"; // default status

// Simple validation
if (empty($nom) || empty($username) || empty($compte) || empty($motdepasse) || empty($structure)) {
    http_response_code(400);
    echo json_encode(["message" => "Champs requis manquants."]);
    exit;
}

// Insert into DB
$sql = "INSERT INTO users (nom_user, prenom_user, username, password, status, Role, structure)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt->execute([$nom, $prenom, $username, $motdepasse, $status, $role, $structure])) {
    echo json_encode(["message" => "Utilisateur ajouté avec succès."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Erreur lors de l'insertion."]);
}
?>
