<?php
require_once("../../../db_connection/db_conn.php");
header("Content-Type: application/json");

// Get search query from GET parameter
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

try {
    if (empty($query)) {
        // If no query, return all users (reuse existing get_users.php)
        include 'get_users.php';
        exit;
    }
    
    // If we have a search query, perform the search
    $searchTerm = "%$query%";
    
    $sql = "
        SELECT 
            users.id,
            users.nom_user,
            users.prenom_user,
            users.username,
            users.status,
            direction.libelle AS direction,
            role.nom_role AS role
        FROM users
        LEFT JOIN direction ON users.structure = direction.id
        LEFT JOIN role ON users.Role = role.id
        WHERE 
            users.nom_user LIKE ? OR 
            users.prenom_user LIKE ? OR 
            users.username LIKE ? OR
            direction.libelle LIKE ? OR
            role.nom_role LIKE ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = $row;
    }

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(["message" => "Database query failed: " . $e->getMessage()]);
}
?>