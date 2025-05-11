<?php
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Récupération et nettoyage des messages de session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
$show_success_alert = $_SESSION['show_success_alert'] ?? false;
unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['show_success_alert']);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Garanties Bancaires</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="listesGaranties.css">
    <style>
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

        body {
            background-color: #f0f5ff;
            color: var(--gray-800);
            font-family: "Poppins", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1320px;
            padding: 1.5rem;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--box-shadow-lg);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1.25rem 1.5rem;
        }

        .card-header h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .card-header h2 i {
            margin-right: 0.75rem;
            font-size: 1.75rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Styles pour les badges de statut */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.35em 0.8em;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 50rem;
            transition: var(--transition);
        }

        .status-pill.status-valide {
            background-color: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        .status-pill.status-expiree {
            background-color: rgba(239, 68, 68, 0.15);
            color: #b91c1c;
        }

        .status-pill.status-liberee {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        /* Style pour les boutons de tri */
        .sortable {
            cursor: pointer;
            position: relative;
            user-select: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .sortable:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--primary);
        }

        .sortable i {
            font-size: 1rem;
            vertical-align: middle;
            margin-left: 5px;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .sortable.active {
            color: var(--primary);
        }

        .sortable.active i {
            opacity: 1;
            color: var(--primary);
        }

        /* Style pour le tableau */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.01);
        }

        /* Style pour les boutons d'action */
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 2px;
            transition: var(--transition);
            padding: 0;
            font-size: 1.1rem;
            border: none;
            background-color: transparent;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-info {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .btn-info:hover, .btn-info:focus {
            background-color: rgba(59, 130, 246, 0.25);
            color: #1e40af;
        }

        .btn-success {
            background-color: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        .btn-success:hover, .btn-success:focus {
            background-color: rgba(16, 185, 129, 0.25);
            color: #047857;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            color: white;
        }

        .btn-warning {
            background-color: rgba(245, 158, 11, 0.15);
            color: #b45309;
        }

        .btn-warning:hover, .btn-warning:focus {
            background-color: rgba(245, 158, 11, 0.25);
            color: #b45309;
        }

        .btn-outline-secondary {
            border: 1px solid var(--gray-300);
            color: var(--gray-500);
        }

        /* Animation pour le bouton de libération */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .liberation-btn:hover {
            animation: pulse 1.5s infinite;
        }

        /* Style pour le message "aucun résultat" */
        #noResults {
            padding: 3rem 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            text-align: center;
            border: 1px dashed #dee2e6;
        }

        #noResults i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        #noResults p {
            font-size: 1rem;
            color: #6c757d;
        }

        /* Styles pour la pagination */
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: var(--primary);
            border: 1px solid #dee2e6;
            margin: 0 2px;
            border-radius: 4px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
            transform: translateY(-1px);
        }

        .page-link:hover {
            color: var(--primary-dark);
            background-color: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        /* Style pour la barre d'information */
        .showing-entries {
            color: #6c757d;
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }

        /* Styles pour les filtres */
        .filter-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            border: 1px solid #f0f0f0;
        }

        .form-control, .form-select {
            height: 42px;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            transition: var(--transition);
            box-shadow: none;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }

        .input-group-text {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-right: none;
            padding-left: 1rem;
            color: #adb5bd;
        }

        .input-group .form-control {
            border-left: none;
        }

        /* Style pour les alertes */
        .alert {
            border-radius: var(--border-radius);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            color: #047857;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            color: #b91c1c;
        }

        /* Styles pour les montants */
        .montant {
            font-weight: 500;
            color: #495057;
        }

        /* Style pour les dates */
        .date {
            color: #6c757d;
            white-space: nowrap;
        }

        /* Style pour ajouter un bouton */
        .btn-add {
            padding: 0.6rem 1.25rem;
            font-weight: 500;
            border-radius: 50rem;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.15);
            transition: all 0.3s ease;
            background-color: var(--primary);
            color: white;
            border: none;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(59, 130, 246, 0.2);
            background-color: var(--primary-hover);
        }

        /* Style pour le bouton de recherche */
        .search-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1rem;
            color: #adb5bd;
            display: none;
            cursor: pointer;
        }

        /* Tooltip styles */
        .tooltip-container {
            position: relative;
            display: inline-block;
        }

        .tooltip-text {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.75rem;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .table-responsive {
                border-radius: var(--border-radius);
                overflow: hidden;
            }
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: start !important;
            }
            
            .card-header h2 {
                margin-bottom: 1rem;
            }
            
            .filter-container .row > div {
                margin-bottom: 0.75rem;
            }

            .action-btn {
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0.75rem;
            }

            .card-body {
                padding: 1rem;
            }

            .filter-container {
                padding: 1rem;
            }

            .table thead th, .table tbody td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }

            .status-pill {
                font-size: 0.7rem;
                padding: 0.25em 0.6em;
            }

            .action-btn {
                margin: 0 1px;
            }
        }

        /* Animation d'entrée */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Loader */
        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid var(--gray-200);
            border-bottom-color: var(--primary);
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Tooltip pour les actions */
        [data-tooltip] {
            position: relative;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background-color: var(--gray-800);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }

        /* Styles pour les boutons d'action dans le tableau */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 4px;
        }

        /* Style pour le bouton de libération déjà libérée */
        .btn-already-liberated {
            background-color: rgba(156, 163, 175, 0.15);
            color: var(--gray-500);
            cursor: pointer;
        }

        .btn-already-liberated:hover {
            background-color: rgba(156, 163, 175, 0.25);
            box-shadow: none;
            transform: none;
        }

        /* Style pour les tooltips des boutons */
        .action-tooltip {
            position: relative;
        }

        .action-tooltip .tooltip-text {
            width: auto;
            min-width: 120px;
            white-space: nowrap;
        }
        
    </style>
</head>
<body>
    <div class="listegarantie-container">
        <?php if (!empty($success_message) && $show_success_alert): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-check-circle me-2 fs-5'></i>
                    <div><?= htmlspecialchars($success_message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-error-circle me-2 fs-5'></i>
                    <div><?= htmlspecialchars($error_message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h2 class="text-primary">
                    <i class='bx bx-shield me-2'></i>Liste des Garanties
                </h2>
                <a href="add_garantie.php" class="btn btn-add d-flex align-items-center">
                    <i class='bx bx-plus-circle me-2'></i>Ajouter une garantie
                </a>
            </div>
            <div class="card-body">
                <!-- Filtres améliorés -->
                <div class="filter-container mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group position-relative">
                                <span class="input-group-text bg-white"><i class='bx bx-search'></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par numéro, fournisseur, direction...">
                                <button type="button" id="clearSearch" class="search-clear" aria-label="Effacer la recherche">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="Valide">Valide</option>
                                <option value="Expiree">Expirée</option>
                                <option value="Liberee">Libérée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="sortSelect" class="form-select">
                                <option value="date_creation_desc">Date création (récent)</option>
                                <option value="date_creation_asc">Date création (ancien)</option>
                                <option value="date_validite_asc">Date validité (croissant)</option>
                                <option value="date_validite_desc">Date validité (décroissant)</option>
                                <option value="montant_desc">Montant (décroissant)</option>
                                <option value="montant_asc">Montant (croissant)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="garantiesTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="num_garantie">Numéro <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="date_creation">Date Création <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="date_validite">Validité <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="montant">Montant <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="direction">Direction <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="fournisseur">Fournisseur <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="statut">Statut <i class='bx bx-sort'></i></th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($garanties)): ?>
                                <?php foreach ($garanties as $row): 
                                    $validite = new DateTime($row['date_validite']);
                                    
                                    // Détermination du statut
                                    if (!empty($row['liberation_id'])) {
                                        $status = 'Liberee';
                                        $statusClass = 'liberee';
                                    } else {
                                        $status = ($validite < $today) ? 'Expiree' : 'Valide';
                                        $statusClass = ($status === 'Expiree') ? 'expiree' : 'valide';
                                    }
                                ?>
                                <tr 
                                    data-num="<?= htmlspecialchars($row['num_garantie']) ?>"
                                    data-date="<?= $row['date_creation'] ?>"
                                    data-validite="<?= $row['date_validite'] ?>"
                                    data-montant="<?= $row['montant'] ?>"
                                    data-direction="<?= htmlspecialchars($row['direction']) ?>"
                                    data-fournisseur="<?= htmlspecialchars($row['nom_fournisseur']) ?>"
                                    data-statut="<?= $status ?>"
                                >
                                    <td><strong><?= htmlspecialchars($row['num_garantie']) ?></strong></td>
                                    <td class="date"><?= date('d/m/Y', strtotime($row['date_creation'])) ?></td>
                                    <td class="date"><?= date('d/m/Y', strtotime($row['date_validite'])) ?></td>
                                    <td class="montant"><?= number_format($row['montant'], 2, ',', ' ') . ' ' . htmlspecialchars($row['monnaie_symbole']) ?></td>
                                    <td><?= htmlspecialchars($row['direction']) ?></td>
                                    <td><?= htmlspecialchars($row['nom_fournisseur']) ?></td>
                                    <td>
                                        <span class="status-pill status-<?= $statusClass ?>">
                                            <?php if ($status === 'Valide'): ?>
                                                <i class='bx bx-check-circle me-1'></i>
                                            <?php elseif ($status === 'Expiree'): ?>
                                                <i class='bx bx-time me-1'></i>
                                            <?php else: ?>
                                                <i class='bx bx-lock-open me-1'></i>
                                            <?php endif; ?>
                                        
                                            <?= $status === 'Liberee' ? 'Libérée' : ($status === 'Expiree' ? 'Expirée' : 'Valide') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="../../Backend/Garantie/view_garantie.php?id=<?= $row['garantie_id'] ?>" 
                                               class="action-btn btn-info" 
                                               data-tooltip="Voir les détails">
                                                <i class='bx bx-show'></i>
                                            </a>
                                            
                                            <a href="../../Backend/Garantie/edit_garantie.php?id=<?= $row['garantie_id'] ?>" 
                                               class="action-btn btn-primary" 
                                               data-tooltip="Modifier la garantie">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            
                                            <form method="GET" action="../Amandements/Amandement.php" class="d-inline">
                                                <input type="hidden" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                                <button type="submit" 
                                                       class="action-btn btn-warning" 
                                                       data-tooltip="Ajouter un amendement">
                                                    <i class='bx bx-edit-alt'></i>
                                                </button>
                                            </form>

                                            <form method="GET" action="../Authentification/Authentification.php" class="d-inline">
                                                <input type="hidden" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                                <button type="submit" 
                                                       class="action-btn btn-info" 
                                                       data-tooltip="Authentifier cette garantie">
                                                    <i class='bx bx-check-shield'></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Modification ici: permettre l'accès à la page de libération même si déjà libérée -->
                                            <form method="POST" action="../Liberation/liberation.php" class="d-inline">
                                                <input type="hidden" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                                <?php if (empty($row['liberation_id'])): ?>
                                                    <button type="submit" 
                                                           class="action-btn btn-success liberation-btn" 
                                                           data-tooltip="Libérer cette garantie">
                                                        <i class='bx bx-lock-open'></i>
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Bouton modifié pour permettre l'accès à la page de libération -->
                                                    <button type="submit" 
                                                           class="action-btn btn-already-liberated" 
                                                           data-tooltip="Voir la libération du <?= date('d/m/Y', strtotime($row['date_liberation'] ?? 'now')) ?>">
                                                        <i class='bx bx-lock-open-alt'></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class='bx bx-info-circle fs-1 text-muted mb-3'></i>
                                            <h5 class="text-muted mb-2">Aucune garantie disponible</h5>
                                            <p class="text-muted mb-4">Aucune garantie n'a été trouvée dans la base de données.</p>
                                            <a href="add_garantie.php" class="btn btn-sm btn-outline-primary">
                                                <i class='bx bx-plus-circle me-1'></i>Ajouter une garantie
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div id="noResults" class="text-center mt-4" style="display: none;">
                    <div class="d-flex flex-column align-items-center p-4">
                        <i class='bx bx-search-alt fs-1 text-muted mb-3'></i>
                        <h5 class="text-muted mb-2">Aucun résultat trouvé</h5>
                        <p class="text-muted">Aucune garantie ne correspond à votre recherche. Essayez d'autres critères.</p>
                        <button id="resetFilters" class="btn btn-sm btn-outline-primary mt-2">
                            <i class='bx bx-reset me-1'></i>Réinitialiser les filtres
                        </button>
                    </div>
                </div>
                
                <!-- Informations de pagination améliorées -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4">
                    <div class="showing-entries mb-3 mb-md-0">
                        Affichage <span id="startEntry">1</span> à <span id="endEntry">10</span> sur <span id="totalEntries"><?= count($garanties) ?></span> entrées
                    </div>
                    <nav id="paginationNav">
                        <ul class="pagination pagination-sm justify-content-center mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>


</body>
</html>

<?php
require_once ('../Template/footer.php');
?>
