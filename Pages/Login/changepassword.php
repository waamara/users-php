<?php
// Start session and include required files
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$error_message = "";
$success_message = "";
$old_password_error = "";
$new_password_error = "";
$confirm_password_error = "";
$image_error = "";
$password_changed = false;

// Get user information
$stmt = $pdo->prepare("SELECT nom_user, prenom_user, username, Role, password FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_password = $user['password'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate form data
    $is_valid = validateFormData($old_password, $new_password, $confirm_password, $current_password, 
                               $old_password_error, $new_password_error, $confirm_password_error);
    
    // Validate image
    $image_valid = validateImage($_FILES['image'], $image_error);
    
    // Process if all validations pass
    if ($is_valid && $image_valid) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Update password
            updatePassword($pdo, $new_password, $user_id);
            
            // Process and save image
            processImage($pdo, $_FILES['image'], $user_id);
            
            // Update first login status
            updateFirstLoginStatus($pdo, $user_id);
            
            // Commit transaction
            $pdo->commit();
            
            // Set success flag
            $password_changed = true;
            $success_message = "Votre mot de passe et votre photo de profil ont été mis à jour avec succès.";
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $error_message = "Erreur lors de la mise à jour: " . $e->getMessage();
        }
    }
}

/**
 * Validate form data
 */
function validateFormData($old_password, $new_password, $confirm_password, $current_password, 
                        &$old_password_error, &$new_password_error, &$confirm_password_error) {
    $is_valid = true;
    
    // Validate old password
    if (empty($old_password)) {
        $old_password_error = "Veuillez entrer votre mot de passe actuel";
        $is_valid = false;
    } elseif (!password_verify($old_password, $current_password)) {
        $old_password_error = "Le mot de passe actuel est incorrect";
        $is_valid = false;
    }
    
    // Validate new password
    if (empty($new_password)) {
        $new_password_error = "Veuillez entrer un nouveau mot de passe";
        $is_valid = false;
    } elseif (strlen($new_password) < 8) {
        $new_password_error = "Le mot de passe doit contenir au moins 8 caractères";
        $is_valid = false;
    }
    
    // Validate password confirmation
    if (empty($confirm_password)) {
        $confirm_password_error = "Veuillez confirmer votre nouveau mot de passe";
        $is_valid = false;
    } elseif ($new_password !== $confirm_password) {
        $confirm_password_error = "Les mots de passe ne correspondent pas";
        $is_valid = false;
    }
    
    return $is_valid;
}

/**
 * Validate uploaded image
 */
function validateImage($file, &$image_error) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        $image_error = "Veuillez télécharger une photo de profil";
        return false;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $image_error = "Erreur lors du téléchargement de l'image (Code: " . $file['error'] . ")";
        return false;
    }
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        $image_error = "Seuls les formats JPG, JPEG, PNG et GIF sont acceptés";
        return false;
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $image_error = "La taille de l'image ne doit pas dépasser 5MB";
        return false;
    }
    
    return true;
}

/**
 * Update user password
 */
function updatePassword($pdo, $new_password, $user_id) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

/**
 * Process and save image
 */
function processImage($pdo, $file, $user_id) {
    // Create upload directory if it doesn't exist
    $upload_dir = "../../uploads/profile_images/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid('profile_', true) . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception("Erreur lors du téléchargement de l'image");
    }
    
    // Save image info to database
    $image_name = $file['name'];
    $image_path = 'uploads/profile_images/' . $new_filename;
    
    // Check if user already has a profile image
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM image_users WHERE usersid = :user_id");
    $check_stmt->bindParam(':user_id', $user_id);
    $check_stmt->execute();
    
    if ($check_stmt->fetchColumn() > 0) {
        // Update existing image
        $update_stmt = $pdo->prepare("UPDATE image_users SET nom_image = :nom_image, image_path = :image_path 
                                     WHERE usersid = :user_id");
        $update_stmt->bindParam(':nom_image', $image_name);
        $update_stmt->bindParam(':image_path', $image_path);
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();
    } else {
        // Insert new image
        $insert_stmt = $pdo->prepare("INSERT INTO image_users (nom_image, image_path, usersid) 
                                     VALUES (:nom_image, :image_path, :user_id)");
        $insert_stmt->bindParam(':nom_image', $image_name);
        $insert_stmt->bindParam(':image_path', $image_path);
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->execute();
    }
}

/**
 * Update first login status
 */
function updateFirstLoginStatus($pdo, $user_id) {
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM first_login WHERE user_id = :user_id");
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->execute();
    
    if ($check_stmt->fetchColumn() == 0) {
        $insert_stmt = $pdo->prepare("INSERT INTO first_login (user_id) VALUES (:user_id)");
        $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insert_stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement de mot de passe</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <style>
        /* Keep the existing CSS styles */
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");
        :root {
            --primary: #1a56db;
            --primary-light: #3b82f6;
            --primary-dark: #1e40af;
            --primary-hover: #2563eb;
            --primary-focus: rgba(59, 130, 246, 0.25);
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --border-radius: 0.5rem;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        #dynamic-content {
            padding: 0;
            margin-top: -10px;
            animation: fadeIn 0.3s ease-in-out;
            overflow-y: auto;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
            font-family: "Poppins", sans-serif;
        }

        .main-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .main-title i {
            margin-right: 0.5rem;
            font-size: 1.5rem;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 0;
            transition: var(--transition);
            min-height: auto;
            max-height: 100%;
            overflow-y: auto;
        }

        .card:hover {
            box-shadow: var(--box-shadow-lg);
        }

        .card-body {
            padding: 1.25rem;
        }

        .card-subtitle {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .card-subtitle i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .divider {
            height: 1px;
            background-color: var(--gray-200);
            margin: 0.75rem 0;
            border: none;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -0.25rem;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0.25rem;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0.25rem;
        }

        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0.25rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.25rem;
            color: var(--gray-700);
            font-size: 0.9rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-control {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-focus);
            outline: none;
        }

        .input-group {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }

        .input-group input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-focus);
            outline: none;
        }

        .input-group i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .input-group input:focus+i {
            color: var(--primary);
        }

        .toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .toggle-password:hover {
            color: var(--gray-700);
        }

        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
            min-height: 1rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .password-strength {
            margin-top: 0.25rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .strength-meter {
            height: 4px;
            background-color: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .strength-meter-fill {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }

        .strength-text {
            font-size: 0.8rem;
            color: var(--gray-600);
        }

        .weak {
            width: 33%;
            background-color: var(--danger);
        }

        .medium {
            width: 66%;
            background-color: var(--warning);
        }

        .strong {
            width: 100%;
            background-color: var(--success);
        }

        .password-requirements {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background-color: var(--gray-50);
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .password-requirements h5 {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.4rem;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.2rem;
            font-size: 0.8rem;
            color: var(--gray-600);
        }

        .requirement-item i {
            margin-right: 0.4rem;
            font-size: 0.9rem;
        }

        .requirement-valid {
            color: var(--success);
        }

        .requirement-invalid {
            color: var(--gray-400);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            border: 1px solid transparent;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary));
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .btn-secondary {
            color: #fff;
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-secondary:hover {
            background-color: #4b5563;
            border-color: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(100, 116, 139, 0.25);
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            gap: 0.5rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .alert {
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--danger);
            color: #b91c1c;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success);
            color: #065f46;
        }

        .alert i {
            font-size: 1.1rem;
        }

        .detail-value {
            font-weight: 500;
            color: var(--gray-800);
        }

        .required-asterisk {
            color: var(--danger);
            margin-left: 2px;
        }

        .welcome-message {
            background-color: var(--primary);
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            position: relative;
            overflow: hidden;
        }

        .welcome-message::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 60%);
            z-index: 0;
            animation: pulse 15s infinite linear;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.5;
            }
            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }

        .welcome-message h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            position: relative;
            z-index: 1;
        }

        .welcome-message p {
            font-size: 0.9rem;
            opacity: 0.9;
            max-width: 800px;
            position: relative;
            z-index: 1;
            margin-bottom: 0;
        }

        .omc {
            display: flex;
            flex-direction: row; 
            justify-content: space-evenly;
        }
        
        .file-upload-container {
            position: relative;
            width: 30%;
            margin-top: 60px; 
            margin-left: -80px;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background-color: #f9fafb;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-upload-label:hover {
            background-color: #f3f4f6;
        }

        .upload-icon {
            color: #3b82f6;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .upload-text {
            color: #6b7280;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .file-input {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }
        
        /* Added styles for image preview */
        .image-preview {
            margin-top: 10px;
            width: 100%;
            max-width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            display: none;
            border: 3px solid var(--primary-light);
        }
        
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }

            .col-md-4,
            .col-md-6,
            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .welcome-message h1 {
                font-size: 1.3rem;
            }
            
            .omc {
                flex-direction: column;
            }
            
            .file-upload-container {
                width: 100%;
                margin-left: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <main id="dynamic-content">
        <div class="container">
            <div class="card">
                <!-- Welcome message -->
                <div class="welcome-message">
                    <h1>Salut <?php echo htmlspecialchars($user['prenom_user'] . ' ' . $user['nom_user']); ?> !</h1>
                    <p>Bienvenue dans le système. Comme c'est votre première connexion, vous devez changer votre mot de passe et ajouter une photo de profil.</p>
                </div>

                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-shield'></i> Sécurisation de votre compte
                    </h5>
                    <hr class="divider">

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class='bx bx-error-circle'></i>
                            <span><?php echo $error_message; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <i class='bx bx-check-circle'></i>
                            <span><?php echo $success_message; ?></span>
                        </div>
                    <?php endif; ?>

                    <form id="passwordForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                        <div class="omc">
                            <!-- Profile Photo Upload -->
                            <div class="file-upload-container">
                                <label for="imageUpload" class="form-label">
                                    Ajouter Votre Photo de Profil <span class="required-asterisk">*</span>
                                </label>
                                <label for="imageUpload" class="file-upload-label">
                                    <div class="upload-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                    </div>
                                    <div class="upload-text" id="upload-text">Cliquez ici pour sélectionner une Photo</div>
                                </label>
                                <input type="file" id="imageUpload" name="image" accept="image/*" class="file-input">
                                <div class="image-preview" id="imagePreview">
                                    <img src="/placeholder.svg" alt="Aperçu de l'image" id="preview-img">
                                </div>
                                <span class="error-message"><?php echo $image_error; ?></span>
                            </div>

                            <!-- Password Change Form -->
                            <div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="old_password" class="form-label">
                                            <i class='bx bx-lock-alt'></i> Mot de passe actuel <span class="required-asterisk">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Entrez votre mot de passe actuel">
                                            <i class='bx bx-lock-alt'></i>
                                            <button type="button" class="toggle-password" tabindex="-1">
                                                <i class='bx bx-hide'></i>
                                            </button>
                                        </div>
                                        <span class="error-message"><?php echo $old_password_error; ?></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="new_password" class="form-label">
                                                <i class='bx bx-lock'></i> Nouveau mot de passe <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Entrez votre nouveau mot de passe">
                                                <i class='bx bx-lock'></i>
                                                <button type="button" class="toggle-password" tabindex="-1">
                                                    <i class='bx bx-hide'></i>
                                                </button>
                                            </div>
                                            <div class="password-strength">
                                                <div class="strength-meter">
                                                    <div class="strength-meter-fill" id="strength-meter-fill"></div>
                                                </div>
                                                <span class="strength-text" id="strength-text">Force du mot de passe</span>
                                            </div>
                                            <span class="error-message"><?php echo $new_password_error; ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="confirm_password" class="form-label">
                                                <i class='bx bx-lock-open'></i> Confirmer le mot de passe <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirmez votre nouveau mot de passe">
                                                <i class='bx bx-lock-open'></i>
                                                <button type="button" class="toggle-password" tabindex="-1">
                                                    <i class='bx bx-hide'></i>
                                                </button>
                                            </div>
                                            <span class="error-message"><?php echo $confirm_password_error; ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="password-requirements">
                                            <h5>Le mot de passe doit contenir :</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="requirement-item" id="length-requirement">
                                                        <i class='bx bx-x-circle requirement-invalid'></i>
                                                        Au moins 8 caractères
                                                    </div>
                                                    <div class="requirement-item" id="uppercase-requirement">
                                                        <i class='bx bx-x-circle requirement-invalid'></i>
                                                        Au moins une lettre majuscule
                                                    </div>
                                                    <div class="requirement-item" id="lowercase-requirement">
                                                        <i class='bx bx-x-circle requirement-invalid'></i>
                                                        Au moins une lettre minuscule
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="requirement-item" id="number-requirement">
                                                        <i class='bx bx-x-circle requirement-invalid'></i>
                                                        Au moins un chiffre
                                                    </div>
                                                    <div class="requirement-item" id="special-requirement">
                                                        <i class='bx bx-x-circle requirement-invalid'></i>
                                                        Au moins un caractère spécial
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-check'></i> Enregistrer les modifications
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancel-btn">
                                <i class='bx bx-x'></i> Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Inclusion du script JavaScript -->
    <script>
        // Password changed flag
        var passwordChanged = <?php echo $password_changed ? 'true' : 'false'; ?>;
    </script>
    <script src="js/changepassword.js"></script>
</body>
</html>
<?php require_once('../Template/footer.php'); ?>