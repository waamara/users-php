<?php
// Démarrer la session
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../Login/login.php");
    exit;
  }
require_once("../Template/header.php");

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
   <title>Gestion des Agences</title>
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

        /* Agence List Styles - Format liste avec pagination */
        .agences-list {
          list-style-type: none;
          margin: 0;
          padding: 0;
          border: 1px solid var(--grey-200);
          border-radius: var(--radius-md);
          overflow: hidden;
        }

        .agence-item {
          display: flex;
          align-items: center;
          padding: 1rem 1.25rem;
          border-bottom: 1px solid var(--grey-200);
          transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          position: relative;
          cursor: pointer;
          background-color: var(--light);
        }

        .agence-item:last-child {
          border-bottom: none;
        }

        .agence-item:hover {
          background-color: rgba(37, 99, 235, 0.05);
        }

        .agence-item-number {
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

        .agence-content {
          flex: 1;
          display: flex;
          align-items: center;
          gap: 1.5rem;
        }

        .agence-code {
          font-weight: 600;
          color: var(--grey-700);
          font-size: 1rem;
          min-width: 100px;
          padding-right: 1rem;
          border-right: 2px solid var(--grey-200);
        }

        .agence-label {
          color: var(--grey-600);
          font-size: 0.9375rem;
          min-width: 150px;
          padding-right: 1rem;
          border-right: 2px solid var(--grey-200);
        }

        .agence-adresse {
          color: var(--grey-600);
          font-size: 0.9375rem;
          flex: 1;
        }

        .agence-actions {
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

        .header-label {
          min-width: 150px;
          padding-right: 1rem;
          margin-right: 1.5rem;
        }

        .header-adresse {
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

        .close, .addClose-btn {
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

        .close:hover, .addClose-btn:hover {
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
          
          .agence-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
          }
          
          .agence-code, .agence-label {
            border-right: none;
            padding-right: 0;
            border-bottom: 1px solid var(--grey-200);
            padding-bottom: 0.5rem;
            width: 100%;
          }
          
          .table-header {
            display: none;
          }
          
          .agence-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
          }
          
          .agence-item-number {
            margin-bottom: 0.5rem;
          }
          
          .agence-actions {
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
          .btn-cancel {
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
           <i class='bx bx-building-house'></i>
           <span id="pageTitle">Gestion des Agences de la Banque</span>
       </div>
       
       <div class="page">
           <div class="form-container">
               <div class="form-title">
                   <i class='bx bx-list-ul'></i>
                   Liste des Agences
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
                               <input type="text" id="searchAgence" placeholder="Rechercher une agence..." class="form-control">
                           </div>
                       </div>
                   </div>
                   
                   <!-- Boutons d'action à droite -->
                   <div class="action-buttons">
                       <button id="addAgenceBtn" class="btn btn-primary">
                           <i class='bx bx-plus-circle'></i> Ajouter une Agence
                       </button>
                   </div>
               </div>

               <div class="table-container slideUp">
                   <!-- En-tête du tableau -->
                   <div class="table-header">
                       <div class="header-number">#</div>
                       <div class="header-code">Code</div>
                       <div class="header-label">Label</div>
                       <div class="header-adresse">Adresse</div>
                       <div class="header-actions">Actions</div>
                   </div>
                   
                   <ul class="agences-list" id="agencesData">
                       <!-- Les données seront chargées dynamiquement ici -->
                   </ul>
                   
                   <!-- Pagination -->
                   <div class="pagination-container" id="paginationContainer" style="display: none;">
                       <div class="pagination-info" id="paginationInfo"></div>
                       <ul class="pagination" id="pagination"></ul>
                   </div>
               </div>
               
               <!-- Message pour aucun résultat de recherche -->
               <div class="no-results" id="noResults">
                   <i class='bx bx-search'></i>
                   <p>Aucune agence ne correspond à votre recherche.</p>
               </div>
           </div>
       </div>
   </div>
   
   <!-- Modal pour ajouter une agence -->
   <div id="agenceModal" class="modal">
       <div class="modal-content">
           <span class="close addClose-btn">&times;</span>
           <div class="form-title">
               <i class='bx bx-plus-circle'></i>
               Ajouter une Agence
           </div>
           <form id="agenceForm">
               <div class="form-group">
                   <label for="code">Code<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-code-alt'></i>
                       <input type="text" id="code" name="code" class="form-control" maxlength="50" required>
                   </div>
                   <span class="error" id="codeError"></span>
               </div>
               <div class="form-group">
                   <label for="label">Label<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-tag'></i>
                       <input type="text" id="label" name="label" class="form-control" maxlength="50" required>
                   </div>
                   <span class="error" id="labelError"></span>
               </div>
               <div class="form-group">
                   <label for="adresse">Adresse<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-map'></i>
                       <input type="text" id="adresse" name="adresse" class="form-control" maxlength="100" required>
                   </div>
                   <span class="error" id="adresseError"></span>
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
   
   <!-- Modal pour modifier une agence -->
   <div id="editAgenceModal" class="modal">
       <div class="modal-content">
           <span class="close">&times;</span>
           <div class="form-title">
               <i class='bx bx-edit'></i>
               Modifier une Agence
           </div>
           <form id="editAgenceForm">
               <input type="hidden" id="editAgenceId" name="id">
               <div class="form-group">
                   <label for="editCode">Code<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-code-alt'></i>
                       <input type="text" id="editCode" name="code" class="form-control" maxlength="50" required>
                   </div>
                   <span class="error" id="editCodeError"></span>
               </div>
               <div class="form-group">
                   <label for="editLabel">Label<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-tag'></i>
                       <input type="text" id="editLabel" name="label" class="form-control" maxlength="50" required>
                   </div>
                   <span class="error" id="editLabelError"></span>
               </div>
               <div class="form-group">
                   <label for="editAdresse">Adresse<span class="required">*</span></label>
                   <div class="input-with-icon">
                       <i class='bx bx-map'></i>
                       <input type="text" id="editAdresse" name="adresse" class="form-control" maxlength="100" required>
                   </div>
                   <span class="error" id="editAdresseError"></span>
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
           const addModal = document.getElementById("agenceModal");
           const editModal = document.getElementById("editAgenceModal");
           const addAgenceBtn = document.getElementById("addAgenceBtn");
           const addForm = document.getElementById("agenceForm");
           const editForm = document.getElementById("editAgenceForm");
           const btnAnnulerAdd = document.getElementById("btnAnnulerAdd");
           const btnAnnulerEdit = document.getElementById("btnAnnulerEdit");
           const closeBtns = document.querySelectorAll(".close");
           const addCloseBtns = document.querySelectorAll(".addClose-btn");
           const searchInput = document.getElementById('searchAgence');
           const noResultsDiv = document.getElementById('noResults');
           const agencesData = document.getElementById('agencesData');
           const pageTitle = document.getElementById('pageTitle');
           
           // Récupère l'ID de la banque sélectionnée depuis sessionStorage
           const selectedBanqueId = sessionStorage.getItem('selectedBanqueId');
           const selectedBanqueName = sessionStorage.getItem('selectedBanqueName');

           // Vérifier si une banque est sélectionnée
           if (!selectedBanqueId) {
               Swal.fire({
                   icon: 'error',
                   title: 'Erreur !',
                   text: "Aucune banque sélectionnée.",
                   confirmButtonColor: '#2563eb'
               }).then(() => {
                   window.location.href = '../Banque/banque.php';
               });
               return;
           }

           // Mettre à jour le titre avec le nom de la banque
           if (selectedBanqueName) {
               pageTitle.textContent = `Les Agences de la Banque ${selectedBanqueName}`;
           }
           
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
               
               // Initialiser la recherche
               initializeSearch();
               
               // Initialiser les modales
               initializeModals();
               
               // Initialiser les formulaires
               initializeForms();
               
               // Récupérer les agences au chargement de la page
               fetchAgences();
           }
           
           /**
            * Fonction pour récupérer les agences associées à une banque
            */
           function fetchAgences(query = '') {
               // Afficher un indicateur de chargement
               agencesData.innerHTML = '<li class="no-data-message">Chargement des agences...</li>';
               
               fetch(`../../Backend/Agence/requetes_ajax/affichageAgence.php?query=${encodeURIComponent(query)}&banque_id=${selectedBanqueId}`, {
                   method: 'GET',
                   headers: { 'Content-Type': 'application/json' }
               })
               .then(response => {
                   if (!response.ok) {
                       throw new Error('Erreur réseau');
                   }
                   return response.json();
               })
               .then(data => {
                   // Efface le contenu actuel de la liste
                   agencesData.innerHTML = "";
                   
                   if (data.error) {
                       throw new Error(data.error);
                   }
                   
                   if (data.length === 0) {
                       agencesData.innerHTML = '<li class="no-data-message">Aucune agence trouvée</li>';
                       document.getElementById('paginationContainer').style.display = 'none';
                       return;
                   }
                   
                   // Configuration de la pagination
                   const itemsPerPage = 10;
                   const totalItems = data.length;
                   const totalPages = Math.ceil(totalItems / itemsPerPage);
                   let currentPage = 1;
                   
                   // Fonction pour afficher les agences de la page actuelle
                   function displayAgences(page) {
                       const startIndex = (page - 1) * itemsPerPage;
                       const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
                       const currentItems = data.slice(startIndex, endIndex);
                       
                       // Vider la liste
                       agencesData.innerHTML = "";
                       
                       // Remplir la liste avec les données des agences
                       currentItems.forEach((agence, index) => {
                           const item = document.createElement('li');
                           item.className = 'agence-item';
                           item.setAttribute('data-id', agence.id);
                           item.setAttribute('data-code', agence.code);
                           item.setAttribute('data-label', agence.label);
                           item.setAttribute('data-adresse', agence.adresse);
                           
                           item.innerHTML = `
                               <div class="agence-item-number">${startIndex + index + 1}</div>
                               <div class="agence-content">
                                   <div class="agence-code">${agence.code}</div>
                                   <div class="agence-label">${agence.label}</div>
                                   <div class="agence-adresse">${agence.adresse}</div>
                               </div>
                               <div class="agence-actions">
                                   <button class="btn-edit" 
                                       data-id="${agence.id}" 
                                       data-code="${agence.code}" 
                                       data-label="${agence.label}" 
                                       data-adresse="${agence.adresse}">
                                       <i class='bx bx-edit'></i> Modifier
                                   </button>
                               </div>
                           `;
                           agencesData.appendChild(item);
                       });
                       
                       // Mettre à jour l'info de pagination
                       document.getElementById('paginationInfo').textContent = 
                           `Affichage de ${startIndex + 1} à ${endIndex} sur ${totalItems} agences`;
                       
                       // Attacher les écouteurs aux boutons "Modifier"
                       attachEditListeners();
                       
                       // Ajouter des animations aux éléments de la liste
                       animateListItems();
                   }
                   
                   // Fonction pour générer la pagination
                   function generatePagination() {
                       const paginationElement = document.getElementById('pagination');
                       paginationElement.innerHTML = '';
                       
                       // Bouton précédent
                       const prevLi = document.createElement('li');
                       if (currentPage > 1) {
                           const prevLink = document.createElement('a');
                           prevLink.href = '#';
                           prevLink.innerHTML = '<i class="bx bx-chevron-left"></i>';
                           prevLink.addEventListener('click', (e) => {
                               e.preventDefault();
                               currentPage--;
                               displayAgences(currentPage);
                               generatePagination();
                           });
                           prevLi.appendChild(prevLink);
                       } else {
                           const prevSpan = document.createElement('span');
                           prevSpan.innerHTML = '<i class="bx bx-chevron-left"></i>';
                           prevLi.className = 'disabled';
                           prevLi.appendChild(prevSpan);
                       }
                       paginationElement.appendChild(prevLi);
                       
                       // Pages
                       const startPage = Math.max(1, currentPage - 2);
                       const endPage = Math.min(totalPages, startPage + 4);
                       
                       // Première page et ellipsis
                       if (startPage > 1) {
                           const firstLi = document.createElement('li');
                           const firstLink = document.createElement('a');
                           firstLink.href = '#';
                           firstLink.textContent = '1';
                           firstLink.addEventListener('click', (e) => {
                               e.preventDefault();
                               currentPage = 1;
                               displayAgences(currentPage);
                               generatePagination();
                           });
                           firstLi.appendChild(firstLink);
                           paginationElement.appendChild(firstLi);
                           
                           if (startPage > 2) {
                               const ellipsisLi = document.createElement('li');
                               ellipsisLi.className = 'disabled';
                               const ellipsisSpan = document.createElement('span');
                               ellipsisSpan.textContent = '...';
                               ellipsisLi.appendChild(ellipsisSpan);
                               paginationElement.appendChild(ellipsisLi);
                           }
                       }
                       
                       // Pages numérotées
                       for (let i = startPage; i <= endPage; i++) {
                           const pageLi = document.createElement('li');
                           if (i === currentPage) {
                               pageLi.className = 'active';
                           }
                           
                           const pageLink = document.createElement('a');
                           pageLink.href = '#';
                           pageLink.textContent = i;
                           pageLink.addEventListener('click', (e) => {
                               e.preventDefault();
                               currentPage = i;
                               displayAgences(currentPage);
                               generatePagination();
                           });
                           
                           pageLi.appendChild(pageLink);
                           paginationElement.appendChild(pageLi);
                       }
                       
                       // Dernière page et ellipsis
                       if (endPage < totalPages) {
                           if (endPage < totalPages - 1) {
                               const ellipsisLi = document.createElement('li');
                               ellipsisLi.className = 'disabled';
                               const ellipsisSpan = document.createElement('span');
                               ellipsisSpan.textContent = '...';
                               ellipsisLi.appendChild(ellipsisSpan);
                               paginationElement.appendChild(ellipsisLi);
                           }
                           
                           const lastLi = document.createElement('li');
                           const lastLink = document.createElement('a');
                           lastLink.href = '#';
                           lastLink.textContent = totalPages;
                           lastLink.addEventListener('click', (e) => {
                               e.preventDefault();
                               currentPage = totalPages;
                               displayAgences(currentPage);
                               generatePagination();
                           });
                           lastLi.appendChild(lastLink);
                           paginationElement.appendChild(lastLi);
                       }
                       
                       // Bouton suivant
                       const nextLi = document.createElement('li');
                       if (currentPage < totalPages) {
                           const nextLink = document.createElement('a');
                           nextLink.href = '#';
                           nextLink.innerHTML = '<i class="bx bx-chevron-right"></i>';
                           nextLink.addEventListener('click', (e) => {
                               e.preventDefault();
                               currentPage++;
                               displayAgences(currentPage);
                               generatePagination();
                           });
                           nextLi.appendChild(nextLink);
                       } else {
                           const nextSpan = document.createElement('span');
                           nextSpan.innerHTML = '<i class="bx bx-chevron-right"></i>';
                           nextLi.className = 'disabled';
                           nextLi.appendChild(nextSpan);
                       }
                       paginationElement.appendChild(nextLi);
                   }
                   
                   // Afficher la pagination si nécessaire
                   if (totalPages > 1) {
                       document.getElementById('paginationContainer').style.display = 'flex';
                       generatePagination();
                   } else {
                       document.getElementById('paginationContainer').style.display = 'none';
                   }
                   
                   // Afficher les agences de la première page
                   displayAgences(currentPage);
               })
               .catch(error => {
                   console.error("Erreur lors du chargement des agences :", error);
                   agencesData.innerHTML = '<li class="no-data-message" style="color: var(--danger);">Une erreur est survenue lors du chargement des agences.</li>';
                   
                   Swal.fire({
                       icon: 'error',
                       title: 'Erreur !',
                       text: "Une erreur est survenue lors du chargement des agences.",
                       confirmButtonColor: '#2563eb'
                   });
               });
           }
           
           /**
            * Initialise la fonctionnalité de recherche
            */
           function initializeSearch() {
               if (!searchInput) return;
               
               // Réinitialiser la recherche au chargement
               searchInput.value = '';
               
               // Cacher le message "aucun résultat" au départ
               if (noResultsDiv) {
                   noResultsDiv.style.display = 'none';
               }
               
               // Ajouter l'événement de recherche
               searchInput.addEventListener('input', function() {
                   const searchTerm = this.value.toLowerCase().trim();
                   
                   if (searchTerm.length > 0) {
                       fetchAgences(searchTerm);
                   } else {
                       fetchAgences();
                   }
               });
           }
           
           /**
            * Initialise les modales
            */
           function initializeModals() {
               // Événement pour ouvrir le modal d'ajout
               if (addAgenceBtn) {
                   addAgenceBtn.addEventListener("click", function(e) {
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
               
               if (addCloseBtns) {
                   addCloseBtns.forEach((btn) => {
                       btn.addEventListener("click", function() {
                           hideModal(addModal);
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
               // Vérifier l'unicité du code en temps réel
               const codeField = document.getElementById('code');
               const labelField = document.getElementById('label');
               
               if (codeField) {
                   codeField.addEventListener('input', () => {
                       checkUniqueness('code', codeField.value, document.getElementById('codeError'));
                   });
               }
               
               if (labelField) {
                   labelField.addEventListener('input', () => {
                       checkUniqueness('label', labelField.value, document.getElementById('labelError'));
                   });
               }
               
               // Soumission du formulaire d'ajout
               if (addForm) {
                   addForm.addEventListener("submit", async function(event) {
                       event.preventDefault();
                       
                       // Validation du formulaire
                       const code = document.getElementById('code').value.trim();
                       const label = document.getElementById('label').value.trim();
                       const adresse = document.getElementById('adresse').value.trim();
                       
                       clearErrors();
                       
                       // Validation des champs requis
                       let valid = true;
                       
                       if (code === '') {
                           showError(document.getElementById('codeError'), "Ce champ est obligatoire.");
                           valid = false;
                       }
                       if (label === '') {
                           showError(document.getElementById('labelError'), "Ce champ est obligatoire.");
                           valid = false;
                       }
                       if (adresse === '') {
                           showError(document.getElementById('adresseError'), "Ce champ est obligatoire.");
                           valid = false;
                       }
                       
                       // Vérification de l'unicité du code et du label
                       const isCodeUnique = await checkUniqueness('code', code, document.getElementById('codeError'));
                       const isLabelUnique = await checkUniqueness('label', label, document.getElementById('labelError'));
                       
                       if (!isCodeUnique || !isLabelUnique) {
                           valid = false; // Empêche la soumission si les champs ne sont pas uniques
                       }
                       
                       // Soumet le formulaire si toutes les validations sont passées
                       if (valid) {
                           // Afficher confirmation avant soumission
                           confirmAndSubmit("add", code, label, adresse);
                       }
                   });
               }
           }
           
           /**
            * Confirme et soumet le formulaire
            */
           function confirmAndSubmit(formType, code, label, adresse, id = null) {
               const isAdd = formType === "add";
               const actionText = isAdd ? "ajouter" : "modifier";
               
               Swal.fire({
                   title: "Confirmation",
                   text: `Voulez-vous ${actionText} cette agence ?`,
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
                           title: "Traitement en cours...",
                           text: "Veuillez patienter",
                           allowOutsideClick: false,
                           showConfirmButton: false,
                           didOpen: () => {
                               Swal.showLoading();
                           },
                       });
                       
                       // Préparer les données à envoyer
                       const data = {
                           code,
                           label,
                           adresse
                       };
                       
                       if (isAdd) {
                           data.banque_id = selectedBanqueId;
                       } else {
                           data.id = id;
                       }
                       
                       // URL de l'API
                       const url = isAdd 
                           ? '../../Backend/Agence/requetes_ajax/ajouterAgence.php'
                           : '../../Backend/Agence/requetes_ajax/modifierAgence.php';
                       
                       // Envoyer la requête
                       fetch(url, {
                           method: 'POST',
                           headers: { 'Content-Type': 'application/json' },
                           body: JSON.stringify(data)
                       })
                       .then(response => response.json())
                       .then(data => {
                           Swal.close();
                           
                           if (data.success) {
                               // Fermer le modal
                               hideModal(isAdd ? addModal : editModal);
                               
                               // Afficher le message de succès
                               Swal.fire({
                                   icon: 'success',
                                   title: 'Succès !',
                                   text: data.success,
                                   confirmButtonColor: '#2563eb'
                               }).then(() => {
                                   // Actualiser les données
                                   fetchAgences();
                                   
                                   // Réinitialiser le formulaire
                                   if (isAdd && addForm) {
                                       addForm.reset();
                                   }
                               });
                           } else {
                               Swal.fire({
                                   icon: 'error',
                                   title: 'Erreur !',
                                   text: data?.error || 'Une erreur inattendue est survenue.',
                                   confirmButtonColor: '#2563eb'
                               });
                           }
                       })
                       .catch(error => {
                           console.error("Erreur AJAX :", error);
                           Swal.fire({
                               icon: 'error',
                               title: 'Erreur !',
                               text: "Une erreur est survenue lors de l'envoi des données.",
                               confirmButtonColor: '#2563eb'
                           });
                       });
                   }
               });
           }
           
           /**
            * Attache des écouteurs aux boutons "Modifier"
            */
           function attachEditListeners() {
               document.querySelectorAll('.btn-edit').forEach(button => {
                   button.addEventListener('click', (e) => {
                       e.preventDefault();
                       e.stopPropagation(); // Empêcher la propagation au parent
                       
                       const agenceId = e.currentTarget.dataset.id;
                       const originalCode = e.currentTarget.dataset.code;
                       const originalLabel = e.currentTarget.dataset.label;
                       const originalAdresse = e.currentTarget.dataset.adresse;
                       
                       // Pré-remplissage des champs du modal de modification
                       const editCodeField = document.getElementById('editCode');
                       const editLabelField = document.getElementById('editLabel');
                       const editAdresseField = document.getElementById('editAdresse');
                       
                       // Remplit les champs avec les données actuelles
                       document.getElementById('editAgenceId').value = agenceId;
                       editCodeField.value = originalCode;
                       editLabelField.value = originalLabel;
                       editAdresseField.value = originalAdresse;
                       
                       // Effacer les erreurs
                       clearEditErrors();
                       
                       // Affiche le modal de modification
                       showModal(editModal);
                       
                       // Validation en temps réel pour les champs "code" et "label"
                       editCodeField.addEventListener('input', () => {
                           checkUniqueness('code', editCodeField.value, document.getElementById('editCodeError'), agenceId);
                       });
                       
                       editLabelField.addEventListener('input', () => {
                           checkUniqueness('label', editLabelField.value, document.getElementById('editLabelError'), agenceId);
                       });
                   });
               });
               
               // Rendre les éléments de liste cliquables pour l'édition
               document.querySelectorAll('.agence-item').forEach(item => {
                   item.addEventListener('click', function(e) {
                       // Ne pas déclencher si on a cliqué sur le bouton d'édition
                       if (e.target.closest('.btn-edit')) {
                           return;
                       }
                       
                       const agenceId = this.getAttribute('data-id');
                       const code = this.getAttribute('data-code');
                       const label = this.getAttribute('data-label');
                       const adresse = this.getAttribute('data-adresse');
                       
                       // Pré-remplissage des champs du modal de modification
                       const editCodeField = document.getElementById('editCode');
                       const editLabelField = document.getElementById('editLabel');
                       const editAdresseField = document.getElementById('editAdresse');
                       
                       // Remplit les champs avec les données actuelles
                       document.getElementById('editAgenceId').value = agenceId;
                       editCodeField.value = code;
                       editLabelField.value = label;
                       editAdresseField.value = adresse;
                       
                       // Effacer les erreurs
                       clearEditErrors();
                       
                       // Affiche le modal de modification
                       showModal(editModal);
                       
                       // Validation en temps réel pour les champs "code" et "label"
                       editCodeField.addEventListener('input', () => {
                           checkUniqueness('code', editCodeField.value, document.getElementById('editCodeError'), agenceId);
                       });
                       
                       editLabelField.addEventListener('input', () => {
                           checkUniqueness('label', editLabelField.value, document.getElementById('editLabelError'), agenceId);
                       });
                   });
               });
               
               // Gestion du formulaire d'édition
               if (editForm) {
                   editForm.addEventListener('submit', async function(e) {
                       e.preventDefault();
                       
                       const agenceId = document.getElementById('editAgenceId').value;
                       const newCode = document.getElementById('editCode').value.trim();
                       const newLabel = document.getElementById('editLabel').value.trim();
                       const newAdresse = document.getElementById('editAdresse').value.trim();
                       
                       clearEditErrors();
                       
                       // Validation des champs requis
                       let valid = true;
                       
                       if (newCode === '') {
                           showError(document.getElementById('editCodeError'), "Ce champ est obligatoire.");
                           valid = false;
                       }
                       if (newLabel === '') {
                           showError(document.getElementById('editLabelError'), "Ce champ est obligatoire.");
                           valid = false;
                       }
                       if (newAdresse === '') {
                           showError(document.getElementById('editAdresseError'), "Ce champ est obligatoire.");
                           valid = false;
                       }
                       
                       // Vérification de l'unicité (en excluant l'enregistrement actuel)
                       const isCodeUnique = await checkUniqueness('code', newCode, document.getElementById('editCodeError'), agenceId);
                       const isLabelUnique = await checkUniqueness('label', newLabel, document.getElementById('editLabelError'), agenceId);
                       
                       if (!isCodeUnique || !isLabelUnique) {
                           valid = false;
                       }
                       
                       // Soumet le formulaire si toutes les validations sont passées
                       if (valid) {
                           confirmAndSubmit("edit", newCode, newLabel, newAdresse, agenceId);
                       }
                   });
               }
           }
           
           /**
            * Fonction pour vérifier l'unicité d'un champ
            */
           function checkUniqueness(field, value, errorElement, currentId = null) {
               return new Promise((resolve) => {
                   if (!value.trim()) {
                       resolve(true); // Ne vérifie pas si le champ est vide
                       return;
                   }
                   
                   fetch('../../Backend/Agence/requetes_ajax/uniqueAgence.php', {
                       method: 'POST',
                       headers: { 'Content-Type': 'application/json' },
                       body: JSON.stringify({ field, value, currentId }) // Inclut l'ID actuel pour exclure l'enregistrement en cours de modification
                   })
                   .then(response => {
                       if (!response.ok) throw new Error('Network response was not ok');
                       return response.json();
                   })
                   .then(data => {
                       if (data.success) {
                           errorElement.textContent = "";
                           errorElement.style.display = 'none';
                           resolve(true); // Le champ est unique
                       } else {
                           showError(errorElement, data.error);
                           resolve(false); // Le champ n'est pas unique
                       }
                   })
                   .catch(error => {
                       console.error("Erreur lors de la vérification :", error);
                       showError(errorElement, "Une erreur est survenue lors de la vérification.");
                       resolve(false); // Supposons que ce n'est pas unique en cas d'erreur
                   });
               });
           }
           
           /**
            * Affiche un message d'erreur avec animation
            */
           function showError(errorElement, message) {
               if (errorElement) {
                   errorElement.textContent = message;
                   errorElement.style.display = 'block';
                   errorElement.classList.add('show');
                   
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
            * Efface les messages d'erreur du formulaire d'ajout
            */
           function clearErrors() {
               const errorElements = document.querySelectorAll('#agenceForm .error');
               errorElements.forEach((element) => {
                   element.textContent = "";
                   element.style.display = 'none';
                   element.classList.remove('show');
               });
               
               const inputFields = document.querySelectorAll('#agenceForm .form-control');
               inputFields.forEach((field) => {
                   field.classList.remove('error-input');
               });
           }
           
           /**
            * Efface les messages d'erreur du formulaire d'édition
            */
           function clearEditErrors() {
               const errorElements = document.querySelectorAll('#editAgenceForm .error');
               errorElements.forEach((element) => {
                   element.textContent = "";
                   element.style.display = 'none';
                   element.classList.remove('show');
               });
               
               const inputFields = document.querySelectorAll('#editAgenceForm .form-control');
               inputFields.forEach((field) => {
                   field.classList.remove('error-input');
               });
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
            * Ajouter des animations aux éléments de la liste
            */
           function animateListItems() {
               const items = document.querySelectorAll('.agence-item');
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
<?php require_once("../Template/footer.php"); ?>