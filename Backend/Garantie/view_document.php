<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once('../../db_connection/db_conn.php');

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo "ID de document non spécifié.";
    exit;
}

$document_id = intval($_GET['id']);

try {
    // Récupérer les informations du document
    $stmt = $pdo->prepare("SELECT * FROM document_garantie WHERE id = :id");
    $stmt->bindParam(':id', $document_id);
    $stmt->execute();
    
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        header('Content-Type: text/html; charset=utf-8');
        echo "Document non trouvé.";
        exit;
    }
    
    // Vérifier si le fichier existe
    if (!file_exists($document['document_path'])) {
        header('Content-Type: text/html; charset=utf-8');
        echo "Le fichier n'existe pas sur le serveur.";
        exit;
    }
    
    // Définir les en-têtes pour afficher le PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($document['document_path']) . '"');
    header('Content-Length: ' . filesize($document['document_path']));
    
    // Lire et afficher le fichier
    readfile($document['document_path']);
    exit;
    
} catch (PDOException $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo "Erreur lors de la récupération du document: " . $e->getMessage();
    exit;
}
?>
