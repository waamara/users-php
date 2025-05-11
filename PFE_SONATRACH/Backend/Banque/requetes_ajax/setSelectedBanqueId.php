<?php
session_start();

// Read JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['banque_id'])) {
    $_SESSION['selectedBanqueId'] = $data['banque_id'];
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "ID de banque manquant."]);
}
?>