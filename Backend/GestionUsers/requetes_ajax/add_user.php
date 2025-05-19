<?php
require_once("../../../db_connection/db_conn.php");
header("Content-Type: application/json");

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and validate input data
$data = json_decode(file_get_contents("php://input"), true);

$nomComplet = isset($data['nomComplet']) ? trim($data['nomComplet']) : null;
$userName = isset($data['userName']) ? trim($data['userName']) : null;
$compte = isset($data['compte']) ? trim($data['compte']) : null;
$motDePasse = isset($data['motDePasse']) ? trim($data['motDePasse']) : null;
$structure = isset($data['structure']) ? trim($data['structure']) : null;
$role = isset($data['role']) ? trim($data['role']) : null;

// Validate all required fields
if (!$nomComplet || !$userName || !$compte || !$motDePasse || !$structure || !$role) {
    echo json_encode([
        "success" => false,
        "message" => "Tous les champs sont requis."
    ]);
    exit;
}

try {
    // Check if username already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $checkStmt->execute([$userName]);
    $userExists = $checkStmt->fetchColumn() > 0;
    
    if ($userExists) {
        echo json_encode([
            "success" => false,
            "message" => "Ce nom d'utilisateur existe déjà. Veuillez en choisir un autre."
        ]);
        exit;
    }
    
    // Séparer nom et prénom
    $nameParts = explode(" ", $nomComplet, 2);
    $nom_user = $nameParts[0];
    $prenom_user = isset($nameParts[1]) ? $nameParts[1] : "";

    // Déterminer le statut
    $status = strtolower($compte) === 'actif' ? 1 : 0;
    
    // Hash the password for security
    $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);

    // Préparer la requête d'insertion
    $sql = "INSERT INTO users (nom_user, prenom_user, username, password, structure, Role, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$nom_user, $prenom_user, $userName, $hashedPassword, $structure, $role, $status]);
    
    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Utilisateur ajouté avec succès."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Erreur lors de l'ajout de l'utilisateur."
        ]);
    }
} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Database error: " . $e->getMessage());
    
    // Check for duplicate entry error
    if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo json_encode([
            "success" => false,
            "message" => "Ce nom d'utilisateur existe déjà. Veuillez en choisir un autre."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Erreur de base de données: " . $e->getMessage()
        ]);
    }
}
?> 