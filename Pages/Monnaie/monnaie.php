<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../Login/login.php");
    exit;
  }
require_once("../Template/header.php"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Monnaies</title>
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <!-- CSS intégré -->
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        :root {
            /* Main color palette */
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
            padding: 1.5rem;
            width: 100%;
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

        .search-form {
            position: relative;
        }

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

        .search-form input,
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

        .search-form input:hover,
        .form-control:hover {
            border-color: var(--grey-400);
        }

        .search-form input:focus,
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .search-form .icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--grey-500);
            font-size: 1rem;
            transition: color var(--transition-fast);
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
        /* Style pour la colonne d'actions */
        th:last-child {
            text-align: right;
            width: 120px;
        }

        /* Ajustement du bouton modifier */
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
            justify-content: center;
            gap: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            outline: none;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
            min-width: 100px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Style pour les numéros séquentiels dans un cercle bleu */
        .sequence-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            font-weight: 600;
            margin: 0 auto;
        }

        /* Pagination styles */
        .pagination-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--grey-200);
            width: 100%;
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
            width: 100%;
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
            width: 35%;
            max-width: 500px;
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

        /* Style pour les icônes dans les champs de formulaire */
        .input-with-icon {
            position: relative;
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

        .input-with-icon .form-control {
            padding-left: 2.5rem;
        }

        /* Error message styling */
        .error-message {
            color: var(--danger);
            font-size: 0.8125rem;
            margin-top: 0.375rem;
            padding-left: 0.5rem;
            border-left: 2px solid var(--danger);
            animation: fadeIn 0.3s ease;
        }

        .is-invalid {
            border-color: var(--danger) !important;
            background-color: rgba(220, 53, 69, 0.03);
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Style pour les boutons d'action dans les modales */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        /* Titre des modales */
        #modalTitle {
            font-size: 1.5rem;
            color: var(--grey-700);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--grey-200);
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
            <i class='bx bx-money'></i>
            Gestion des Monnaies
        </div>
        
        <div class="page">
            <!-- Barre d'action pour rechercher et ajouter -->
            <div class="action-bar">
                <!-- Recherche à gauche -->
                <div class="search-container">
                    <div class="search-form">
                        <div class="form-group">
                            <i class='bx bx-search icon'></i>
                            <input type="text" id="searchInput" placeholder="Rechercher..." class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- Bouton d'ajout à droite -->
                <button class="btn btn-primary" id="addMonnaieBtn">
                    <i class='bx bx-plus-circle'></i> Ajouter une Monnaie
                </button>
            </div>

            <div class="table-container slideUp">
                <table id="monnaieTable">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">N°</th>
                            <th>Code</th>
                            <th>Label</th>
                            <th>Symbole</th>
                            <th style="text-align: right; width: 120px;">Actions</th>
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
                    Affichage de 1 à 10 sur 0 monnaies
                </div>
                <ul class="pagination" id="pagination">
                    <!-- La pagination sera générée dynamiquement ici -->
                </ul>
            </div>
            
            <!-- Message pour aucun résultat de recherche -->
            <div class="no-results" id="noResults">
                <i class='bx bx-search'></i>
                <p>Aucune monnaie ne correspond à votre recherche.</p>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier une monnaie -->
    <div class="modal" id="monnaieModal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 id="modalTitle">Ajouter une Monnaie</h2>
            <form id="monnaieForm" autocomplete="off">
                <input type="hidden" id="monnaieId">
                <div class="form-group">
                    <label for="code">Code<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-code-alt'></i>
                        <input type="text" id="code" class="form-control">
                    </div>
                    <div class="error-message" id="codeError"></div>
                </div>
                <div class="form-group">
                    <label for="label">Label<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-tag'></i>
                        <input type="text" id="label" class="form-control">
                    </div>
                    <div class="error-message" id="labelError"></div>
                </div>
                <div class="form-group">
                    <label for="symbole">Symbole<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-dollar'></i>
                        <input type="text" id="symbole" class="form-control">
                    </div>
                    <div class="error-message" id="symboleError"></div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" id="btnAnnuler">
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

    <!-- Script principal -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Éléments du DOM
            const monnaieTableBody = document.querySelector("#monnaieTable tbody");
            const addform = document.getElementById("monnaieForm");
            const addmodal = document.getElementById("monnaieModal");
            const addBtn = document.getElementById("addMonnaieBtn");
            const searchInput = document.getElementById("searchInput");
            const closeAddModal = document.querySelector(".close-btn");
            const btnAnnuler = document.getElementById("btnAnnuler");
            const noResultsDiv = document.getElementById("noResults");
            const paginationContainer = document.getElementById("paginationContainer");
            const paginationInfo = document.getElementById("paginationInfo");
            const pagination = document.getElementById("pagination");
            
            // Champs du formulaire
            const codeFieldadd = document.getElementById("code");
            const labelFieldadd = document.getElementById("label");
            const symboleFieldadd = document.getElementById("symbole");
            const codeErroradd = document.getElementById("codeError");
            const labelErroradd = document.getElementById("labelError");
            const symboleErroradd = document.getElementById("symboleError");
            const idFieldadd = document.getElementById("monnaieId");
            
            // Variables globales
            let monnaies = []; // Stocke les données récupérées
            let currentPage = 1;
            const itemsPerPage = 10;
            let isCodeUnique = false;
            let isLabelUnique = false;
            
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
                
                // Récupérer les monnaies au chargement de la page
                fetchMonnaies();
            }
            
            /**
             * Récupère les monnaies depuis la base de données
             */
            function fetchMonnaies() {
                // Afficher un indicateur de chargement
                monnaieTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Chargement des monnaies...</td></tr>';
                
                fetch("../../Backend/Monnaie/requetes_ajax/fetch_monnaies.php")
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Erreur lors de la récupération des monnaies:', data.error);
                            monnaieTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--danger);">Une erreur est survenue lors du chargement des monnaies.</td></tr>';
                            return;
                        }
                        
                        monnaies = data; // Stocker les données récupérées
                        
                        // Afficher les données avec pagination
                        displayMonnaies(currentPage);
                        
                        // Générer la pagination
                        generatePagination();
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        monnaieTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--danger);">Une erreur est survenue lors du chargement des monnaies.</td></tr>';
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur !',
                            text: "Une erreur est survenue lors du chargement des monnaies.",
                            confirmButtonColor: '#2563eb'
                        });
                    });
            }
            
            /**
             * Affiche les monnaies pour la page spécifiée
             */
            function displayMonnaies(page) {
                // Calculer les indices de début et de fin pour la page actuelle
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, monnaies.length);
                
                // Filtrer les monnaies pour la recherche si nécessaire
                let filteredMonnaies = monnaies;
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                if (searchTerm) {
                    filteredMonnaies = monnaies.filter(monnaie =>
                        monnaie.id.toString().includes(searchTerm) ||
                        monnaie.code.toLowerCase().includes(searchTerm) ||
                        monnaie.label.toLowerCase().includes(searchTerm) ||
                        monnaie.symbole.toLowerCase().includes(searchTerm)
                    );
                }
                
                // Vérifier s'il y a des résultats
                if (filteredMonnaies.length === 0) {
                    monnaieTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Aucune monnaie trouvée</td></tr>';
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
                const currentItems = filteredMonnaies.slice(startIndex, endIndex);
                
                // Vider le tableau
                monnaieTableBody.innerHTML = '';
                
                // Remplir le tableau avec les données
                currentItems.forEach((monnaie, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="text-align: center;">
                            <div class="sequence-number">${startIndex + index + 1}</div>
                        </td>
                        <td>${monnaie.code}</td>
                        <td>${monnaie.label}</td>
                        <td>${monnaie.symbole}</td>
                        <td style="text-align: right; width: 120px;">
                            <button class="btn-edit" data-id="${monnaie.id}">
                                <i class='bx bx-edit'></i> Modifier
                            </button>
                        </td>
                    `;
                    monnaieTableBody.appendChild(row);
                });
                
                // Mettre à jour l'info de pagination
                paginationInfo.textContent = `Affichage de ${startIndex + 1} à ${endIndex} sur ${filteredMonnaies.length} monnaies`;
                
                // Afficher la pagination si nécessaire
                if (filteredMonnaies.length > itemsPerPage) {
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
                const totalPages = Math.ceil(monnaies.length / itemsPerPage);
                
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
                        displayMonnaies(currentPage);
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
                        displayMonnaies(currentPage);
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
                        displayMonnaies(currentPage);
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
                        displayMonnaies(currentPage);
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
                        displayMonnaies(currentPage);
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
                    displayMonnaies(currentPage);
                    
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
                        if (addform) addform.reset();
                        clearAddModalErrors();
                        idFieldadd.value = '';
                        document.getElementById('modalTitle').textContent = 'Ajouter une Monnaie';
                        showModal(addmodal);
                    });
                }
                
                // Fermer le modal avec le bouton X
                if (closeAddModal) {
                    closeAddModal.addEventListener("click", function() {
                        hideModal(addmodal);
                    });
                }
                
                // Fermer le modal avec le bouton Annuler
                if (btnAnnuler) {
                    btnAnnuler.addEventListener("click", function() {
                        hideModal(addmodal);
                    });
                }
                
                // Fermer le modal en cliquant en dehors
                window.addEventListener("click", function(event) {
                    if (event.target === addmodal) {
                        hideModal(addmodal);
                    }
                });
            }
            
            /**
             * Initialise les formulaires
             */
            function initializeForms() {
                // Vérification de l'unicité du code
                if (codeFieldadd) {
                    codeFieldadd.addEventListener("input", checkCodeUniqueness);
                }
                
                // Vérification de l'unicité du label
                if (labelFieldadd) {
                    labelFieldadd.addEventListener("input", checkLabelUniqueness);
                }
                
                // Réinitialiser l'erreur du symbole lors de la saisie
                if (symboleFieldadd) {
                    symboleFieldadd.addEventListener("input", () => {
                        symboleErroradd.textContent = "";
                        symboleErroradd.style.display = "none";
                    });
                }
                
                // Soumission du formulaire
                if (addform) {
                    addform.addEventListener("submit", async function(event) {
                        event.preventDefault();
                        
                        await checkCodeUniqueness();
                        await checkLabelUniqueness();
                        
                        if (!isCodeUnique || !isLabelUnique) {
                            return;
                        }
                        
                        // Récupérer les valeurs des champs
                        const id = idFieldadd.value.trim();
                        const code = codeFieldadd.value.trim();
                        const label = labelFieldadd.value.trim();
                        const symbole = symboleFieldadd.value.trim();
                        
                        // Validation des champs
                        let valid = true;
                        
                        if (!code) {
                            codeErroradd.textContent = "Ce champ est obligatoire.";
                            codeErroradd.style.display = "block";
                            valid = false;
                        }
                        if (!label) {
                            labelErroradd.textContent = "Ce champ est obligatoire.";
                            labelErroradd.style.display = "block";
                            valid = false;
                        }
                        if (!symbole) {
                            symboleErroradd.textContent = "Ce champ est obligatoire.";
                            symboleErroradd.style.display = "block";
                            valid = false;
                        }
                        if (!valid) return;
                        
                        // Préparer les données à envoyer
                        const jsonData = { id, code, label, symbole };
                        
                        // URL en fonction de l'action (ajout ou modification)
                        const url = id ? "../../Backend/Monnaie/requetes_ajax/update_monnaie.php" : "../../Backend/Monnaie/requetes_ajax/add_monnaie.php";
                        
                        // Envoyer la requête
                        fetch(url, {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(jsonData),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Succès!",
                                    text: data.success,
                                    confirmButtonColor: '#2563eb'
                                });
                                fetchMonnaies();
                                hideModal(addmodal);
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Erreur!",
                                    text: data.error,
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Erreur AJAX:", error);
                            Swal.fire({
                                icon: "error",
                                title: "Erreur!",
                                text: "Problème lors de l'opération.",
                                confirmButtonColor: '#2563eb'
                            });
                        });
                    });
                }
            }
            
            /**
             * Vérifie l'unicité du code
             */
            async function checkCodeUniqueness() {
                const code = codeFieldadd.value.trim();
                const id = idFieldadd.value.trim();
                
                try {
                    const response = await fetch("../../Backend/Monnaie/requetes_ajax/check_code_monnaies.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ code, id }),
                    });
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        codeErroradd.textContent = data.error;
                        codeErroradd.style.display = "block";
                        isCodeUnique = false;
                    } else {
                        codeErroradd.textContent = "";
                        codeErroradd.style.display = "none";
                        isCodeUnique = true;
                    }
                } catch (error) {
                    console.error("AJAX Error:", error);
                }
            }
            
            /**
             * Vérifie l'unicité du label
             */
            async function checkLabelUniqueness() {
                const label = labelFieldadd.value.trim();
                const id = idFieldadd.value.trim();
                
                try {
                    const response = await fetch("../../Backend/Monnaie/requetes_ajax/check_label.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ label, id }),
                    });
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        labelErroradd.textContent = data.error;
                        labelErroradd.style.display = "block";
                        isLabelUnique = false;
                    } else {
                        labelErroradd.textContent = "";
                        labelErroradd.style.display = "none";
                        isLabelUnique = true;
                    }
                } catch (error) {
                    console.error("AJAX Error:", error);
                }
            }
            
            /**
             * Attache des écouteurs aux boutons "Modifier"
             */
            function attachEditListeners() {
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation(); // Empêcher la propagation au parent
                        
                        const monnaieId = e.currentTarget.dataset.id;
                        clearAddModalErrors();
                        loadMonnaieDetails(monnaieId);
                    });
                });
            }
            
            /**
             * Charge les détails d'une monnaie pour modification
             */
            function loadMonnaieDetails(monnaieId) {
                fetch(`../../Backend/Monnaie/requetes_ajax/get_monnaie.php?id=${monnaieId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            Swal.fire({
                                icon: "error",
                                title: "Erreur!",
                                text: data.error,
                                confirmButtonColor: '#2563eb'
                            });
                            return;
                        }
                        
                        // Remplir le formulaire avec les données
                        idFieldadd.value = data.id;
                        codeFieldadd.value = data.code;
                        labelFieldadd.value = data.label;
                        symboleFieldadd.value = data.symbole;
                        
                        // Mettre à jour le titre du modal
                        document.getElementById('modalTitle').textContent = 'Modifier une Monnaie';
                        
                        // Afficher le modal
                        showModal(addmodal);
                    })
                    .catch(error => console.error("Fetch Error:", error));
            }
            
            /**
             * Efface les messages d'erreur du formulaire
             */
            function clearAddModalErrors() {
                codeErroradd.textContent = "";
                labelErroradd.textContent = "";
                symboleErroradd.textContent = "";
                codeErroradd.style.display = "none";
                labelErroradd.style.display = "none";
                symboleErroradd.style.display = "none";
            }
            
            /**
             * Fonction d'animation pour afficher une modal
             */
            function showModal(modal) {
                if (!modal) return;
                
                modal.style.display = "flex";
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
                const rows = document.querySelectorAll('#monnaieTable tbody tr');
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
                        if (!this.classList.contains("is-invalid") && document.activeElement !== this) {
                            this.style.borderColor = "var(--primary-light)";
                        }
                    });
                    
                    field.addEventListener("mouseleave", function () {
                        if (!this.classList.contains("is-invalid") && document.activeElement !== this) {
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