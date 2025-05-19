<?php
// Configuration de la base de données
require_once '../../../db_connection/config.php';

// Fonction pour vérifier si un code existe déjà
function checkCodeExists($code, $currentId = 0) {
    try {
        $pdo = connectDB();
        
        $sql = "SELECT COUNT(*) FROM direction WHERE code = :code";
        if ($currentId > 0) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        
        if ($currentId > 0) {
            $stmt->bindParam(':id', $currentId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        return [
            'success' => true,
            'exists' => $count > 0
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erreur lors de la vérification du code: ' . $e->getMessage(),
            'exists' => false
        ];
    }
}

// Traitement des requêtes
header('Content-Type: application/json');

// Requêtes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'checkCode':
            $code = isset($_GET['code']) ? $_GET['code'] : '';
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            echo json_encode(checkCodeExists($code, $id));
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action non reconnue'
            ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>