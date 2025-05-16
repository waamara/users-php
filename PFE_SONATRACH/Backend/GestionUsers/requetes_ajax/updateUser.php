<?php
require_once("../../../db_connection/db_conn.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = isset($data['id']) ? intval($data['id']) : null;
$nomComplet = isset($data['nomComplet']) ? trim($data['nomComplet']) : null;
$userName = isset($data['userName']) ? trim($data['userName']) : null;
$compte = isset($data['compte']) ? trim($data['compte']) : null;
$structure = isset($data['structure']) ? trim($data['structure']) : null;

if ($id && $nomComplet && $userName && $compte && $structure) {
    try {
        $nameParts = explode(" ", $nomComplet, 2);
        $nom_user = $nameParts[0];
        $prenom_user = isset($nameParts[1]) ? $nameParts[1] : "";

        $status = strtolower($compte) === 'actif' ? 1 : 0;

        $sql = "UPDATE users 
                SET nom_user = ?, prenom_user = ?, username = ?, structure = ?, status = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom_user, $prenom_user, $userName, $structure, $status, $id]);

        echo json_encode(["message" => "User successfully updated."]);
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "All fields are required."]);
}
