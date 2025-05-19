<?php
require_once("../../../db_connection/db_conn.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$nomComplet = isset($data['nomComplet']) ? trim($data['nomComplet']) : null;
$userName = isset($data['userName']) ? trim($data['userName']) : null;
$compte = isset($data['compte']) ? trim($data['compte']) : null;
$motDePasse = isset($data['motDePasse']) ? trim($data['motDePasse']) : null;
$structure = isset($data['structure']) ? trim($data['structure']) : null;

if ($nomComplet && $userName && $compte && $motDePasse && $structure) {
    try {
        $nameParts = explode(" ", $nomComplet, 2);
        $nom_user = $nameParts[0];
        $prenom_user = isset($nameParts[1]) ? $nameParts[1] : "";

        // Convertir "actif" ou "desactive" en 1 ou 0
        $status = strtolower($compte) === 'actif' ? 1 : 0;

        $sql = "INSERT INTO users (nom_user, prenom_user, username, password, structure, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom_user, $prenom_user, $userName, $motDePasse, $structure, $status]);

        echo json_encode(["message" => "User successfully added."]);
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "All fields are required."]);
}
