<?php
// Include database connection
require_once("../../db_connection/db_conn.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to the form page if accessed directly
    header("Location: authentification.php");
    exit;
}

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Validate and sanitize inputs
$garantie_id = isset($_POST['garantie_id']) ? intval($_POST['garantie_id']) : 0;
$num_auth = isset($_POST['num_auth']) ? trim($_POST['num_auth']) : '';
$date_depo = isset($_POST['date_depo']) ? trim($_POST['date_depo']) : '';
$date_auth = isset($_POST['date_auth']) ? trim($_POST['date_auth']) : '';

// Validate inputs
$errors = [];

// Check if garantie_id is valid
if ($garantie_id <= 0) {
    $errors[] = "ID de garantie invalide.";
}

// Check if num_auth is provided
if (empty($num_auth)) {
    $errors[] = "Le numéro d'authentification est obligatoire.";
} else {
    // Check if num_auth is unique
    $sql_check = "SELECT COUNT(*) as count FROM authentification WHERE num_auth = :num_auth";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':num_auth', $num_auth, PDO::PARAM_STR);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $errors[] = "Ce numéro d'authentification existe déjà.";
    }
}

// Check if dates are provided
if (empty($date_depo)) {
    $errors[] = "La date de dépôt est obligatoire.";
}

if (empty($date_auth)) {
    $errors[] = "La date d'authentification est obligatoire.";
} else if (strtotime($date_auth) < strtotime($date_depo)) {
    $errors[] = "La date d'authentification doit être postérieure à la date de dépôt.";
}

// Check if file is uploaded
if (!isset($_FILES['document_scanne']) || $_FILES['document_scanne']['error'] == UPLOAD_ERR_NO_FILE) {
    $errors[] = "Le document scanné est obligatoire.";
} else {
    $file = $_FILES['document_scanne'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    // Get file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Allowed extensions
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
    
    // Check if file extension is allowed
    if (!in_array($file_ext, $allowed_ext)) {
        $errors[] = "Type de fichier non autorisé. Seuls les fichiers PDF, JPG, JPEG et PNG sont acceptés.";
    }
    
    // Check if file size is within limit (10MB)
    if ($file_size > 10485760) {
        $errors[] = "Le fichier est trop volumineux. La taille maximale autorisée est de 10MB.";
    }
    
    // Check if file upload was successful
    if ($file_error !== UPLOAD_ERR_OK) {
        $errors[] = "Une erreur s'est produite lors du téléchargement du fichier.";
    }
    
    // Check if file name is unique
    $sql_check = "SELECT COUNT(*) as count FROM document_auth WHERE nom_document = :file_name";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':file_name', $file_name, PDO::PARAM_STR);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $errors[] = "Un fichier avec ce nom existe déjà.";
    }
}

// If there are errors, redirect back with error messages
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: authentification.php?garantie_id=" . $garantie_id);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Insert authentication data
    $sql_auth = "INSERT INTO authentification (garantie_id, num_auth, date_depo, date_auth, created_at) 
                VALUES (:garantie_id, :num_auth, :date_depo, :date_auth, NOW())";
    $stmt_auth = $pdo->prepare($sql_auth);
    $stmt_auth->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
    $stmt_auth->bindParam(':num_auth', $num_auth, PDO::PARAM_STR);
    $stmt_auth->bindParam(':date_depo', $date_depo, PDO::PARAM_STR);
    $stmt_auth->bindParam(':date_auth', $date_auth, PDO::PARAM_STR);
    $stmt_auth->execute();
    
    // Get the ID of the inserted authentication
    $auth_id = $pdo->lastInsertId();
    
    // Create upload directory if it doesn't exist
    $upload_dir = "../../uploads/authentifications/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $new_file_name = time() . '_' . $file_name;
    $file_path = $upload_dir . $new_file_name;
    
    // Move uploaded file to destination
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Insert document data
        $document_path = "uploads/authentifications/" . $new_file_name;
        $sql_doc = "INSERT INTO document_auth (authentification_id, nom_document, document_path, created_at) 
                    VALUES (:auth_id, :nom_document, :document_path, NOW())";
        $stmt_doc = $pdo->prepare($sql_doc);
        $stmt_doc->bindParam(':auth_id', $auth_id, PDO::PARAM_INT);
        $stmt_doc->bindParam(':nom_document', $file_name, PDO::PARAM_STR);
        $stmt_doc->bindParam(':document_path', $document_path, PDO::PARAM_STR);
        $stmt_doc->execute();
        
        // Commit transaction
        $pdo->commit();
        
        // Set success message
        $_SESSION['success'] = "L'authentification a été enregistrée avec succès.";
        
        // Redirect to the guarantee details page
        header("Location: authentification.php?garantie_id=" . $garantie_id);
        exit;
    } else {
        // Rollback transaction if file upload fails
        $pdo->rollBack();
        $_SESSION['errors'] = ["Une erreur s'est produite lors du téléchargement du fichier."];
        header("Location: authentification.php?garantie_id=" . $garantie_id);
        exit;
    }
} catch (PDOException $e) {
    // Rollback transaction if an error occurs
    $pdo->rollBack();
    $_SESSION['errors'] = ["Une erreur s'est produite: " . $e->getMessage()];
    header("Location: authentification.php?garantie_id=" . $garantie_id);
    exit;
}
?>