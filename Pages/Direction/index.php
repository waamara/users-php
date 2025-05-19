<?php
// Démarrer la session
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../Login/login.php");
  exit;
}
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
          /* Main color palette - Bleu spécifié */
          --primary: #2563eb;
          --primary-light: #3b82f6;
          --primary-dark: #1d4ed8;
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
          font-size: 14px;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
          overflow-x: hidden;
        }

        /* Container - Largeur fixée à 1600px */
        .main-container {
          width: 100%;
          max-width: 1600px;
          margin: 0 auto;
          padding: 1.5rem 2rem;
        }

        /* Page title - Style amélioré avec un seul trait */
        .title {
          display: flex;
          align-items: center;
          font-size: 1.75rem;
          font-weight: 600;
          color: var(--primary);
          margin-bottom: 1.5rem;
          padding-bottom: 0.75rem;
          border-bottom: 2px solid var(--primary);
          position: relative;
        }

        .title i {
          margin-right: 0.75rem;
          font-size: 1.875rem;
        }

        /* Page content - Style amélioré */
        .page {
          background-color: var(--light);
          border-radius: var(--radius-lg);
          box-shadow: var(--shadow-lg);
          overflow: hidden;
          transition: transform var(--transition-normal), box-shadow var(--transition-normal);
          margin-bottom: 2rem;
        }

        .form-container {
          padding: 1.5rem;
        }

        .form-title {
          display: flex;
          align-items: center;
          font-size: 1.25rem;
          font-weight: 600;
          margin-bottom: 1.5rem;
          color: var(--grey-700);
        }

        .form-title i {
          margin-right: 0.75rem;
          font-size: 1.375rem;
          color: var(--primary);
        }

        /* Alerts - Style amélioré */
        .alert {
          padding: 0.875rem 1rem;
          border-radius: var(--radius-md);
          margin-bottom: 1.25rem;
          display: flex;
          align-items: center;
          gap: 0.75rem;
          font-size: 0.9375rem;
          box-shadow: var(--shadow-sm);
          animation: slideIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes slideIn {
          from {
            transform: translateY(-10px);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }

        .alert i {
          font-size: 1.125rem;
        }

        .alert-danger {
          background-color: #fff5f5;
          color: #e53e3e;
          border-left: 4px solid var(--danger);
        }

        .alert-success {
          background-color: #f0fff4;
          color: #38a169;
          border-left: 4px solid var(--success);
        }

        /* Action bar - Style amélioré et réorganisé */
        .action-bar {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1.25rem;
        }

        /* Search container - Style amélioré */
        .search-container {
          flex: 1;
          max-width: 350px;
          margin-bottom: 0;
        }

        .search-container .form-group {
          margin-bottom: 0;
        }

        /* Action buttons - Style amélioré */
        .action-buttons {
          display: flex;
          justify-content: flex-end;
          gap: 0.75rem;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 0.5rem;
          padding: 0.625rem 1.25rem;
          border-radius: var(--radius-md);
          font-weight: 500;
          cursor: pointer;
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          border: none;
          text-decoration: none;
          font-size: 0.9375rem;
          outline: none;
          position: relative;
          overflow: hidden;
        }

        .btn i {
          font-size: 1.125rem;
        }

        .btn-primary {
          background-color: var(--primary);
          color: var(--light);
          box-shadow: 0 4px 6px rgba(37, 99, 235, 0.25);
        }

        .btn-primary:hover {
          background-color: var(--primary-dark);
          transform: translateY(-3px);
          box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
          transform: translateY(-1px);
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-cancel {
          background-color: var(--grey-100);
          color: var(--grey-600);
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .btn-cancel:hover {
          background-color: var(--grey-200);
          transform: translateY(-3px);
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-edit {
          padding: 0.5rem 0.875rem;
          border-radius: var(--radius-md);
          background-color: var(--primary);
          color: var(--light);
          border: none;
          cursor: pointer;
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          display: inline-flex;
          align-items: center;
          gap: 0.375rem;
          font-size: 0.875rem;
          font-weight: 500;
          outline: none;
          box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .btn-edit:hover {
          background-color: var(--primary-dark);
          transform: translateY(-2px);
          box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        /* Ripple effect - Amélioré */
        .ripple {
          position: absolute;
          border-radius: 50%;
          background-color: rgba(255, 255, 255, 0.5);
          transform: scale(0);
          animation: ripple 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          pointer-events: none;
        }

        @keyframes ripple {
          to {
            transform: scale(4);
            opacity: 0;
          }
        }

        /* Form elements - Style amélioré */
        .form-group {
          margin-bottom: 1.25rem;
          position: relative;
        }

        .form-group label {
          display: block;
          margin-bottom: 0.5rem;
          font-weight: 500;
          color: var(--grey-600);
          font-size: 0.9375rem;
          transition: color var(--transition-fast);
        }

        .required {
          color: var(--danger);
          margin-left: 0.25rem;
        }

        .input-with-icon {
          position: relative;
          transition: transform var(--transition-fast);
        }

        .input-with-icon i {
          position: absolute;
          left: 0.75rem;
          top: 50%;
          transform: translateY(-50%);
          color: var(--grey-500);
          font-size: 1rem;
          transition: color var(--transition-fast);
        }

        .form-control {
          width: 100%;
          padding: 0.75rem 0.75rem 0.75rem 2.5rem;
          border: 1px solid var(--grey-200);
          border-radius: var(--radius-md);
          font-size: 0.9375rem;
          transition: all var(--transition-fast);
          background-color: var(--light);
          font-family: 'Poppins', sans-serif;
          box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .form-control:hover {
          border-color: var(--grey-400);
        }

        .form-control:focus {
          outline: none;
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .error-input {
          border-color: var(--danger) !important;
          background-color: rgba(220, 53, 69, 0.03);
        }

        .error {
          display: none;
          color: var(--danger);
          font-size: 0.8125rem;
          margin-top: 0.375rem;
          padding-left: 0.5rem;
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
          gap: 0.75rem;
          margin-top: 1.5rem;
          padding-top: 1rem;
          border-top: 1px solid var(--grey-100);
        }

        /* Input focus effects - Amélioré */
        .input-with-icon.focused {
          transform: translateY(-2px);
        }

        .form-group.focused .form-control {
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-group.focused i {
          color: var(--primary);
        }

        .form-group.focused label {
          color: var(--primary);
        }

        /* Direction List Styles - Format liste avec pagination */
        .directions-list {
          list-style-type: none;
          margin: 0;
          padding: 0;
          border: 1px solid var(--grey-200);
          border-radius: var(--radius-md);
          overflow: hidden;
        }

        .direction-item {
          display: flex;
          align-items: center;
          padding: 1rem 1.25rem;
          border-bottom: 1px solid var(--grey-200);
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          position: relative;
          cursor: pointer;
          background-color: var(--light);
        }

        .direction-item:last-child {
          border-bottom: none;
        }

        .direction-item:hover {
          background-color: rgba(37, 99, 235, 0.05);
        }

        .direction-item-number {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 2rem;
          height: 2rem;
          background-color: var(--primary);
          color: white;
          border-radius: 50%;
          font-weight: 600;
          margin-right: 1rem;
          flex-shrink: 0;
          font-size: 0.875rem;
          box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .direction-content {
          flex: 1;
          display: flex;
          align-items: center;
          gap: 1.5rem;
        }

        .direction-code {
          font-weight: 600;
          color: var(--grey-700);
          font-size: 1rem;
          min-width: 100px;
          padding-right: 1rem;
          border-right: 2px solid var(--grey-200);
        }

        .direction-libelle {
          color: var(--grey-600);
          font-size: 0.9375rem;
          flex: 1;
        }

        .direction-actions {
          margin-left: auto;
        }

        .no-data-message {
          text-align: center;
          padding: 2rem;
          color: var(--grey-500);
          font-style: italic;
          font-size: 1rem;
          background-color: var(--grey-50);
          border-radius: var(--radius-md);
          border: 1px dashed var(--grey-300);
        }

        /* Table header */
        .table-header {
          display: flex;
          align-items: center;
          padding: 0.75rem 1.25rem;
          background-color: var(--grey-100);
          border-bottom: 1px solid var(--grey-200);
          font-weight: 600;
          color: var(--grey-700);
          font-size: 0.9375rem;
        }

        .header-number {
          width: 2rem;
          margin-right: 1rem;
          text-align: center;
        }

        .header-code {
          min-width: 100px;
          padding-right: 1rem;
          margin-right: 1.5rem;
        }

        .header-libelle {
          flex: 1;
        }

        .header-actions {
          width: 100px;
          text-align: center;
        }

        /* Pagination styles */
        .pagination-container {
          display: flex;
          justify-content: flex-end;
          align-items: center;
          margin-top: 1.5rem;
          padding-top: 1rem;
          border-top: 1px solid var(--grey-200);
        }

        .pagination {
          display: flex;
          list-style: none;
          padding: 0;
          margin: 0;
          gap: 0.25rem;
        }

        .pagination li {
          margin: 0;
        }

        .pagination a, .pagination span {
          display: flex;
          align-items: center;
          justify-content: center;
          min-width: 2.25rem;
          height: 2.25rem;
          padding: 0 0.5rem;
          border-radius: var(--radius-md);
          text-decoration: none;
          font-size: 0.875rem;
          color: var(--grey-600);
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          border: 1px solid var(--grey-200);
          background-color: var(--light);
        }

        .pagination a:hover {
          background-color: var(--grey-100);
          color: var(--grey-700);
          border-color: var(--grey-300);
        }

        .pagination .active a, .pagination .active span {
          background-color: var(--primary);
          color: var(--light);
          border-color: var(--primary);
          font-weight: 600;
          box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .pagination .disabled span {
          color: var(--grey-400);
          cursor: not-allowed;
          background-color: var(--grey-50);
        }

        .pagination-info {
          margin-right: 1rem;
          color: var(--grey-600);
          font-size: 0.875rem;
        }

        /* No results message - Style amélioré */
        .no-results {
          display: none;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 2rem;
          background-color: var(--light);
          border-radius: var(--radius-md);
          box-shadow: var(--shadow-sm);
          margin-top: 1.5rem;
          border: 1px dashed var(--grey-300);
          animation: fadeIn 0.5s ease;
        }

        .no-results i {
          font-size: 2.5rem;
          color: var(--primary);
          margin-bottom: 1rem;
          opacity: 0.7;
        }

        .no-results p {
          color: var(--grey-500);
          font-style: italic;
          font-size: 1rem;
        }

        /* Modal styles - Style amélioré */
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
          transition: opacity 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          backdrop-filter: blur(5px);
        }

        .modal.show {
          opacity: 1;
        }

        .modal-content {
          background-color: var(--light);
          margin: 5% auto;
          padding: 2rem;
          border-radius: var(--radius-lg);
          width: 30%;
          height: 55%;
          box-shadow: var(--shadow-lg);
          position: relative;
          transform: translateY(-30px) scale(0.95);
          transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .modal.show .modal-content {
          transform: translateY(0) scale(1);
        }

        .close {
          position: absolute;
          right: 1.25rem;
          top: 1rem;
          color: var(--grey-500);
          font-size: 1.5rem;
          font-weight: bold;
          cursor: pointer;
          outline: none;
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          height: 2rem;
          width: 2rem;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 50%;
          background-color: var(--grey-50);
        }

        .close:hover {
          color: var(--grey-700);
          background-color: var(--grey-100);
          transform: rotate(90deg);
        }

        /* Animations - Améliorées */
        .slideUp {
          animation: slideUp 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes slideUp {
          from {
            transform: translateY(30px);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }

        /* Responsive styles - Améliorées */
        @media (max-width: 1200px) {
          .main-container {
            width: 100%;
            padding: 1.25rem;
          }
        }

        @media (max-width: 992px) {
          .modal-content {
            width: 60%;
          }
        }

        @media (max-width: 768px) {
          .main-container {
            padding: 1rem;
          }
          
          .action-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
          }
          
          .search-container {
            max-width: 100%;
            order: 1;
          }
          
          .action-buttons {
            order: 2;
            justify-content: flex-end;
          }
          
          .modal-content {
            width: 90%;
            padding: 1.5rem;
            margin: 10% auto;
          }
          
          .direction-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
          }
          
          .direction-code {
            border-right: none;
            padding-right: 0;
            border-bottom: 1px solid var(--grey-200);
            padding-bottom: 0.5rem;
            width: 100%;
          }
          
          .table-header {
            display: none;
          }
          
          .direction-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
          }
          
          .direction-item-number {
            margin-bottom: 0.5rem;
          }
          
          .direction-actions {
            margin-left: 0;
            width: 100%;
          }
          
          .btn-edit {
            width: 100%;
            justify-content: center;
          }

          .form-actions {
            flex-direction: column-reverse;
            gap: 0.75rem;
          }

          .form-actions button {
            width: 100%;
          }

          .action-buttons {
            flex-direction: column;
            gap: 0.75rem;
          }

          .btn-primary,
          .btn-secondary {
            width: 100%;
          }
          
          .pagination {
            flex-wrap: wrap;
            justify-content: center;
          }
          
          .pagination-container {
            flex-direction: column;
            align-items: flex-end;
            gap: 0.75rem;
          }
          
          .pagination-info {
            margin-right: 0;
            margin-bottom: 0.5rem;
          }
        }

        @media (max-width: 480px) {
          .title {
            font-size: 1.5rem;
          }
          
          .title i {
            font-size: 1.625rem;
          }
          
          .form-title {
            font-size: 1.125rem;
          }
          
          .form-container {
            padding: 1.25rem 1rem;
          }
        }

        /* SweetAlert2 customization - Amélioré */
        .swal2-popup {
          border-radius: var(--radius-lg);
          padding: 2em;
          font-size: 0.9375rem !important;
          box-shadow: var(--shadow-lg) !important;
        }

        .swal2-title {
          color: var(--grey-700);
          font-size: 1.375rem !important;
          font-weight: 600 !important;
        }

        .swal2-html-container {
          font-size: 1rem !important;
          color: var(--grey-600) !important;
        }

        .swal2-confirm {
          background-color: var(--primary) !important;
          font-size: 0.9375rem !important;
          padding: 0.625rem 1.25rem !important;
          border-radius: var(--radius-md) !important;
          box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2) !important;
        }

        .swal2-confirm:hover {
          background-color: var(--primary-dark) !important;
          transform: translateY(-2px) !important;
          box-shadow: 0 6px 10px rgba(37, 99, 235, 0.3) !important;
        }

        .swal2-cancel {
          background-color: var(--secondary) !important;
          font-size: 0.9375rem !important;
          padding: 0.625rem 1.25rem !important;
          border-radius: var(--radius-md) !important;
        }

        /* Accessibility improvements */
        .btn:focus-visible,
        .btn-edit:focus-visible,
        .form-control:focus-visible {
          outline: 3px solid rgba(37, 99, 235, 0.5);
          outline-offset: 2px;
        }

        /* Animations supplémentaires */
        .pulse {
          animation: pulse 2s infinite;
        }

        @keyframes pulse {
          0% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4);
          }
          70% {
            box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
          }
          100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
          }
        }

        /* Amélioration du scroll */
        ::-webkit-scrollbar {
          width: 8px;
          height: 8px;
        }

        ::-webkit-scrollbar-track {
          background: var(--grey-100);
          border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
          background: var(--grey-300);
          border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
          background: var(--grey-400);
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
               
               <!-- Barre d'action pour rechercher et ajouter -->
               <div class="action-bar">
                   <!-- Recherche à gauche -->
                   <div class="search-container">
                       <div class="form-group">
                           <div class="input-with-icon">
                               <i class='bx bx-search'></i>
                               <input type="text" id="searchDirection" placeholder="Rechercher une direction..." class="form-control">
                           </div>
                       </div>
                   </div>
                   
                   <!-- Bouton d'ajout à droite -->
                   <div class="action-buttons">
                       <button id="btnAjouter" class="btn btn-primary">
                           <i class='bx bx-plus-circle'></i> Ajouter une Direction
                       </button>
                   </div>
               </div>

               <div class="table-container slideUp">
                   <!-- En-tête du tableau -->
                   <div class="table-header">
                       <div class="header-number">#</div>
                       <div class="header-code">Code</div>
                       <div class="header-libelle">Libellé</div>
                       <div class="header-actions">Actions</div>
                   </div>
                   
                   <ul class="directions-list" id="directionsData">
                       <?php if (empty($directions)): ?>
                           <li class="no-data-message">Aucune direction trouvée</li>
                       <?php else: ?>
                           <?php 
                           // Configuration de la pagination
                           $total_items = count($directions);
                           $items_per_page = 10;
                           $total_pages = ceil($total_items / $items_per_page);
                           $current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
                           $offset = ($current_page - 1) * $items_per_page;
                           
                           // Récupérer les éléments pour la page actuelle
                           $current_items = array_slice($directions, $offset, $items_per_page);
                           
                           $counter = $offset + 1;
                           foreach ($current_items as $direction): 
                           ?>
                               <li class="direction-item" data-id="<?php echo htmlspecialchars($direction['id']); ?>" data-code="<?php echo htmlspecialchars($direction['code']); ?>" data-libelle="<?php echo htmlspecialchars($direction['libelle']); ?>">
                                   <div class="direction-item-number"><?php echo $counter++; ?></div>
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
                   
                   <!-- Pagination -->
                   <?php if (!empty($directions) && $total_pages > 1): ?>
                   <div class="pagination-container">
                       <div class="pagination-info">
                           Affichage de <?php echo $offset + 1; ?> à <?php echo min($offset + $items_per_page, $total_items); ?> sur <?php echo $total_items; ?> directions
                       </div>
                       <ul class="pagination">
                           <!-- Bouton précédent -->
                           <?php if ($current_page > 1): ?>
                               <li>
                                   <a href="?page=<?php echo $current_page - 1; ?>">
                                       <i class='bx bx-chevron-left'></i>
                                   </a>
                               </li>
                           <?php else: ?>
                               <li class="disabled">
                                   <span><i class='bx bx-chevron-left'></i></span>
                               </li>
                           <?php endif; ?>
                           
                           <!-- Pages -->
                           <?php
                           $start_page = max(1, $current_page - 2);
                           $end_page = min($total_pages, $start_page + 4);
                           
                           if ($start_page > 1): ?>
                               <li><a href="?page=1">1</a></li>
                               <?php if ($start_page > 2): ?>
                                   <li class="disabled"><span>...</span></li>
                               <?php endif; ?>
                           <?php endif; ?>
                           
                           <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                               <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                   <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                               </li>
                           <?php endfor; ?>
                           
                           <?php if ($end_page < $total_pages): ?>
                               <?php if ($end_page < $total_pages - 1): ?>
                                   <li class="disabled"><span>...</span></li>
                               <?php endif; ?>
                               <li><a href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
                           <?php endif; ?>
                           
                           <!-- Bouton suivant -->
                           <?php if ($current_page < $total_pages): ?>
                               <li>
                                   <a href="?page=<?php echo $current_page + 1; ?>">
                                       <i class='bx bx-chevron-right'></i>
                                   </a>
                               </li>
                           <?php else: ?>
                               <li class="disabled">
                                   <span><i class='bx bx-chevron-right'></i></span>
                               </li>
                           <?php endif; ?>
                       </ul>
                   </div>
                   <?php endif; ?>
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
                       <input type="text" id="addCode" name="code" class="form-control" maxlength="50" required>
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
                       <input type="text" id="editCode" name="code" class="form-control" maxlength="50" required>
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
               
               // Ajouter des animations aux éléments de la liste
               animateListItems();
               
               // Initialiser la pagination côté client
               initializePagination();
           }
           
           /**
            * Initialise la pagination côté client
            */
           function initializePagination() {
               // Gérer les clics sur les liens de pagination
               document.querySelectorAll('.pagination a').forEach(link => {
                   link.addEventListener('click', function(e) {
                       // Si la recherche est active, ne pas permettre la pagination standard
                       if (searchInput.value.trim() !== '') {
                           e.preventDefault();
                           return;
                       }
                   });
               });
           }
           
           /**
            * Ajoute des animations aux éléments de la liste
            */
           function animateListItems() {
               const items = document.querySelectorAll('.direction-item');
               items.forEach((item, index) => {
                   item.style.opacity = '0';
                   item.style.transform = 'translateY(20px)';
                   
                   setTimeout(() => {
                       item.style.transition = 'all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                       item.style.opacity = '1';
                       item.style.transform = 'translateY(0)';
                   }, 100 + (index * 50));
               });
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
               const paginationContainer = document.querySelector('.pagination-container');
               const paginationInfo = document.querySelector('.pagination-info');
               
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
                   let visibleCount = 0;
                   
                   directionItems.forEach((item, index) => {
                       // Récupérer le texte des éléments de contenu
                       const code = item.querySelector('.direction-code').textContent.toLowerCase();
                       const libelle = item.querySelector('.direction-libelle').textContent.toLowerCase();
                       const itemText = code + ' ' + libelle;
                       
                       if (searchTerm === '' || itemText.includes(searchTerm)) {
                           item.style.display = 'flex';
                           hasResults = true;
                           visibleCount++;
                           
                           // Mettre à jour le numéro affiché
                           const numberElement = item.querySelector('.direction-item-number');
                           if (numberElement) {
                               numberElement.textContent = visibleCount;
                           }
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
                   
                   // Gérer l'affichage de la pagination
                   if (paginationContainer) {
                       paginationContainer.style.display = searchTerm === '' ? 'flex' : 'none';
                   }
                   
                   if (paginationInfo) {
                       paginationInfo.style.display = searchTerm === '' ? 'block' : 'none';
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
                   confirmButtonColor: "#2563eb",
                   cancelButtonColor: "#6c757d",
                   confirmButtonText: `Oui, ${actionText}`,
                   cancelButtonText: "Annuler",
                   customClass: {
                       confirmButton: 'btn-swal-confirm',
                       cancelButton: 'btn-swal-cancel'
                   },
                   buttonsStyling: true
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
                   item.addEventListener('click', function(e) {
                       // Ne pas déclencher si on a cliqué sur le bouton d'édition
                       if (e.target.closest('.btn-edit')) {
                           return;
                       }
                       
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
                   confirmButtonColor: '#2563eb',
                   timer: 3000,
                   timerProgressBar: true,
                   showClass: {
                       popup: 'animate__animated animate__fadeInDown'
                   },
                   hideClass: {
                       popup: 'animate__animated animate__fadeOutUp'
                   }
               });
               <?php endif; ?>
               
               <?php if (!empty($error_message)): ?>
               Swal.fire({
                   icon: 'error',
                   title: 'Erreur!',
                   text: '<?php echo addslashes($error_message); ?>',
                   confirmButtonColor: '#2563eb'
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
                       }, 800); // Durée de l'animation augmentée
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
               } 
               
               // Valider le libellé
               if (libelle === "") {
                   showError(`${formType}LibelleError`, "Le libellé est obligatoire");
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
                   
                   // Ajouter une animation de secousse
                   const inputField = errorElement.closest('.form-group').querySelector('.form-control');
                   if (inputField) {
                       inputField.classList.add('error-input');
                       inputField.style.animation = 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both';
                       setTimeout(() => {
                           inputField.style.animation = '';
                       }, 500);
                   }
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
               
               const inputFields = document.querySelectorAll('.form-control');
               inputFields.forEach((field) => {
                   field.classList.remove('error-input');
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
                   confirmButtonColor: "#2563eb",
                   confirmButtonText: "OK",
                   allowOutsideClick: true,
                   allowEscapeKey: true,
                   allowEnterKey: true,
                   showConfirmButton: true,
                   timer: 5000, // Affiche le message pendant 5 secondes
                   timerProgressBar: true,
                   showClass: {
                       popup: 'animate__animated animate__fadeInDown'
                   },
                   hideClass: {
                       popup: 'animate__animated animate__fadeOutUp'
                   }
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
           
           // Animation de secousse pour les erreurs
           document.head.insertAdjacentHTML('beforeend', `
               <style>
                   @keyframes shake {
                       0%, 100% { transform: translateX(0); }
                       10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                       20%, 40%, 60%, 80% { transform: translateX(5px); }
                   }
               </style>
           `);
       });
   </script>
</body>
</html>
<?php
require_once('../Template/footer.php');
?>
