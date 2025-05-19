<?php
// Configuration de la base de données
require_once '../../../db_connection/config.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
    
    // Validation côté serveur
    $errors = [];
    
    if ($id <= 0) {
        $errors[] = "ID invalide";
    }
    
    if (empty($code)) {
        $errors[] = "Le code est obligatoire";
    } elseif (strlen($code) > 5) {
        $errors[] = "Le code ne peut pas dépasser 5 caractères";
    }
    
    if (empty($libelle)) {
        $errors[] = "Le libellé est obligatoire";
    } elseif (strlen($libelle) > 50) {
        $errors[] = "Le libellé ne peut pas dépasser 50 caractères";
    }
    
    // Vérifier si le code existe déjà (pour un autre enregistrement)
    if (empty($errors)) {
        try {
            $pdo = connectDB();
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM direction WHERE code = :code AND id != :id");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Ce code existe déjà";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur de base de données: " . $e->getMessage();
        }
    }
    
    // S'il n'y a pas d'erreurs, mettre à jour la direction
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE direction SET code = :code, libelle = :libelle WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->bindParam(':libelle', $libelle, PDO::PARAM_STR);
            $stmt->execute();
            
            // Rediriger avec un message de succès
            header("Location: ../../../Pages/Direction/index.php?status=success&message=" . urlencode("Direction modifiée avec succès"));
            exit;
        } catch (PDOException $e) {
            // Rediriger avec un message d'erreur
            header("Location: ../../../Pages/Direction/index.php?status=error&message=" . urlencode("Erreur lors de la modification: " . $e->getMessage()));
            exit;
        }
    } else {
        // Rediriger avec un message d'erreur
        header("Location: ../../../Pages/Direction/index.php?status=error&message=" . urlencode(implode(", ", $errors)));
        exit;
    }
} else {
    // Si le formulaire n'a pas été soumis, rediriger vers la page principale
    header("Location: ../../../Pages/Direction/index.php");
    exit;
}
?>