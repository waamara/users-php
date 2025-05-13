<?php
require_once("../../../db_connection/db_conn.php");

header("Content-Type: application/json");

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

$users = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
} else {
    echo json_encode(["error" => "Erreur lors de la récupération des utilisateurs"]);
}

$conn->close();
