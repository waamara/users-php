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
    header("Location: liberation.php");
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
$num = isset($_POST['num']) ? trim($_POST['num']) : '';
$date_liberation = isset($_POST['date_liberation']) ? trim($_POST['date_liberation']) : '';

// Validate inputs
$errors = [];

// Check if garantie_id is valid
if ($garantie_id <= 0) {
    $errors[] = "ID de garantie invalide.";
}

// Check if num is provided
if (empty($num)) {
    $errors[] = "Le numéro de libération est obligatoire.";
} else {
    // Check if num is unique
    $sql_check = "SELECT COUNT(*) as count FROM liberation WHERE num = :num";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':num', $num, PDO::PARAM_STR);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $errors[] = "Ce numéro de libération existe déjà.";
    }
}

// Check if date_liberation is provided
if (empty($date_liberation)) {
    $errors[] = "La date de libération est obligatoire.";
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
    $sql_check = "SELECT COUNT(*) as count FROM document_liberation WHERE nom_document = :file_name";
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
    header("Location: liberation.php?garantie_id=" . $garantie_id);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Insert liberation data
    $sql_liberation = "INSERT INTO liberation (garantie_id, num, date_liberation, created_at) 
                VALUES (:garantie_id, :num, :date_liberation, NOW())";
    $stmt_liberation = $pdo->prepare($sql_liberation);
    $stmt_liberation->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
    $stmt_liberation->bindParam(':num', $num, PDO::PARAM_STR);
    $stmt_liberation->bindParam(':date_liberation', $date_liberation, PDO::PARAM_STR);
    $stmt_liberation->execute();
    
    // Get the ID of the inserted liberation
    $liberation_id = $pdo->lastInsertId();
    
    // Create upload directory if it doesn't exist
    $upload_dir = "../../uploads/liberations/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $new_file_name = time() . '_' . $file_name;
    $file_path = $upload_dir . $new_file_name;
    
    // Move uploaded file to destination
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Insert document data
        $document_path = "uploads/liberations/" . $new_file_name;
        $sql_doc = "INSERT INTO document_liberation (liberation_id, nom_document, document_path, created_at) 
                    VALUES (:liberation_id, :nom_document, :document_path, NOW())";
        $stmt_doc = $pdo->prepare($sql_doc);
        $stmt_doc->bindParam(':liberation_id', $liberation_id, PDO::PARAM_INT);
        $stmt_doc->bindParam(':nom_document', $file_name, PDO::PARAM_STR);
        $stmt_doc->bindParam(':document_path', $document_path, PDO::PARAM_STR);
        $stmt_doc->execute();
        
        // Commit transaction
        $pdo->commit();
        
        // Set success message
        $_SESSION['success'] = "La libération a été enregistrée avec succès.";
        
        // Redirect to the guarantee details page
        header("Location: liberation.php?garantie_id=" . $garantie_id);
        exit;
    } else {
        // Rollback transaction if file upload fails
        $pdo->rollBack();
        $_SESSION['errors'] = ["Une erreur s'est produite lors du téléchargement du fichier."];
        header("Location: liberation.php?garantie_id=" . $garantie_id);
        exit;
    }
} catch (PDOException $e) {
    // Rollback transaction if an error occurs
    $pdo->rollBack();
    $_SESSION['errors'] = ["Une erreur s'est produite: " . $e->getMessage()];
    header("Location: liberation.php?garantie_id=" . $garantie_id);
    exit;
}
?>