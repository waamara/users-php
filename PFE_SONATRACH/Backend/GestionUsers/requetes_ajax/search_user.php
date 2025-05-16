<?php
require_once("../../db_connection/db_conn.php");
header("Content-Type: application/json");

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query === '') {
    // Si rien à chercher, renvoyer tous les users
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Requête préparée avec LIKE sur nom_user, prenom_user et username
    $likeQuery = "%$query%";
    $sql = "SELECT * FROM users WHERE nom_user LIKE ? OR prenom_user LIKE ? OR username LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$likeQuery, $likeQuery, $likeQuery]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($users);
