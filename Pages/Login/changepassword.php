<?php
// Démarrer la session
session_start();
require_once("../Template/header.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Inclure la connexion à la base de données
require_once("../../db_connection/db_conn.php");

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT nom_user, prenom_user, username, Role FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer le mot de passe actuel de l'utilisateur depuis la base de données
$stmt_pwd = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
$stmt_pwd->bindParam(':user_id', $_SESSION['user_id']);
$stmt_pwd->execute();
$current_password = $stmt_pwd->fetchColumn();

// Initialiser les variables
$error_message = "";
$success_message = "";
$old_password_error = "";
$new_password_error = "";
$confirm_password_error = "";
$password_changed = false;

// Traitement du formulaire de changement de mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et nettoyer les données
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validation
    $is_valid = true;

    // Vérifier l'ancien mot de passe
    if (empty($old_password)) {
        $old_password_error = "Veuillez entrer votre mot de passe actuel";
        $is_valid = false;
    } elseif (!password_verify($old_password, $current_password)) {
        // NOTE: Si le mot de passe était haché, il faudrait utiliser password_verify() comme ceci:
        // elseif (!password_verify($old_password, $current_password)) {
        $old_password_error = "Le mot de passe actuel est incorrect";
        $is_valid = false;
    }

    // Vérifier le nouveau mot de passe
    if (empty($new_password)) {
        $new_password_error = "Veuillez entrer un nouveau mot de passe";
        $is_valid = false;
    } elseif (strlen($new_password) < 8) {
        $new_password_error = "Le mot de passe doit contenir au moins 8 caractères";
        $is_valid = false;
    }

    // Vérifier la confirmation du mot de passe
    if (empty($confirm_password)) {
        $confirm_password_error = "Veuillez confirmer votre nouveau mot de passe";
        $is_valid = false;
    } elseif ($new_password !== $confirm_password) {
        $confirm_password_error = "Les mots de passe ne correspondent pas";
        $is_valid = false;
    }

    // Si tout est valide, mettre à jour le mot de passe
    if ($is_valid) {
        try {
            // Hacher le nouveau mot de passe
            $hashed_password = $new_password;

            // Mettre à jour le mot de passe dans la base de données
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $update_stmt->bindParam(':password', $hashed_password);
            $update_stmt->bindParam(':user_id', $_SESSION['user_id']);
            $update_stmt->execute();

            // Vérifie si une entrée existe déjà
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM first_login WHERE user_id = :user_id");
            $check_stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $check_stmt->execute();
            $exists = $check_stmt->fetchColumn();

            if (!$exists) {
                // Insère une nouvelle ligne si elle n'existe pas
                $insert_stmt = $pdo->prepare("INSERT INTO first_login (user_id) VALUES (:user_id)");
                $insert_stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $insert_stmt->execute();
            }

            // Définir un indicateur de succès pour JavaScript
            $password_changed = true;
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la mise à jour du mot de passe: " . $e->getMessage();
        }
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
        /* Votre CSS ici - inchangé */
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
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
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
        }

        .omc {
            display: flex;
            flex-direction: row;
        }
    </style>
</head>

<body>
    <main id="dynamic-content">
        <div class="container">
            <!-- Titre Principal -->
            <h2 class="main-title">

            </h2>

            <!-- Carte principale -->
            <div class="card">
                <!-- Message de bienvenue -->
                <div class="welcome-message">
                    <h1>Salut <?php echo htmlspecialchars($user['prenom_user'] . ' ' . $user['nom_user']); ?> !</h1>
                    <p>Bienvenue dans le système. Comme c'est votre première connexion, vous devez changer votre mot de passe pour des raisons de sécurité.</p>
                </div>
                <div>

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

                    <form id="passwordForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="omc">

                            <div class="file-upload-container">
                                <label for="imageUpload" class="file-upload-label">
                                    <div class="upload-icon">
                                        <!-- Simple CSS upload icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                    </div>
                                    <div class="upload-text">Cliquez ici pour sélectionner un fichier</div>
                                </label>
                                <input type="file" id="imageUpload" name="image" accept="image/*" class="file-input">
                            </div>

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
                                <i class='bx bx-check'></i> Changer mon mot de passe
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

    <!-- Variables PHP pour JavaScript -->
    <script>
        // Passage des variables PHP au JavaScript
        var passwordChanged = <?php echo $password_changed ? 'true' : 'false'; ?>;
    </script>

    <!-- Inclusion du fichier JavaScript externe -->
    <script src="js/changepassword.js"></script>
</body>

</html>
<?php
require_once('../Template/footer.php');
?>