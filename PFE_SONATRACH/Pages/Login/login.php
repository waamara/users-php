<?php
session_start();
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
           $stmt = $pdo->prepare("SELECT id, nom, prenom, username, password FROM users WHERE username = :username");
           $stmt->bindParam(':username', $username);
           $stmt->execute();
           
           if ($stmt->rowCount() > 0) {
               $user = $stmt->fetch(PDO::FETCH_ASSOC);
               
               // Modifier la partie de vérification du mot de passe
               // Remplacer :
               if ($password === $user['password']) {
                   // Authentification réussie, créer la session
                   $_SESSION['user_id'] = $user['id'];
                   $_SESSION['username'] = $user['username'];
                   $_SESSION['nom'] = $user['nom'];
                   $_SESSION['prenom'] = $user['prenom'];
                   
                   // Rediriger vers la page des garanties
                   header("Location: ../Garantie/ListeGaranties.php");
                   exit;
               } else {
                   $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
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
   <title>Connexion - Système de Gestion des Garanties Bancaires</title>
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
   <link rel="stylesheet" href="css/login.css">

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
                   <p>Système de Gestion des Garanties Bancaires</p>
               </div>
               <div class="login-content">
                   <h2>Gestion Sécurisée</h2>
                   <p>Accédez à votre espace de gestion des garanties bancaires en toute sécurité</p>
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

   <script>
       document.addEventListener("DOMContentLoaded", () => {
           // Éléments du formulaire
           const loginForm = document.getElementById("loginForm");
           const usernameInput = document.getElementById("username");
           const passwordInput = document.getElementById("password");
           const usernameError = document.getElementById("usernameError");
           const passwordError = document.getElementById("passwordError");
           const togglePasswordBtn = document.querySelector(".toggle-password");
           const loginBtn = document.querySelector(".btn-login");

           // Fonction pour valider le formulaire côté client
           function validateForm() {
               let isValid = true;

               // Réinitialiser les messages d'erreur
               usernameError.textContent = "";
               passwordError.textContent = "";

               // Valider le nom d'utilisateur
               if (!usernameInput.value.trim()) {
                   usernameError.textContent = "Le nom d'utilisateur est requis";
                   isValid = false;
                   usernameInput.focus();
               }

               // Valider le mot de passe
               if (!passwordInput.value.trim()) {
                   passwordError.textContent = "Le mot de passe est requis";
                   isValid = false;
                   if (usernameInput.value.trim()) {
                       passwordInput.focus();
                   }
               }

               return isValid;
           }

           // Gestionnaire d'événement pour la soumission du formulaire
           if (loginForm) {
               loginForm.addEventListener("submit", (e) => {
                   // Valider le formulaire avant la soumission
                   if (!validateForm()) {
                       e.preventDefault();
                       return false;
                   }

                   // Ajouter l'état de chargement au bouton
                   loginBtn.classList.add("loading");

                   // La soumission continue normalement si la validation réussit
                   return true;
               });
           }

           // Gestionnaire d'événement pour afficher/masquer le mot de passe
           if (togglePasswordBtn) {
               togglePasswordBtn.addEventListener("click", function() {
                   const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                   passwordInput.setAttribute("type", type);

                   // Changer l'icône
                   const icon = this.querySelector("i");
                   if (type === "password") {
                       icon.classList.remove("bx-show");
                       icon.classList.add("bx-hide");
                   } else {
                       icon.classList.remove("bx-hide");
                       icon.classList.add("bx-show");
                   }
               });
           }

           // Effacer les messages d'erreur lors de la saisie
           usernameInput.addEventListener("input", () => {
               usernameError.textContent = "";
           });

           passwordInput.addEventListener("input", () => {
               passwordError.textContent = "";
           });

           // Ajouter des effets visuels aux champs de formulaire
           const inputs = document.querySelectorAll("input");
           inputs.forEach((input) => {
               // Effet au focus
               input.addEventListener("focus", function() {
                   this.parentElement.style.transform = "scale(1.02)";
                   this.parentElement.style.transition = "transform 0.3s ease";
               });

               // Retour à la normale au blur
               input.addEventListener("blur", function() {
                   this.parentElement.style.transform = "scale(1)";
               });
           });
       });
   </script>
</body>
</html>
