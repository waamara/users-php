<?php
// Démarrer la session
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['user_role'] !== 'admin' ) {
    header("Location: ../Login/login.php");
    
    exit;
  }
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

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
    <title>Gestion des Fournisseurs</title>
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

        .error, .validation-message {
            display: none;
            color: var(--danger);
            font-size: 0.8125rem;
            margin-top: 0.375rem;
            padding-left: 0.5rem;
            border-left: 2px solid var(--danger);
            animation: fadeIn 0.3s ease;
        }

        .error.show, .validation-message.show {
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

        /* Table styles - Style amélioré */
        .table-container {
            width: 100%;
            background: rgb(255, 255, 255);
           
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* En-tête de tableau gris au lieu de bleu */
        th {
            background: var(--grey-100);
            color: var(--grey-700);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid var(--grey-200);
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background-color: #f5f5f5;
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
            box-shadow: var(--shadow-lg);
            position: relative;
            transform: translateY(-30px) scale(0.95);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .modal.show .modal-content {
            transform: translateY(0) scale(1);
        }

        .close-btn {
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

        .close-btn:hover {
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
            <i class='bx bx-package'></i>
            Gestion des Fournisseurs
        </div>
        
        <div class="page">
            <div class="form-container">
                <div class="form-title">
                    <i class='bx bx-list-ul'></i>
                    Liste des Fournisseurs
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
                                <input type="text" id="searchInput" placeholder="Rechercher un fournisseur..." class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bouton d'ajout à droite -->
                    <div class="action-buttons">
                        <button id="addFournisseurBtn" class="btn btn-primary">
                            <i class='bx bx-plus-circle'></i> Ajouter un Fournisseur
                        </button>
                    </div>
                </div>

                <div class="table-container slideUp">
                    <table id="fournisseursTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code Fournisseur</th>
                                <th>Nom Fournisseur</th>
                                <th>Raison Sociale</th>
                                <th>Pays</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les données seront chargées dynamiquement ici -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-container" id="paginationContainer">
                    <div class="pagination-info" id="paginationInfo">
                        Affichage de 1 à 10 sur 0 fournisseurs
                    </div>
                    <ul class="pagination" id="pagination">
                        <!-- La pagination sera générée dynamiquement ici -->
                    </ul>
                </div>
                
                <!-- Message pour aucun résultat de recherche -->
                <div class="no-results" id="noResults">
                    <i class='bx bx-search'></i>
                    <p>Aucun fournisseur ne correspond à votre recherche.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier un fournisseur -->
    <div id="fournisseurModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="form-title">
                <i class='bx bx-plus-circle'></i>
                <span id="modalTitle">Ajouter un Fournisseur</span>
            </div>
            <form id="fournisseurForm">
                <input type="hidden" id="fournisseurId">
                <div class="form-group">
                    <label for="codeFournisseur">Code Fournisseur<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-code-alt'></i>
                        <input type="text" id="codeFournisseur" class="form-control" maxlength="50">
                    </div>
                    <span class="validation-message" id="codeError">Ce champ est obligatoire.</span>
                </div>
                <div class="form-group">
                    <label for="nomFournisseur">Nom Fournisseur<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-user'></i>
                        <input type="text" id="nomFournisseur" class="form-control" maxlength="50">
                    </div>
                    <span class="validation-message" id="nomError">Ce champ est obligatoire.</span>
                </div>
                <div class="form-group">
                    <label for="raisonSociale">Raison Sociale<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-building'></i>
                        <input type="text" id="raisonSociale" class="form-control" maxlength="100">
                    </div>
                    <span class="validation-message" id="raisonError">Ce champ est obligatoire.</span>
                </div>
                <div class="form-group">
                    <label for="paysId">Pays<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-map'></i>
                        <select id="paysId" class="form-control">
                            <option value="" disabled selected>Sélectionnez un pays</option>
                            <!-- Options will be dynamically populated here -->
                        </select>
                    </div>
                    <span class="validation-message" id="paysError">Veuillez sélectionner un pays.</span>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" id="btnAnnuler">
                        <i class='bx bx-x'></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save'></i> Enregistrer
                    </button>
                </div>
                <div class="error-message" id="formError"></div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.js"></script>

    <!-- Script principal avec toutes les corrections -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Éléments du DOM
            const tableBody = document.querySelector('#fournisseursTable tbody');
            const searchInput = document.getElementById('searchInput');
            const addBtn = document.getElementById('addFournisseurBtn');
            const modal = document.getElementById('fournisseurModal');
            const form = document.getElementById('fournisseurForm');
            const closeBtn = document.querySelector('.close-btn');
            const btnAnnuler = document.getElementById('btnAnnuler');
            const formError = document.getElementById('formError');
            const paysDropdown = document.getElementById('paysId');
            const noResultsDiv = document.getElementById('noResults');
            const paginationContainer = document.getElementById('paginationContainer');
            const paginationInfo = document.getElementById('paginationInfo');
            const pagination = document.getElementById('pagination');
            
            // Variables globales
            let fournisseurs = []; // Stocke les données récupérées
            let currentPage = 1;
            const itemsPerPage = 10;
            
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
                
                // Récupérer les fournisseurs au chargement de la page
                fetchFournisseurs();
                
                // Récupérer les pays pour le dropdown
                fetchCountries();
            }
            
            /**
             * Récupère les fournisseurs depuis la base de données
             */
            async function fetchFournisseurs() {
                try {
                    // Afficher un indicateur de chargement
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Chargement des fournisseurs...</td></tr>';
                    
                    const response = await fetch('../../Backend/Fournisseur/requetes_ajax/get_fournisseurs.php');
                    if (!response.ok) throw new Error('Erreur réseau');
                    
                    const data = await response.json();
                    if (data.error) {
                        console.error('Erreur lors de la récupération des fournisseurs:', data.error);
                        tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--danger);">Une erreur est survenue lors du chargement des fournisseurs.</td></tr>';
                        return;
                    }
                    
                    fournisseurs = data.data; // Stocker les données récupérées
                    
                    // Afficher les données avec pagination
                    displayFournisseurs(currentPage);
                    
                    // Générer la pagination
                    generatePagination();
                } catch (error) {
                    console.error('Erreur:', error);
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--danger);">Une erreur est survenue lors du chargement des fournisseurs.</td></tr>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur !',
                        text: "Une erreur est survenue lors du chargement des fournisseurs.",
                        confirmButtonColor: '#2563eb'
                    });
                }
            }
            
            /**
             * Affiche les fournisseurs pour la page spécifiée
             */
            function displayFournisseurs(page) {
                // Calculer les indices de début et de fin pour la page actuelle
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, fournisseurs.length);
                
                // Filtrer les fournisseurs pour la recherche si nécessaire
                let filteredFournisseurs = fournisseurs;
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                if (searchTerm) {
                    filteredFournisseurs = fournisseurs.filter(f =>
                        f.code_fournisseur.toLowerCase().includes(searchTerm) ||
                        f.nom_fournisseur.toLowerCase().includes(searchTerm) ||
                        (f.raison_sociale && f.raison_sociale.toLowerCase().includes(searchTerm)) ||
                        (f.pays_label && f.pays_label.toLowerCase().includes(searchTerm))
                    );
                }
                
                // Vérifier s'il y a des résultats
                if (filteredFournisseurs.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aucun fournisseur trouvé</td></tr>';
                    paginationContainer.style.display = 'none';
                    
                    // Afficher le message "aucun résultat" si une recherche est en cours
                    if (searchTerm) {
                        noResultsDiv.style.display = 'flex';
                    } else {
                        noResultsDiv.style.display = 'none';
                    }
                    
                    return;
                }
                
                // Cacher le message "aucun résultat"
                noResultsDiv.style.display = 'none';
                
                // Obtenir les éléments pour la page actuelle
                const currentItems = filteredFournisseurs.slice(startIndex, endIndex);
                
                // Vider le tableau
                tableBody.innerHTML = '';
                
                // Remplir le tableau avec les données
                currentItems.forEach((f, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${f.id}</td>
                        <td>${f.code_fournisseur}</td>
                        <td>${f.nom_fournisseur}</td>
                        <td>${f.raison_sociale || '-'}</td>
                        <td>${f.pays_label || '-'}</td>
                        <td>
                            <button class="btn-edit" data-id="${f.id}">
                                <i class='bx bx-edit'></i> Modifier
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                // Mettre à jour l'info de pagination
                paginationInfo.textContent = `Affichage de ${startIndex + 1} à ${endIndex} sur ${filteredFournisseurs.length} fournisseurs`;
                
                // Afficher la pagination si nécessaire
                if (filteredFournisseurs.length > itemsPerPage) {
                    paginationContainer.style.display = 'flex';
                } else {
                    paginationContainer.style.display = 'none';
                }
                
                // Attacher les écouteurs aux boutons "Modifier"
                attachEditListeners();
                
                // Ajouter des animations aux lignes du tableau
                animateTableRows();
            }
            
            /**
             * Génère la pagination
             */
            function generatePagination() {
                // Calculer le nombre total de pages
                const totalPages = Math.ceil(fournisseurs.length / itemsPerPage);
                
                // Vider la pagination
                pagination.innerHTML = '';
                
                // Bouton précédent
                const prevLi = document.createElement('li');
                if (currentPage > 1) {
                    const prevLink = document.createElement('a');
                    prevLink.href = '#';
                    prevLink.innerHTML = '<i class="bx bx-chevron-left"></i>';
                    prevLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage--;
                        displayFournisseurs(currentPage);
                        generatePagination();
                    });
                    prevLi.appendChild(prevLink);
                } else {
                    const prevSpan = document.createElement('span');
                    prevSpan.innerHTML = '<i class="bx bx-chevron-left"></i>';
                    prevLi.className = 'disabled';
                    prevLi.appendChild(prevSpan);
                }
                pagination.appendChild(prevLi);
                
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
                        displayFournisseurs(currentPage);
                        generatePagination();
                    });
                    firstLi.appendChild(firstLink);
                    pagination.appendChild(firstLi);
                    
                    if (startPage > 2) {
                        const ellipsisLi = document.createElement('li');
                        ellipsisLi.className = 'disabled';
                        const ellipsisSpan = document.createElement('span');
                        ellipsisSpan.textContent = '...';
                        ellipsisLi.appendChild(ellipsisSpan);
                        pagination.appendChild(ellipsisLi);
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
                        displayFournisseurs(currentPage);
                        generatePagination();
                    });
                    
                    pageLi.appendChild(pageLink);
                    pagination.appendChild(pageLi);
                }
                
                // Dernière page et ellipsis
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const ellipsisLi = document.createElement('li');
                        ellipsisLi.className = 'disabled';
                        const ellipsisSpan = document.createElement('span');
                        ellipsisSpan.textContent = '...';
                        ellipsisLi.appendChild(ellipsisSpan);
                        pagination.appendChild(ellipsisLi);
                    }
                    
                    const lastLi = document.createElement('li');
                    const lastLink = document.createElement('a');
                    lastLink.href = '#';
                    lastLink.textContent = totalPages;
                    lastLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = totalPages;
                        displayFournisseurs(currentPage);
                        generatePagination();
                    });
                    lastLi.appendChild(lastLink);
                    pagination.appendChild(lastLi);
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
                        displayFournisseurs(currentPage);
                        generatePagination();
                    });
                    nextLi.appendChild(nextLink);
                } else {
                    const nextSpan = document.createElement('span');
                    nextSpan.innerHTML = '<i class="bx bx-chevron-right"></i>';
                    nextLi.className = 'disabled';
                    nextLi.appendChild(nextSpan);
                }
                pagination.appendChild(nextLi);
            }
            
            /**
             * Récupère les pays depuis la base de données
             */
            async function fetchCountries() {
                try {
                    const response = await fetch('../../Backend/Fournisseur/requetes_ajax/get_pays.php');
                    if (!response.ok) throw new Error('Erreur réseau');
                    
                    const data = await response.json();
                    if (data.status === 'success') {
                        // Vider les options existantes
                        paysDropdown.innerHTML = '';
                        
                        // Ajouter l'option par défaut
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = 'Sélectionnez un pays';
                        defaultOption.disabled = true;
                        defaultOption.selected = true;
                        paysDropdown.appendChild(defaultOption);
                        
                        // Remplir le dropdown avec les pays
                        data.data.forEach(country => {
                            const option = document.createElement('option');
                            option.value = country.id;
                            option.textContent = country.label;
                            paysDropdown.appendChild(option);
                        });
                    } else {
                        console.error('Erreur lors de la récupération des pays:', data.message);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur !',
                        text: "Une erreur est survenue lors du chargement des pays.",
                        confirmButtonColor: '#2563eb'
                    });
                }
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
                    // Réinitialiser la page courante lors d'une recherche
                    currentPage = 1;
                    
                    // Afficher les résultats filtrés
                    displayFournisseurs(currentPage);
                    
                    // Mettre à jour la pagination
                    generatePagination();
                });
            }
            
            /**
             * Initialise les modales
             */
            function initializeModals() {
                // Événement pour ouvrir le modal d'ajout
                if (addBtn) {
                    addBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        if (form) form.reset();
                        clearErrors();
                        document.getElementById('modalTitle').textContent = 'Ajouter un Fournisseur';
                        document.getElementById('fournisseurId').value = '';
                        showModal(modal);
                    });
                }
                
                // Fermer le modal avec le bouton X
                if (closeBtn) {
                    closeBtn.addEventListener("click", function() {
                        hideModal(modal);
                    });
                }
                
                // Fermer le modal avec le bouton Annuler
                if (btnAnnuler) {
                    btnAnnuler.addEventListener("click", function() {
                        hideModal(modal);
                    });
                }
                
                // Fermer le modal en cliquant en dehors
                window.addEventListener("click", function(event) {
                    if (event.target === modal) {
                        hideModal(modal);
                    }
                });
            }
            
            /**
             * Initialise les formulaires
             */
            function initializeForms() {
                // Soumission du formulaire
                if (form) {
                    form.addEventListener("submit", async function(event) {
                        event.preventDefault();
                        formError.style.display = 'none'; // Réinitialiser le message d'erreur général
                        
                        // Effacer les messages de validation précédents
                        clearErrors();
                        
                        // Récupérer les valeurs des champs
                        const id = document.getElementById('fournisseurId').value;
                        const codeFournisseur = document.getElementById('codeFournisseur').value.trim();
                        const nomFournisseur = document.getElementById('nomFournisseur').value.trim();
                        const raisonSociale = document.getElementById('raisonSociale').value.trim();
                        const paysId = document.getElementById('paysId').value;
                        
                        // Validation des champs
                        let isValid = true;
                        
                        // Valider Code Fournisseur
                        if (!codeFournisseur) {
                            document.getElementById('codeError').style.display = 'block';
                            isValid = false;
                        }
                        
                        // Valider Nom Fournisseur
                        if (!nomFournisseur) {
                            document.getElementById('nomError').style.display = 'block';
                            isValid = false;
                        }
                        
                        // Valider Raison Sociale
                        if (!raisonSociale) {
                            document.getElementById('raisonError').style.display = 'block';
                            isValid = false;
                        }
                        
                        // Valider Pays
                        if (!paysId) {
                            document.getElementById('paysError').style.display = 'block';
                            isValid = false;
                        }
                        
                        if (!isValid) return; // Arrêter si la validation échoue
                        
                        // Afficher confirmation avant soumission
                        confirmAndSubmit(id, codeFournisseur, nomFournisseur, raisonSociale, paysId);
                    });
                }
            }
            
            /**
             * Confirme et soumet le formulaire
             */
            function confirmAndSubmit(id, codeFournisseur, nomFournisseur, raisonSociale, paysId) {
                const isAdd = !id;
                const actionText = isAdd ? "ajouter" : "modifier";
                
                Swal.fire({
                    title: "Confirmation",
                    text: `Voulez-vous ${actionText} ce fournisseur ?`,
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
                        const requestData = {
                            id: id || null,
                            code_fournisseur: codeFournisseur,
                            nom_fournisseur: nomFournisseur,
                            raison_sociale: raisonSociale || '',
                            pays_id: paysId || ''
                        };
                        
                        // URL de l'API
                        const url = isAdd 
                            ? '../../Backend/Fournisseur/requetes_ajax/add_fournisseur.php'
                            : '../../Backend/Fournisseur/requetes_ajax/update_fournisseur.php';
                        
                        // Envoyer la requête
                        fetch(url, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(requestData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            
                            if (data.status === 'success') {
                                // Fermer le modal
                                hideModal(modal);
                                
                                // Afficher le message de succès
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès !',
                                    text: isAdd ? 'Le fournisseur a été ajouté avec succès.' : 'Le fournisseur a été modifié avec succès.',
                                    confirmButtonColor: '#2563eb'
                                }).then(() => {
                                    // Actualiser les données
                                    fetchFournisseurs();
                                    
                                    // Réinitialiser le formulaire
                                    if (form) {
                                        form.reset();
                                    }
                                });
                            } else {
                                // Gérer l'erreur d'entrée en double
                                if (data.message && data.message.includes('Duplicate entry')) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur !',
                                        text: 'Le code ou Le nom du fournisseur existe déjà. Veuillez choisir un autre code.',
                                        confirmButtonColor: '#2563eb'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur !',
                                        text: data.message || 'Une erreur est survenue. Veuillez réessayer.',
                                        confirmButtonColor: '#2563eb'
                                    });
                                }
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
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        e.preventDefault();
                        e.stopPropagation(); // Empêcher la propagation au parent
                        
                        const id = e.currentTarget.dataset.id;
                        const f = fournisseurs.find(f => f.id == id);
                        
                        if (!f) return;
                        
                        // Pré-remplissage des champs du modal de modification
                        document.getElementById('modalTitle').textContent = 'Modifier un Fournisseur';
                        document.getElementById('fournisseurId').value = f.id;
                        document.getElementById('codeFournisseur').value = f.code_fournisseur;
                        document.getElementById('nomFournisseur').value = f.nom_fournisseur;
                        document.getElementById('raisonSociale').value = f.raison_sociale || '';
                        
                        // S'assurer que le dropdown est rempli
                        if (paysDropdown.options.length <= 1) {
                            await fetchCountries();
                        }
                        
                        // Définir le pays sélectionné
                        if (f.pays_id && paysDropdown.querySelector(`option[value="${f.pays_id}"]`)) {
                            paysDropdown.value = f.pays_id;
                        } else {
                            paysDropdown.value = '';
                        }
                        
                        // Effacer les erreurs
                        clearErrors();
                        
                        // Afficher le modal
                        showModal(modal);
                    });
                });
            }
            
            /**
             * Efface les messages d'erreur
             */
            function clearErrors() {
                document.querySelectorAll('.validation-message').forEach(message => {
                    message.style.display = 'none';
                });
                
                formError.style.display = 'none';
                formError.textContent = '';
                
                const inputFields = document.querySelectorAll('.form-control');
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
             * Ajouter des animations aux lignes du tableau
             */
            function animateTableRows() {
                const rows = document.querySelectorAll('#fournisseursTable tbody tr');
                rows.forEach((row, index) => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        row.style.transition = 'all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
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