<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once('../../db_connection/db_conn.php');

// Fonction pour nettoyer les données
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Vérifier si la requête est une requête AJAX pour vérifier l'existence d'un numéro de garantie
if (isset($_GET['check_num_garantie'])) {
    $num_garantie = cleanInput($_GET['check_num_garantie']);
    $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
    
    try {
        $params = [':num_garantie' => $num_garantie];
        $sql = "SELECT COUNT(*) FROM garantie WHERE num_garantie = :num_garantie";
        
        if ($exclude_id) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $count = $stmt->fetchColumn();
        
        // Préparer la réponse
        $response = [
            'exists' => ($count > 0),
            'message' => ($count > 0) ? 'Ce numéro de garantie existe déjà.' : 'Ce numéro de garantie est disponible.'
        ];
        
        // Envoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        // En cas d'erreur
        $response = [
            'error' => true,
            'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Vérifier si la requête est une requête AJAX pour vérifier l'existence d'un document
if (isset($_GET['check_document'])) {
    $document_name = cleanInput($_GET['check_document']);
    $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
    
    try {
        $params = [':nom_document' => $document_name];
        $sql = "SELECT COUNT(*) FROM document_garantie WHERE nom_document = :nom_document";
        
        if ($exclude_id) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $count = $stmt->fetchColumn();
        
        // Préparer la réponse
        $response = [
            'exists' => ($count > 0),
            'message' => ($count > 0) ? 'Ce document existe déjà dans la base de données.' : 'Document valide.'
        ];
        
        // Envoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        // En cas d'erreur
        $response = [
            'error' => true,
            'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Traitement du formulaire d'ajout de garantie
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_garantie'])) {
    try {
        // Récupérer et nettoyer les données du formulaire
        $num_garantie = cleanInput($_POST['num_garantie']);
        $montant = cleanInput($_POST['montant']);
        // Nettoyer le montant (enlever les espaces et remplacer les virgules par des points)
        $montant = str_replace([' ', ','], ['', '.'], $montant);
        $monnaie_id = cleanInput($_POST['monnaie_id']);
        $direction_id = cleanInput($_POST['direction_id']);
        $fournisseur_id = cleanInput($_POST['fournisseur_id']);
        $date_creation = cleanInput($_POST['date_creation']);
        $date_emission = cleanInput($_POST['date_emission']);
        $date_validite = cleanInput($_POST['date_validite']);
        $agence_id = cleanInput($_POST['agence_id']);
        $appel_offre_id = isset($_POST['appel_offre_id']) ? cleanInput($_POST['appel_offre_id']) : null;
        
        // Tableau pour stocker les erreurs
        $errors = [];
        
        // Validation côté serveur
        if (empty($num_garantie)) {
            $errors[] = "Le numéro de garantie est requis.";
        }
        
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $errors[] = "Le montant doit être un nombre positif.";
        }
        
        if (empty($date_creation) || empty($date_emission) || empty($date_validite)) {
            $errors[] = "Toutes les dates sont requises.";
        }
        
        if (empty($monnaie_id) || empty($direction_id) || empty($fournisseur_id) || empty($agence_id) || empty($appel_offre_id)) {
            $errors[] = "Tous les champs de sélection sont requis.";
        }
        
        // Vérifier si le numéro de garantie existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM garantie WHERE num_garantie = :num_garantie");
        $stmt->bindParam(':num_garantie', $num_garantie);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Ce numéro de garantie existe déjà dans la base de données.";
        }
        
        // Traitement du fichier PDF
        $pdf_path = null;
        $pdf_name = null;
        if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] == 0) {
            $allowed_types = ['application/pdf'];
            $max_size = 5 * 1024 * 1024; // 5 MB
            
            $file_type = $_FILES['document_pdf']['type'];
            $file_size = $_FILES['document_pdf']['size'];
            $pdf_name = $_FILES['document_pdf']['name'];
            
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = "Seuls les fichiers PDF sont autorisés.";
            }
            
            if ($file_size > $max_size) {
                $errors[] = "La taille du fichier ne doit pas dépasser 5 MB.";
            }
            
            // Vérifier si le document existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_garantie WHERE nom_document = :nom_document");
            $stmt->bindParam(':nom_document', $pdf_name);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Un document avec ce nom existe déjà dans la base de données.";
            }
            
            if (empty($errors)) {
                // Créer le répertoire de stockage s'il n'existe pas
                $upload_dir = '../uploads/garanties/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Générer un nom de fichier unique
                $file_name = $num_garantie . '_' . date('Ymd_His') . '.pdf';
                $pdf_path = $upload_dir . $file_name;
                
                // Déplacer le fichier téléchargé
                if (!move_uploaded_file($_FILES['document_pdf']['tmp_name'], $pdf_path)) {
                    $errors[] = "Erreur lors du téléchargement du fichier.";
                }
            }
        } else {
            $errors[] = "Le document PDF est requis.";
        }
        
        // Si des erreurs sont présentes, rediriger avec les erreurs
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode("<br>", $errors);
            $_SESSION['form_data'] = $_POST;
            header("Location: ../../Pages/Garantie/add_garantie.php");
            exit();
        }
        
        // Commencer une transaction
        $pdo->beginTransaction();
        
        // 1. Insérer dans la table garantie
        $sql = "INSERT INTO garantie (
                    num_garantie, 
                    date_creation, 
                    date_emission, 
                    date_validite, 
                    montant, 
                    direction_id, 
                    fournisseur_id, 
                    monnaie_id, 
                    agence_id,
                    appel_offre_id
                ) VALUES (
                    :num_garantie, 
                    :date_creation, 
                    :date_emission, 
                    :date_validite, 
                    :montant, 
                    :direction_id, 
                    :fournisseur_id, 
                    :monnaie_id, 
                    :agence_id,
                    :appel_offre_id
                )";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':num_garantie' => $num_garantie,
            ':date_creation' => $date_creation,
            ':date_emission' => $date_emission,
            ':date_validite' => $date_validite,
            ':montant' => $montant,
            ':direction_id' => $direction_id,
            ':fournisseur_id' => $fournisseur_id,
            ':monnaie_id' => $monnaie_id,
            ':agence_id' => $agence_id,
            ':appel_offre_id' => $appel_offre_id
        ]);
        
        // Récupérer l'ID de la garantie insérée
        $garantie_id = $pdo->lastInsertId();
        
        // 2. Insérer dans la table document_garantie
        $sql_document = "INSERT INTO document_garantie (
                            nom_document,
                            document_path,
                            garantie_id
                        ) VALUES (
                            :nom_document,
                            :document_path,
                            :garantie_id
                        )";
        
        $stmt_document = $pdo->prepare($sql_document);
        $stmt_document->execute([
            ':nom_document' => $pdf_name,
            ':document_path' => $pdf_path,
            ':garantie_id' => $garantie_id
        ]);
        
        // Valider la transaction
        $pdo->commit();

        // Stocker un message de succès dans la session
        $_SESSION['success_message'] = 'La garantie bancaire a été ajoutée avec succès.';
        $_SESSION['show_success_alert'] = true;

        // Rediriger vers la page de liste des garanties
        header('Location: ../../Pages/Garantie/ListeGaranties.php');
        
        // S'assurer que le script s'arrête après la redirection
        if (headers_sent()) {
            echo "<script>window.location.href='../../Pages/Garantie/ListeGaranties.php';</script>";
            exit;
        }
        exit;
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Supprimer le fichier téléchargé en cas d'erreur
        if (isset($pdf_path) && $pdf_path && file_exists($pdf_path)) {
            unlink($pdf_path);
        }
        
        // Stocker un message d'erreur dans la session
        $_SESSION['error_message'] = 'Erreur lors de l\'ajout de la garantie bancaire: ' . $e->getMessage();
        
        // Stocker les données du formulaire pour les réafficher
        $_SESSION['form_data'] = $_POST;
        
        // Rediriger vers le formulaire
        header('Location: ../../Pages/Garantie/add_garantie.php');
        exit;
    }
} 
// Traitement du formulaire de modification de garantie
else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_garantie'])) {
    try {
        // Récupérer l'ID de la garantie à modifier
        $garantie_id = intval($_POST['garantie_id']);
        
        // Récupérer et nettoyer les données du formulaire
        $num_garantie = cleanInput($_POST['num_garantie']);
        $montant = cleanInput($_POST['montant']);
        // Nettoyer le montant (enlever les espaces et remplacer les virgules par des points)
        $montant = str_replace([' ', ','], ['', '.'], $montant);
        $monnaie_id = cleanInput($_POST['monnaie_id']);
        $direction_id = cleanInput($_POST['direction_id']);
        $fournisseur_id = cleanInput($_POST['fournisseur_id']);
        $date_creation = cleanInput($_POST['date_creation']);
        $date_emission = cleanInput($_POST['date_emission']);
        $date_validite = cleanInput($_POST['date_validite']);
        $agence_id = cleanInput($_POST['agence_id']);
        $appel_offre_id = isset($_POST['appel_offre_id']) ? cleanInput($_POST['appel_offre_id']) : null;
        
        // Tableau pour stocker les erreurs
        $errors = [];
        
        // Validation côté serveur
        if (empty($num_garantie)) {
            $errors[] = "Le numéro de garantie est requis.";
        }
        
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $errors[] = "Le montant doit être un nombre positif.";
        }
        
        if (empty($date_creation) || empty($date_emission) || empty($date_validite)) {
            $errors[] = "Toutes les dates sont requises.";
        }
        
        if (empty($monnaie_id) || empty($direction_id) || empty($fournisseur_id) || empty($agence_id) || empty($appel_offre_id)) {
            $errors[] = "Tous les champs de sélection sont requis.";
        }
        
        // Vérifier si le numéro de garantie existe déjà (sauf pour cette garantie)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM garantie WHERE num_garantie = :num_garantie AND id != :garantie_id");
        $stmt->bindParam(':num_garantie', $num_garantie);
        $stmt->bindParam(':garantie_id', $garantie_id);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Ce numéro de garantie existe déjà dans la base de données.";
        }
        
        // Traitement du fichier PDF (optionnel pour la modification)
        $pdf_path = null;
        $pdf_name = null;
        $new_document = false;
        
        if (isset($_FILES['document_pdf']) && $_FILES['document_pdf']['error'] == 0) {
            $new_document = true;
            $allowed_types = ['application/pdf'];
            $max_size = 5 * 1024 * 1024; // 5 MB
            
            $file_type = $_FILES['document_pdf']['type'];
            $file_size = $_FILES['document_pdf']['size'];
            $pdf_name = $_FILES['document_pdf']['name'];
            
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = "Seuls les fichiers PDF sont autorisés.";
            }
            
            if ($file_size > $max_size) {
                $errors[] = "La taille du fichier ne doit pas dépasser 5 MB.";
            }
            
            // Vérifier si le document existe déjà (sauf pour ce document)
            $document_id = isset($_POST['document_id']) ? intval($_POST['document_id']) : 0;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_garantie WHERE nom_document = :nom_document AND id != :document_id");
            $stmt->bindParam(':nom_document', $pdf_name);
            $stmt->bindParam(':document_id', $document_id);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Un document avec ce nom existe déjà dans la base de données.";
            }
            
            if (empty($errors)) {
                // Créer le répertoire de stockage s'il n'existe pas
                $upload_dir = '../uploads/garanties/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Générer un nom de fichier unique
                $file_name = $num_garantie . '_' . date('Ymd_His') . '.pdf';
                $pdf_path = $upload_dir . $file_name;
                
                // Déplacer le fichier téléchargé
                if (!move_uploaded_file($_FILES['document_pdf']['tmp_name'], $pdf_path)) {
                    $errors[] = "Erreur lors du téléchargement du fichier.";
                }
            }
        }
        
        // Si des erreurs sont présentes, rediriger avec les erreurs
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode("<br>", $errors);
            $_SESSION['form_data'] = $_POST;
            header("Location: edit_garantie.php?id=" . $garantie_id);
            exit();
        }
        
        // Commencer une transaction
        $pdo->beginTransaction();
        
        // 1. Mettre à jour la table garantie
        $sql = "UPDATE garantie SET
                    num_garantie = :num_garantie, 
                    date_creation = :date_creation, 
                    date_emission = :date_emission, 
                    date_validite = :date_validite, 
                    montant = :montant, 
                    direction_id = :direction_id, 
                    fournisseur_id = :fournisseur_id, 
                    monnaie_id = :monnaie_id, 
                    agence_id = :agence_id,
                    appel_offre_id = :appel_offre_id
                WHERE id = :garantie_id";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':num_garantie' => $num_garantie,
            ':date_creation' => $date_creation,
            ':date_emission' => $date_emission,
            ':date_validite' => $date_validite,
            ':montant' => $montant,
            ':direction_id' => $direction_id,
            ':fournisseur_id' => $fournisseur_id,
            ':monnaie_id' => $monnaie_id,
            ':agence_id' => $agence_id,
            ':appel_offre_id' => $appel_offre_id,
            ':garantie_id' => $garantie_id
        ]);
        
        // 2. Mettre à jour ou ajouter le document si un nouveau fichier a été téléchargé
        if ($new_document) {
            // Récupérer l'ancien document pour le supprimer plus tard
            $stmt_old_doc = $pdo->prepare("SELECT * FROM document_garantie WHERE garantie_id = :garantie_id");
            $stmt_old_doc->bindParam(':garantie_id', $garantie_id);
            $stmt_old_doc->execute();
            $old_document = $stmt_old_doc->fetch(PDO::FETCH_ASSOC);
            
            // Mettre à jour le document existant ou en créer un nouveau
            if ($old_document) {
                $sql_document = "UPDATE document_garantie SET
                                    nom_document = :nom_document,
                                    document_path = :document_path
                                WHERE garantie_id = :garantie_id";
            } else {
                $sql_document = "INSERT INTO document_garantie (
                                    nom_document,
                                    document_path,
                                    garantie_id
                                ) VALUES (
                                    :nom_document,
                                    :document_path,
                                    :garantie_id
                                )";
            }
            
            $stmt_document = $pdo->prepare($sql_document);
            $stmt_document->execute([
                ':nom_document' => $pdf_name,
                ':document_path' => $pdf_path,
                ':garantie_id' => $garantie_id
            ]);
            
            // Supprimer l'ancien fichier si nécessaire
            if ($old_document && file_exists($old_document['document_path'])) {
                unlink($old_document['document_path']);
            }
        }
        
        // Valider la transaction
        $pdo->commit();
        
        // Stocker un message de succès dans la session
        $_SESSION['success_message'] = 'La garantie bancaire a été mise à jour avec succès.';
        $_SESSION['show_success_alert'] = true;
        
        // Rediriger vers la page de liste des garanties
        header('Location: ../../Pages/Garantie/ListeGaranties.php');
        
        // S'assurer que le script s'arrête après la redirection
        if (headers_sent()) {
            echo "<script>window.location.href='../../Pages/Garantie/ListeGaranties.php';</script>";
            exit;
        }
        exit;
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Supprimer le fichier téléchargé en cas d'erreur
        if (isset($pdf_path) && $pdf_path && file_exists($pdf_path)) {
            unlink($pdf_path);
        }
        
        // Stocker un message d'erreur dans la session
        $_SESSION['error_message'] = 'Erreur lors de la modification de la garantie bancaire: ' . $e->getMessage();
        
        // Stocker les données du formulaire pour les réafficher
        $_SESSION['form_data'] = $_POST;
        
        // Rediriger vers le formulaire
        header('Location: edit_garantie.php?id=' . $garantie_id);
        exit;
    }
} else {
    // Accès direct au script sans soumission de formulaire
    header("Location: ../../Pages/Garantie/add_garantie.php");
    exit();
}
?>
