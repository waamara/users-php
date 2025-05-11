<?php
require_once("../../../db_connection/db_conn.php");



header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $amendmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (!$amendmentId) {
        echo json_encode(['success' => false, 'message' => 'ID d\'amendement manquant.']);
        exit;
    }

    try {
        $sql = "SELECT 
                    a.*, 
                    da.nom_document, 
                    da.document_path 
                FROM 
                    amandement a 
                LEFT JOIN 
                    document_amandement da ON a.id = da.Amandement_id 
                WHERE 
                    a.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$amendmentId]);
        $amendment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($amendment) {
            echo json_encode(['success' => true, 'data' => $amendment]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Amendement non trouvé.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
    }
}
?>