<?php
require_once("../../../db_connection/db_conn.php");
header("Content-Type: application/json");

try {
    $sql = "SELECT * FROM users";
    $stmt = $pdo->query($sql);

    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = $row;
    }

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(["message" => "Database query failed: " . $e->getMessage()]);
}
?>
