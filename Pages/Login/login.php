<?php
// Démarrer la session
session_start();
// Inclure la connexion à la base de données
require_once("../../db_connection/db_conn.php");

// Initialiser les variables
$error_message = "";
$username = "";

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et nettoyer les données
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validation basique
    if (empty($username) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Requête pour vérifier les identifiants
            $stmt = $pdo->prepare("SELECT id, nom_user, prenom_user, username, password, status, Role, structure FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Vérifier si le compte est actif
                if ($user['status'] != 1) {
                    // Compte inactif, définir un message pour SweetAlert
                    $_SESSION['sweet_alert'] = [
                        'type' => 'error',
                        'title' => 'Compte désactivé',
                        'text' => 'Impossible de se connecter. Veuillez contacter un administrateur.'
                    ];
                } else {
                    // Vérifier le mot de passe (en supposant qu'il est haché avec password_hash)
                    if (password_verify($password, $user['password'])) {
                        // Authentification réussie, créer la session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['nom_user'] = $user['nom_user'];
                        $_SESSION['prenom_user'] = $user['prenom_user'];

                        $roleId = $user['Role'];
                        $stmt = $pdo->prepare("SELECT nom_role FROM role WHERE id = :id");
                        $stmt->execute(['id' => $roleId]);
                        $role = $stmt->fetch(PDO::FETCH_ASSOC);
                        $_SESSION['user_role'] = $role['nom_role']; 

                        $_SESSION['user_structure'] = $user['structure'];
                        
                        // Vérifier si c'est la première connexion
                        // Pour cet exemple, nous utilisons une table first_login
                        $checkFirstLoginStmt = $pdo->prepare("SELECT * FROM first_login WHERE user_id = :user_id");
                        $checkFirstLoginStmt->bindParam(':user_id', $user['id']);
                        $checkFirstLoginStmt->execute();
                        
                        if ($checkFirstLoginStmt->rowCount() == 0) {
                            // C'est la première connexion, rediriger vers la page de changement de mot de passe
                            header("Location: changepassword.php");                           exit;
                        } else {
                            if($_SESSION['user_role'] == 'admin') {
                                include('../Role/admindash.php'); // Sidebar pour administrateurs
                                exit;
                            } 
                            elseif($_SESSION['user_role'] == 'agent') {
                                include('../Garantie/ListeGaranties.php'); // Sidebar pour agents
                                exit;
                            }
                            elseif($_SESSION['user_role'] == 'responsable') {
                                include('../Role/respodash.php'); // Sidebar pour responsables
                                exit;
                            }

                        }
                    } else {
                        $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
                    }
                }
            } else {
                $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error_message = "Erreur de connexion à la base de données: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système de Gestion Bancaire</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="login-container">
        <div class="login-wrapper">
            <div class="login-left">
                <div class="login-header">
                    <div class="logo">
                        <i class='bx bx-shield-quarter'></i>
                        <span>BankGuard</span>
                    </div>
                    <h1>Bienvenue</h1>
                    <p>Système de Gestion Bancaire</p>
                </div>
                <div class="login-content">
                    <h2>Gestion Sécurisée</h2>
                    <p>Accédez à votre espace de gestion en toute sécurité</p>
                </div>
                <div class="login-footer">
                    <p>&copy; <?php echo date('Y'); ?> BankGuard. Tous droits réservés.</p>
                </div>
            </div>
            <div class="login-right">
                <div class="login-form-container">
                    <div class="login-form-header">
                        <h2>Connexion</h2>
                        <p>Veuillez vous connecter pour accéder à votre compte</p>
                    </div>
                    
                    <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class='bx bx-error-circle'></i>
                        <span><?php echo $error_message; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="login-form">
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <div class="input-with-icon">
                                <i class='bx bx-user'></i>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    placeholder="Entrez votre nom d'utilisateur"
                                    autocomplete="username"
                                >
                            </div>
                            <span class="error-message" id="usernameError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="input-with-icon">
                                <i class='bx bx-lock-alt'></i>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Entrez votre mot de passe"
                                    autocomplete="current-password"
                                >
                                <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe">
                                    <i class='bx bx-hide'></i>
                                </button>
                            </div>
                            <span class="error-message" id="passwordError"></span>
                        </div>
                        
                        <div class="form-group remember-me">
                            <label class="checkbox-container">
                                <input type="checkbox" id="remember" name="remember">
                                <span class="checkmark"></span>
                                <span class="checkbox-label">Se souvenir de moi</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn-login">
                                <span>Se connecter</span>
                                <i class='bx bx-right-arrow-alt'></i>
                            </button>
                        </div>
                    </form>
                    
                
                </div>
            </div>
        </div>
    </div>

    <script src="js/login.js"></script>
    
    <?php if (isset($_SESSION['sweet_alert'])): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['sweet_alert']['type']; ?>',
            title: '<?php echo $_SESSION['sweet_alert']['title']; ?>',
            text: '<?php echo $_SESSION['sweet_alert']['text']; ?>',
            confirmButtonColor: '#1a56db'
        });
    </script>
    <?php unset($_SESSION['sweet_alert']); endif; ?>
</body>
</html>
