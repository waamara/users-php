<?php
/**
* Page principale de gestion des directions
* Affiche la liste des directions et permet d'ajouter/modifier
*/

// Démarrer la session
session_start();

// Configuration de la base de données
require_once '../../db_connection/config.php';
require_once("../Template/header.php");

/**
* Fonction pour récupérer toutes les directions
* @return array Liste des directions
*/
function getAllDirections() {
   try {
       $pdo = connectDB();
       $stmt = $pdo->prepare("SELECT * FROM direction ORDER BY id DESC");
       $stmt->execute();
       $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
       return $results;
   } catch (PDOException $e) {
       error_log("Erreur lors de la récupération des directions: " . $e->getMessage());
       return [];
   }
}

// Récupérer toutes les directions
$directions = getAllDirections();

// Récupérer les messages de succès/erreur
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$show_success_alert = isset($_SESSION['show_success_alert']) ? $_SESSION['show_success_alert'] : false;

// Nettoyer les messages de session
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
unset($_SESSION['show_success_alert']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Gestion des Directions</title>
   <!-- Boxicons CSS -->
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
   <!-- SweetAlert2 CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
   <!-- CSS intégré -->
   <style>
       @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        :root {
          /* Main color palette - Bleu plus clair */
          --primary: #4a90e2;
          --primary-light: #6ba5e9;
          --primary-dark: #3a7fd9;
          --secondary: #6c757d;
          --success: #28a745;
          --danger: #dc3545;
          --warning: #ffc107;
          --info: #17a2b8;
          
          /* Neutral colors */
          --light: #ffffff;
          --dark: #212529;
          --grey-50: #f8f9fa;
          --grey-100: #e9ecef;
          --grey-200: #dee2e6;
          --grey-300: #ced4da;
          --grey-400: #adb5bd;
          --grey-500: #6c757d;
          --grey-600: #495057;
          --grey-700: #343a40;
          --grey-800: #212529;
          
          /* Shadows */
          --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
          --shadow-md: 0 3px 5px rgba(0, 0, 0, 0.08);
          --shadow-lg: 0 8px 12px -3px rgba(0, 0, 0, 0.08), 0 3px 5px -2px rgba(0, 0, 0, 0.04);
          
          /* Transitions */
          --transition-fast: 0.2s ease;
          --transition-normal: 0.3s ease;
          --transition-slow: 0.5s ease;
          
          /* Border radius */
          --radius-sm: 3px;
          --radius-md: 6px;
          --radius-lg: 10px;
          --radius-xl: 14px;
          --radius-full: 9999px;
        }

        /* Base styles */
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: 'Poppins', sans-serif;
          background-color: var(--grey-50);
          color: var(--grey-700);
          line-height: 1.4;
          font-size: 13px;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
        }

        /* Container - Taille réduite */
        .main-container {
          max-width: 1000px;
          margin: 0 auto;
          padding: 1rem;
        }

        /* Page title - Taille réduite */
        .title {
          display: flex;
          align-items: center;
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--primary);
          margin-bottom: 1rem;
          padding-bottom: 0.75rem;
          border-bottom: 2px solid var(--primary-light);
        }

        .title i {
          margin-right: 0.5rem;
          font-size: 1.625rem;
        }

        /* Page content - Taille réduite */
        .page {
          background-color: var(--light);
          border-radius: var(--radius-md);
          box-shadow: var(--shadow-md);
          overflow: hidden;
          transition: transform var(--transition-normal), box-shadow var(--transition-normal);
        }

        .form-container {
          padding: 1.25rem;
        }

        .form-title {
          display: flex;
          align-items: center;
          font-size: 1.125rem;
          font-weight: 600;
          margin-bottom: 1.25rem;
          color: var(--grey-700);
        }

        .form-title i {
          margin-right: 0.5rem;
          font-size: 1.25rem;
          color: var(--primary);
        }

        /* Alerts - Taille réduite */
        .alert {
          padding: 0.625rem 0.875rem;
          border-radius: var(--radius-md);
          margin-bottom: 1rem;
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.8125rem;
          box-shadow: var(--shadow-sm);
          animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
          from {
            transform: translateX(-10px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }

        .alert i {
          font-size: 1rem;
        }

        .alert-danger {
          background-color: #f8d7da;
          color: #721c24;
          border-left: 3px solid var(--danger);
        }

        .alert-success {
          background-color: #d4edda;
          color: #155724;
          border-left: 3px solid var(--success);
        }

        /* Action buttons - Taille réduite */
        .action-buttons {
          display: flex;
          justify-content: flex-end;
          margin-bottom: 1rem;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 0.375rem;
          padding: 0.5rem 0.875rem;
          border-radius: var(--radius-md);
          font-weight: 500;
          cursor: pointer;
          transition: all var(--transition-fast);
          border: none;
          text-decoration: none;
          font-size: 0.8125rem;
          outline: none;
          position: relative;
          overflow: hidden;
        }

        .btn i {
          font-size: 0.9375rem;
        }

        .btn-primary {
          background-color: var(--primary);
          color: var(--light);
          box-shadow: 0 2px 4px rgba(74, 144, 226, 0.2);
        }

        .btn-primary:hover {
          background-color: var(--primary-dark);
          transform: translateY(-2px);
          box-shadow: 0 3px 8px rgba(74, 144, 226, 0.3);
        }

        .btn-primary:active {
          transform: translateY(0);
          box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .btn-cancel {
          background-color: var(--grey-100);
          color: var(--grey-600);
        }

        .btn-cancel:hover {
          background-color: var(--grey-200);
          transform: translateY(-2px);
          box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-edit {
          padding: 0.375rem 0.625rem;
          border-radius: var(--radius-md);
          background-color: var(--primary);
          color: var(--light);
          border: none;
          cursor: pointer;
          transition: all var(--transition-fast);
          display: inline-flex;
          align-items: center;
          gap: 0.25rem;
          font-size: 0.75rem;
          font-weight: 500;
          outline: none;
          box-shadow: 0 2px 4px rgba(74, 144, 226, 0.2);
        }

        .btn-edit:hover {
          background-color: var(--primary-dark);
          transform: translateY(-2px);
          box-shadow: 0 3px 8px rgba(74, 144, 226, 0.3);
        }

        /* Ripple effect */
        .ripple {
          position: absolute;
          border-radius: 50%;
          background-color: rgba(255, 255, 255, 0.4);
          transform: scale(0);
          animation: ripple 0.6s linear;
          pointer-events: none;
        }

        @keyframes ripple {
          to {
            transform: scale(4);
            opacity: 0;
          }
        }

        /* Search container - Taille réduite */
        .search-container {
          margin-bottom: 1rem;
        }

        .search-container .form-group {
          max-width: 350px;
        }

        /* Form elements - Taille réduite */
        .form-group {
          margin-bottom: 1rem;
          position: relative;
        }

        .form-group label {
          display: block;
          margin-bottom: 0.375rem;
          font-weight: 500;
          color: var(--grey-600);
          font-size: 0.8125rem;
          transition: color var(--transition-fast);
        }

        .required {
          color: var(--danger);
          margin-left: 0.125rem;
        }

        .input-with-icon {
          position: relative;
          transition: transform var(--transition-fast);
        }

        .input-with-icon i {
          position: absolute;
          left: 0.625rem;
          top: 50%;
          transform: translateY(-50%);
          color: var(--grey-500);
          font-size: 0.875rem;
          transition: color var(--transition-fast);
        }

        .form-control {
          width: 100%;
          padding: 0.5rem 0.5rem 0.5rem 2rem;
          border: 1px solid var(--grey-200);
          border-radius: var(--radius-md);
          font-size: 0.8125rem;
          transition: all var(--transition-fast);
          background-color: var(--light);
          font-family: 'Poppins', sans-serif;
        }

        .form-control:hover {
          border-color: var(--grey-400);
        }

        .form-control:focus {
          outline: none;
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
        }

        .error-input {
          border-color: var(--danger) !important;
          background-color: rgba(220, 53, 69, 0.03);
        }

        .error {
          display: none;
          color: var(--danger);
          font-size: 0.6875rem;
          margin-top: 0.25rem;
          padding-left: 0.375rem;
          border-left: 2px solid var(--danger);
          animation: fadeIn 0.3s ease;
        }

        .error.show {
          display: block;
        }

        @keyframes fadeIn {
          from {
            opacity: 0;
          }
          to {
            opacity: 1;
          }
        }

        .form-actions {
          display: flex;
          justify-content: flex-end;
          gap: 0.5rem;
          margin-top: 1rem;
          padding-top: 0.75rem;
          border-top: 1px solid var(--grey-100);
        }

        /* Input focus effects */
        .input-with-icon.focused {
          transform: translateY(-1px);
        }

        .form-group.focused .form-control {
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
        }

        .form-group.focused i {
          color: var(--primary);
        }

        .form-group.focused label {
          color: var(--primary);
        }

        /* Direction List Styles (Numbered List) - Taille réduite */
        .directions-list {
          list-style-type: none;
          counter-reset: direction-counter;
          margin: 0;
          padding: 0;
        }

        .direction-item {
          display: flex;
          align-items: center;
          padding: 0.75rem;
          border-bottom: 1px solid var(--grey-200);
          transition: background-color var(--transition-fast);
          position: relative;
          counter-increment: direction-counter;
          cursor: pointer;
        }

        .direction-item:last-child {
          border-bottom: none;
        }

        .direction-item:hover {
          background-color: rgba(74, 144, 226, 0.05);
        }

        .direction-item::before {
          content: counter(direction-counter);
          display: flex;
          align-items: center;
          justify-content: center;
          width: 1.75rem;
          height: 1.75rem;
          background-color: var(--primary);
          color: white;
          border-radius: 50%;
          font-weight: 600;
          margin-right: 0.75rem;
          flex-shrink: 0;
          font-size: 0.75rem;
        }

        .direction-content {
          flex: 1;
          display: flex;
          flex-direction: column;
          gap: 0.125rem;
        }

        .direction-code {
          font-weight: 600;
          color: var(--grey-700);
          font-size: 0.875rem;
        }

        .direction-libelle {
          color: var(--grey-600);
          font-size: 0.8125rem;
        }

        .direction-actions {
          margin-left: auto;
        }

        .no-data-message {
          text-align: center;
          padding: 1.5rem;
          color: var(--grey-500);
          font-style: italic;
          font-size: 0.875rem;
        }

        /* No results message - Taille réduite */
        .no-results {
          display: none;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 1.5rem;
          background-color: var(--light);
          border-radius: var(--radius-md);
          box-shadow: var(--shadow-sm);
          margin-top: 1rem;
          border: 1px solid var(--grey-100);
        }

        .no-results i {
          font-size: 1.75rem;
          color: var(--primary);
          margin-bottom: 0.5rem;
          opacity: 0.7;
        }

        .no-results p {
          color: var(--grey-500);
          font-style: italic;
          font-size: 0.8125rem;
        }

        /* Modal styles - Taille réduite */
        .modal {
          display: none;
          position: fixed;
          z-index: 1000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          overflow: auto;
          background-color: rgba(0, 0, 0, 0.5);
          opacity: 0;
          transition: opacity var(--transition-normal);
          backdrop-filter: blur(3px);
        }

        .modal.show {
          opacity: 1;
        }

        .modal-content {
          background-color: var(--light);
          margin: 8% auto;
          padding: 1.25rem;
          border-radius: var(--radius-md);
          width: 35%;
          box-shadow: var(--shadow-lg);
          position: relative;
          transform: translateY(-15px);
          transition: transform var(--transition-normal), box-shadow var(--transition-normal);
        }

        .modal.show .modal-content {
          transform: translateY(0);
        }

        .close {
          position: absolute;
          right: 1rem;
          top: 0.75rem;
          color: var(--grey-500);
          font-size: 1.25rem;
          font-weight: bold;
          cursor: pointer;
          outline: none;
          transition: color var(--transition-fast), transform var(--transition-fast);
          height: 1.75rem;
          width: 1.75rem;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 50%;
        }

        .close:hover {
          color: var(--grey-700);
          background-color: var(--grey-50);
          transform: rotate(90deg);
        }

        /* Animations */
        .slideUp {
          animation: slideUp 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        @keyframes slideUp {
          from {
            transform: translateY(20px);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }

        /* Responsive styles */
        @media (max-width: 992px) {
          .modal-content {
            width: 50%;
          }
        }

        @media (max-width: 768px) {
          .modal-content {
            width: 90%;
            padding: 1rem;
            margin: 10% auto;
          }

          .form-actions {
            flex-direction: column-reverse;
          }

          .form-actions button {
            width: 100%;
          }

          .action-buttons {
            flex-direction: column;
          }

          .btn-primary,
          .btn-secondary {
            width: 100%;
            margin-bottom: 0.5rem;
          }
          
          .direction-item {
            flex-direction: column;
            align-items: flex-start;
            padding: 0.75rem 0.625rem;
          }
          
          .direction-item::before {
            margin-bottom: 0.375rem;
          }
          
          .direction-actions {
            margin-left: 0;
            margin-top: 0.625rem;
            width: 100%;
          }
          
          .direction-actions .btn-edit {
            width: 100%;
            justify-content: center;
          }
        }

        /* SweetAlert2 customization */
        .swal2-popup {
          border-radius: var(--radius-md);
          padding: 1.5em;
          font-size: 0.8125rem !important;
          box-shadow: var(--shadow-lg) !important;
        }

        .swal2-title {
          color: var(--grey-700);
          font-size: 1.125rem !important;
          font-weight: 600 !important;
        }

        .swal2-html-container {
          font-size: 0.875rem !important;
          color: var(--grey-600) !important;
        }

        .swal2-confirm {
          background-color: var(--primary) !important;
          font-size: 0.8125rem !important;
          padding: 0.5rem 1rem !important;
          border-radius: var(--radius-md) !important;
          box-shadow: 0 2px 4px rgba(74, 144, 226, 0.2) !important;
        }

        .swal2-confirm:hover {
          background-color: var(--primary-dark) !important;
          transform: translateY(-2px) !important;
          box-shadow: 0 3px 8px rgba(74, 144, 226, 0.3) !important;
        }

        .swal2-cancel {
          background-color: var(--secondary) !important;
          font-size: 0.8125rem !important;
          padding: 0.5rem 1rem !important;
          border-radius: var(--radius-md) !important;
        }

        /* Accessibility improvements */
        .btn:focus-visible,
        .btn-edit:focus-visible,
        .form-control:focus-visible {
          outline: 2px solid var(--primary);
          outline-offset: 2px;
        }

        /* Fix for page overflow */
        body {
          padding-bottom: 2.5rem;
        }

        .page {
          overflow: visible !important;
        }

        .form-container {
          overflow: visible !important;
        }
   </style>
</head>
<body>
   <div class="main-container">
       <div class="title">
           <i class='bx bx-building'></i>
           Gestion des Directions
       </div>
       
       <div class="page">
           <div class="form-container">
               <div class="form-title">
                   <i class='bx bx-list-ul'></i>
                   Liste des Directions
               </div>
               
               <!-- Message d'alerte pour les erreurs/succès -->
               <div id="alertContainer">
                   <?php if (!empty($error_message)): ?>
                       <div class="alert alert-danger">
                           <i class='bx bx-error-circle'></i> <?php echo $error_message; ?>
                       </div>
                   <?php endif; ?>
                   
                   <?php if (!empty($success_message)): ?>
                       <div class="alert alert-success">
                           <i class='bx bx-check-circle'></i> <?php echo $success_message; ?>
                       </div>
                   <?php endif; ?>
               </div>
               
               <div class="action-buttons">
                   <button id="btnAjouter" class="btn btn-primary">
                       <i class='bx bx-plus-circle'></i> Ajouter une Direction
                   </button>
               </div>
               
               <!-- Barre de recherche -->
               <div class="search-container">
                   <div class="form-group">
                       <div class="input-with-icon">
                           <i class='bx bx-search'></i>
                           <input type="text" id="searchDirection" placeholder="Rechercher une direction..." class="form-control">
                       </div>
                   </div>
               </div>

               <div class="table-container slideUp">
                   <ul class="directions-list" id="directionsData">
                       <?php if (empty($directions)): ?>
                           <li class="no-data-message">Aucune direction trouvée</li>
                       <?php else: ?>
                           <?php foreach ($directions as $direction): ?>
                               <li class="direction-item" data-id="<?php echo htmlspecialchars($direction['id']); ?>" data-code="<?php echo htmlspecialchars($direction['code']); ?>" data-libelle="<?php echo htmlspecialchars($direction['libelle']); ?>">
                                   <div class="direction-content">
                                       <div class="direction-code"><?php echo htmlspecialchars($direction['code']); ?></div>
                                       <div class="direction-libelle"><?php echo htmlspecialchars($direction['libelle']); ?></div>
                                   </div>
                                   <div class="direction-actions">
                                       <button class="btn-edit" 
                                               data-id="<?php echo htmlspecialchars($direction['id']); ?>" 
                                               data-code="<?php echo htmlspecialchars($direction['code']); ?>" 
                                               data-libelle="<?php echo htmlspecialchars($direction['libelle']); ?>">
                                           <i class='bx bx-edit'></i> Modifier
                                       </button>
                                   </div>
                               </li>
                           <?php endforeach; ?>
                       <?php endif; ?>
                   </ul>
               </div>
               
               <!-- Message pour aucun résultat de recherche -->
               <div class="no-results" id="noResults">
                   <i class='bx bx-search'></i>
                   <p>Aucune direction ne correspond à votre recherche.</p>
               </div>
           </div>
       </div>
   </div>
   
   <!-- Modal pour ajouter une direction -->
   <div id="addModal" class="modal">
       <div class="modal-content">
           <span class="close">&times;</span>
           <div class="form-title">
               <i class='bx bx-plus-circle'></i>
               Ajouter une Direction
           </div>
           <form id="addDirectionForm">
               <div class="form-group">
                   <label for="addCode">Code<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-code-alt'></i>
                       <input type="text" id="addCode" name="code" class="form-control" maxlength="5" required>
                   </div>
                   <span class="error" id="addCodeError"></span>
               </div>
               <div class="form-group">
                   <label for="addLibelle">Libellé<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-tag'></i>
                       <input type="text" id="addLibelle" name="libelle" class="form-control" maxlength="50" required>
                   </div>
                   <span class="error" id="addLibelleError"></span>
               </div>
               <div class="form-actions">
                   <button type="button" class="btn btn-cancel" id="btnAnnulerAdd">
                       <i class='bx bx-x'></i> Annuler
                   </button>
                   <button type="submit" class="btn btn-primary">
                       <i class='bx bx-save'></i> Enregistrer
                   </button>
               </div>
           </form>
       </div>
   </div>
   
   <!-- Modal pour modifier une direction -->
   <div id="editModal" class="modal">
       <div class="modal-content">
           <span class="close">&times;</span>
           <div class="form-title">
               <i class='bx bx-edit'></i>
               Modifier une Direction
           </div>
           <form id="editDirectionForm">
               <input type="hidden" id="editId" name="id">
               <div class="form-group">
                   <label for="editCode">Code<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-code-alt'></i>
                       <input type="text" id="editCode" name="code" class="form-control" maxlength="5" required>
                   </div>
                   <span class="error" id="editCodeError"></span>
               </div>
               <div class="form-group">
                   <label for="editLibelle">Libellé<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-tag'></i>
                       <input type="text" id="editLibelle" name="libelle" class="form-control" maxlength="50" required>
                   </div>
                   <span class="error" id="editLibelleError"></span>
               </div>
               <div class="form-actions">
                   <button type="button" class="btn btn-cancel" id="btnAnnulerEdit">
                       <i class='bx bx-x'></i> Annuler
                   </button>
                   <button type="submit" class="btn btn-primary">
                       <i class='bx bx-save'></i> Enregistrer
                   </button>
               </div>
           </form>
       </div>
   </div>
   
   <!-- SweetAlert2 JS -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.js"></script>
   
   <!-- Script principal avec toutes les corrections -->
   <script>
       document.addEventListener('DOMContentLoaded', function() {
           // Éléments du DOM
           const addModal = document.getElementById("addModal");
           const editModal = document.getElementById("editModal");
           const btnAjouter = document.getElementById("btnAjouter");
           const addForm = document.getElementById("addDirectionForm");
           const editForm = document.getElementById("editDirectionForm");
           const btnAnnulerAdd = document.getElementById("btnAnnulerAdd");
           const btnAnnulerEdit = document.getElementById("btnAnnulerEdit");
           const closeBtns = document.querySelectorAll(".close");
           const searchInput = document.getElementById('searchDirection');
           const noResultsDiv = document.getElementById('noResults');
           
           // Configuration SweetAlert2 par défaut
           const Toast = Swal.mixin({
               toast: true,
               position: "top-end",
               showConfirmButton: false,
               timer: 3000,
               timerProgressBar: true,
               didOpen: (toast) => {
                   toast.addEventListener("mouseenter", Swal.stopTimer);
                   toast.addEventListener("mouseleave", Swal.resumeTimer);
               },
           });
           
           // Initialisation
           initializeApp();
           
           /**
            * Initialise l'application
            */
           function initializeApp() {
               // Ajouter des effets visuels aux champs de formulaire
               addFormFieldEffects();
               
               // Ajouter l'effet de ripple aux boutons
               addRippleEffect();
               
               // Vérifier les paramètres d'URL pour les messages
               checkUrlParams();
               
               // Initialiser la recherche
               initializeSearch();
               
               // Initialiser les modales
               initializeModals();
               
               // Initialiser les formulaires
               initializeForms();
               
               // Initialiser les boutons d'édition
               initializeEditButtons();
               
               // Afficher les messages de succès/erreur
               displaySessionMessages();
           }
           
           /**
            * Initialise la fonctionnalité de recherche
            */
           function initializeSearch() {
               if (!searchInput) return;
               
               // Réinitialiser la recherche au chargement
               searchInput.value = '';
               
               // Référence aux éléments de la liste
               const directionItems = document.querySelectorAll('.direction-item');
               const noDataMessage = document.querySelector('.no-data-message');
               
               // S'assurer que tous les éléments sont visibles au départ
               directionItems.forEach(item => {
                   item.style.display = 'flex';
               });
               
               // Cacher le message "aucun résultat" au départ
               if (noResultsDiv) {
                   noResultsDiv.style.display = 'none';
               }
               
               // Ajouter l'événement de recherche
               searchInput.addEventListener('input', function() {
                   const searchTerm = this.value.toLowerCase().trim();
                   let hasResults = false;
                   
                   directionItems.forEach(item => {
                       // Récupérer le texte des éléments de contenu
                       const code = item.querySelector('.direction-code').textContent.toLowerCase();
                       const libelle = item.querySelector('.direction-libelle').textContent.toLowerCase();
                       const itemText = code + ' ' + libelle;
                       
                       if (searchTerm === '' || itemText.includes(searchTerm)) {
                           item.style.display = 'flex';
                           hasResults = true;
                       } else {
                           item.style.display = 'none';
                       }
                   });
                   
                   // Afficher ou masquer le message "aucun résultat"
                   if (noResultsDiv) {
                       // Ne pas afficher le message si la liste est vide au départ
                       if (noDataMessage) {
                           noResultsDiv.style.display = 'none';
                       } else {
                           noResultsDiv.style.display = hasResults ? 'none' : 'flex';
                       }
                   }
               });
           }
           
           /**
            * Initialise les modales
            */
           function initializeModals() {
               // Événement pour ouvrir le modal d'ajout
               if (btnAjouter) {
                   btnAjouter.addEventListener("click", function(e) {
                       e.preventDefault();
                       if (addForm) addForm.reset();
                       clearErrors();
                       showModal(addModal);
                   });
               }
               
               // Fermer les modals avec le bouton X
               if (closeBtns) {
                   closeBtns.forEach((btn) => {
                       btn.addEventListener("click", function() {
                           const modal = this.closest(".modal");
                           hideModal(modal);
                       });
                   });
               }
               
               // Fermer les modals avec les boutons Annuler
               if (btnAnnulerAdd) {
                   btnAnnulerAdd.addEventListener("click", function() {
                       hideModal(addModal);
                   });
               }
               
               if (btnAnnulerEdit) {
                   btnAnnulerEdit.addEventListener("click", function() {
                       hideModal(editModal);
                   });
               }
               
               // Fermer les modals en cliquant en dehors
               window.addEventListener("click", function(event) {
                   if (event.target === addModal) {
                       hideModal(addModal);
                   }
                   if (event.target === editModal) {
                       hideModal(editModal);
                   }
               });
           }
           
           /**
            * Initialise les formulaires
            */
           function initializeForms() {
               // Soumission du formulaire d'ajout
               if (addForm) {
                   addForm.addEventListener("submit", function(event) {
                       event.preventDefault();
                       
                       // Validation du formulaire
                       const code = document.getElementById("addCode").value.trim();
                       const libelle = document.getElementById("addLibelle").value.trim();
                       
                       if (!validateForm(code, libelle, "add")) {
                           return;
                       }
                       
                       // Afficher confirmation avant soumission
                       confirmAndSubmit("add", code, 0);
                   });
               }
               
               // Soumission du formulaire de modification
               if (editForm) {
                   editForm.addEventListener("submit", function(event) {
                       event.preventDefault();
                       
                       // Validation du formulaire
                       const id = document.getElementById("editId").value;
                       const code = document.getElementById("editCode").value.trim();
                       const libelle = document.getElementById("editLibelle").value.trim();
                       
                       if (!validateForm(code, libelle, "edit")) {
                           return;
                       }
                       
                       // Afficher confirmation avant soumission
                       confirmAndSubmit("edit", code, id);
                   });
               }
           }
           
           /**
            * Confirme et soumet le formulaire
            * @param {string} formType - Le type de formulaire ('add' ou 'edit')
            * @param {string} code - Le code à vérifier
            * @param {number} id - L'ID pour l'édition
            */
           function confirmAndSubmit(formType, code, id) {
               const isAdd = formType === "add";
               const form = isAdd ? addForm : editForm;
               const actionText = isAdd ? "ajouter" : "modifier";
               
               Swal.fire({
                   title: "Confirmation",
                   text: `Voulez-vous ${actionText} cette direction ?`,
                   icon: "question",
                   showCancelButton: true,
                   confirmButtonColor: "#4a90e2",
                   cancelButtonColor: "#6c757d",
                   confirmButtonText: `Oui, ${actionText}`,
                   cancelButtonText: "Annuler",
               }).then((result) => {
                   if (result.isConfirmed) {
                       // Afficher animation de chargement
                       Swal.fire({
                           title: "Vérification...",
                           text: "Vérification du code en cours",
                           allowOutsideClick: false,
                           showConfirmButton: false,
                           didOpen: () => {
                               Swal.showLoading();
                           },
                       });
                       
                       // Vérifier si le code existe déjà
                       checkCodeExists(code, id)
                           .then((exists) => {
                               if (exists) {
                                   Swal.close();
                                   showError(`${formType}CodeError`, "Ce code existe déjà");
                                   return;
                               }
                               
                               // Soumettre le formulaire
                               Swal.close();
                               form.action = isAdd 
                                   ? "../../Backend/Direction/requetes_ajax/add_dir.php"
                                   : "../../Backend/Direction/requetes_ajax/edit_dir.php";
                               form.method = "post";
                               form.submit();
                           })
                           .catch((error) => {
                               Swal.close();
                               showSweetAlert("error", "Erreur", error.message || "Une erreur est survenue");
                           });
                   }
               });
           }
           
           /**
            * Initialise les boutons d'édition
            */
           function initializeEditButtons() {
               // Gestion des boutons d'édition
               const editButtons = document.querySelectorAll(".btn-edit");
               
               editButtons.forEach((button) => {
                   button.addEventListener("click", function(e) {
                       e.preventDefault();
                       e.stopPropagation(); // Empêcher la propagation au parent
                       
                       const id = this.getAttribute("data-id");
                       const code = this.getAttribute("data-code");
                       const libelle = this.getAttribute("data-libelle");
                       
                       if (document.getElementById("editId")) document.getElementById("editId").value = id;
                       if (document.getElementById("editCode")) document.getElementById("editCode").value = code;
                       if (document.getElementById("editLibelle")) document.getElementById("editLibelle").value = libelle;
                       
                       clearErrors();
                       showModal(editModal);
                   });
               });
               
               // Rendre les éléments de liste cliquables pour l'édition
               document.querySelectorAll('.direction-item').forEach(item => {
                   item.addEventListener('click', function() {
                       const id = this.getAttribute('data-id');
                       const code = this.getAttribute('data-code');
                       const libelle = this.getAttribute('data-libelle');
                       
                       if (document.getElementById("editId")) document.getElementById("editId").value = id;
                       if (document.getElementById("editCode")) document.getElementById("editCode").value = code;
                       if (document.getElementById("editLibelle")) document.getElementById("editLibelle").value = libelle;
                       
                       clearErrors();
                       showModal(editModal);
                   });
               });
           }
           
           /**
            * Affiche les messages de session
            */
           function displaySessionMessages() {
               <?php if ($show_success_alert && !empty($success_message)): ?>
               Swal.fire({
                   icon: 'success',
                   title: 'Succès!',
                   text: '<?php echo addslashes($success_message); ?>',
                   confirmButtonColor: '#4a90e2'
               });
               <?php endif; ?>
               
               <?php if (!empty($error_message)): ?>
               Swal.fire({
                   icon: 'error',
                   title: 'Erreur!',
                   text: '<?php echo addslashes($error_message); ?>',
                   confirmButtonColor: '#4a90e2'
               });
               <?php endif; ?>
           }
           
           /**
            * Fonction d'animation pour afficher une modal
            */
           function showModal(modal) {
               if (!modal) return;
               
               modal.style.display = "block";
               // Force le navigateur à reconnaître le changement pour l'animation
               setTimeout(() => {
                   modal.classList.add("show");
               }, 10);
           }
           
           /**
            * Fonction d'animation pour cacher une modal
            */
           function hideModal(modal) {
               if (!modal) return;
               
               modal.classList.remove("show");
               setTimeout(() => {
                   modal.style.display = "none";
               }, 300); // Durée de l'animation
           }
           
           /**
            * Ajoute un effet de ripple aux boutons
            */
           function addRippleEffect() {
               const buttons = document.querySelectorAll(".btn, .btn-edit");
               
               buttons.forEach((button) => {
                   button.addEventListener("click", (e) => {
                       const rect = button.getBoundingClientRect();
                       const x = e.clientX - rect.left;
                       const y = e.clientY - rect.top;
                       
                       const ripple = document.createElement("span");
                       ripple.classList.add("ripple");
                       ripple.style.left = `${x}px`;
                       ripple.style.top = `${y}px`;
                       
                       button.appendChild(ripple);
                       
                       setTimeout(() => {
                           ripple.remove();
                       }, 600);
                   });
               });
           }
           
           /**
            * Vérifie les paramètres d'URL pour afficher des messages
            */
           function checkUrlParams() {
               const urlParams = new URLSearchParams(window.location.search);
               const status = urlParams.get("status");
               const message = urlParams.get("message");
               
               if (status && message) {
                   const decodedMessage = decodeURIComponent(message);
                   
                   if (status === "success") {
                       showSweetAlert("success", "Succès", decodedMessage);
                   } else if (status === "error") {
                       showSweetAlert("error", "Erreur", decodedMessage);
                   }
                   
                   // Nettoyer l'URL après avoir affiché le message
                   window.history.replaceState({}, document.title, window.location.pathname);
               }
           }
           
           /**
            * Vérifie si un code existe déjà dans la base de données
            * @param {string} code - Le code à vérifier
            * @param {number} currentId - L'ID actuel (pour l'édition)
            * @returns {Promise<boolean>} - True si le code existe, false sinon
            */
           function checkCodeExists(code, currentId) {
               return fetch(
                   `../../Backend/Direction/requetes_ajax/dirajax.php?action=checkCode&code=${encodeURIComponent(code)}&id=${currentId}`,
               )
                   .then((response) => {
                       if (!response.ok) {
                           throw new Error("Erreur réseau");
                       }
                       return response.json();
                   })
                   .then((data) => {
                       if (!data.success) {
                           throw new Error(data.message || "Erreur lors de la vérification du code");
                       }
                       return data.exists;
                   })
                   .catch((error) => {
                       console.error("Erreur:", error);
                       throw error;
                   });
           }
           
           /**
            * Valide le formulaire
            * @param {string} code - Le code à valider
            * @param {string} libelle - Le libellé à valider
            * @param {string} formType - Le type de formulaire ('add' ou 'edit')
            * @returns {boolean} - True si le formulaire est valide, false sinon
            */
           function validateForm(code, libelle, formType) {
               let isValid = true;
               
               // Vider les messages d'erreur précédents
               clearErrors();
               
               // Valider le code
               if (code === "") {
                   showError(`${formType}CodeError`, "Le code est obligatoire");
                   isValid = false;
               } else if (code.length > 5) {
                   showError(`${formType}CodeError`, "Le code ne peut pas dépasser 5 caractères");
                   isValid = false;
               }
               
               // Valider le libellé
               if (libelle === "") {
                   showError(`${formType}LibelleError`, "Le libellé est obligatoire");
                   isValid = false;
               } else if (libelle.length > 50) {
                   showError(`${formType}LibelleError`, "Le libellé ne peut pas dépasser 50 caractères");
                   isValid = false;
               }
               
               return isValid;
           }
           
           /**
            * Affiche un message d'erreur avec animation
            * @param {string} elementId - L'ID de l'élément où afficher l'erreur
            * @param {string} message - Le message d'erreur à afficher
            */
           function showError(elementId, message) {
               const errorElement = document.getElementById(elementId);
               if (errorElement) {
                   errorElement.textContent = message;
                   errorElement.style.display = "block";
                   errorElement.classList.add("show");
               }
           }
           
           /**
            * Efface tous les messages d'erreur
            */
           function clearErrors() {
               const errorElements = document.querySelectorAll(".error");
               errorElements.forEach((element) => {
                   element.textContent = "";
                   element.style.display = "none";
                   element.classList.remove("show");
               });
           }
           
           /**
            * Affiche un message avec SweetAlert
            * @param {string} icon - L'icône à afficher ('success', 'error', 'warning', 'info')
            * @param {string} title - Le titre du message
            * @param {string} text - Le contenu du message
            */
           function showSweetAlert(icon, title, text) {
               Swal.fire({
                   icon: icon,
                   title: title,
                   text: text,
                   confirmButtonColor: "#4a90e2",
                   confirmButtonText: "OK",
                   allowOutsideClick: true,
                   allowEscapeKey: true,
                   allowEnterKey: true,
                   showConfirmButton: true,
                   timer: 5000, // Affiche le message pendant 5 secondes
                   timerProgressBar: true,
               });
           }
           
           /**
            * Ajouter des effets visuels aux champs de formulaire
            */
           function addFormFieldEffects() {
               // Ajouter des effets de survol et de focus
               document.querySelectorAll(".form-control").forEach((field) => {
                   // Effet au survol
                   field.addEventListener("mouseenter", function () {
                       if (!this.classList.contains("error-input") && document.activeElement !== this) {
                           this.style.borderColor = "var(--primary-light)";
                       }
                   });
                   
                   field.addEventListener("mouseleave", function () {
                       if (!this.classList.contains("error-input") && document.activeElement !== this) {
                           this.style.borderColor = "var(--grey-200)";
                       }
                   });
                   
                   // Effet au focus
                   field.addEventListener("focus", function () {
                       this.parentElement.classList.add("focused");
                       const label = this.closest(".form-group").querySelector("label");
                       if (label) {
                           label.style.color = "var(--primary)";
                       }
                   });
                   
                   field.addEventListener("blur", function () {
                       this.parentElement.classList.remove("focused");
                       const label = this.closest(".form-group").querySelector("label");
                       if (label) {
                           label.style.color = "var(--grey-600)";
                       }
                   });
               });
           }
       });
   </script>
</body>
</html>
<?php
require_once('../Template/footer.php');
?>