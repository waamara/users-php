<?php
// Vérifier si la session est déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit;
}

require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Initialiser les variables de filtrage
$periodeFiltre = isset($_GET['periode']) ? $_GET['periode'] : '';
$typeAlerte = isset($_GET['type_alerte']) ? $_GET['type_alerte'] : '';
$directionFiltre = isset($_GET['direction']) ? $_GET['direction'] : ''; // Nouveau filtre par direction

// Pagination
$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Récupérer toutes les directions pour le filtre
$sqlDirections = "SELECT id, libelle FROM direction ORDER BY libelle ASC";
$stmtDirections = $pdo->prepare($sqlDirections);
$stmtDirections->execute();
$directions = $stmtDirections->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les garanties actives (non libérées)
$sql = "SELECT g.id, g.num_garantie, g.date_creation, g.date_emission, g.date_validite, 
               d.libelle as structure, d.id as direction_id, f.nom_fournisseur,
               DATEDIFF(g.date_validite, CURDATE()) as jours_restants
        FROM garantie g
        LEFT JOIN liberation l ON g.id = l.garantie_id
        LEFT JOIN direction d ON g.direction_id = d.id
        LEFT JOIN fournisseur f ON g.fournisseur_id = f.id
        WHERE l.id IS NULL";

// Ajouter les filtres de période
if (!empty($periodeFiltre)) {
    switch ($periodeFiltre) {
        case 'today':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 0 AND 1";
            break;
        case 'this_week':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 0 AND 7";
            break;
        case 'next_week':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 8 AND 14";
            break;
        case 'this_month':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 0 AND 30";
            break;
        case 'next_month':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 31 AND 60";
            break;
        case 'expired':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) < 0";
            break;
    }
}

// Ajouter le filtre de type d'alerte selon les nouveaux critères
if (!empty($typeAlerte)) {
    switch ($typeAlerte) {
        case 'expired':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) < 0";
            break;
        case 'critical':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 0 AND 1";
            break;
        case 'urgent':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 3 AND 5";
            break;
        case 'preventive':
            $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) BETWEEN 8 AND 21";
            break;
    }
}

// Ajouter le filtre par direction
if (!empty($directionFiltre)) {
    $sql .= " AND g.direction_id = :direction_id";
}

// Limiter aux garanties qui expirent dans les 30 prochains jours ou qui sont déjà expirées
if (empty($periodeFiltre) && empty($typeAlerte)) {
    $sql .= " AND DATEDIFF(g.date_validite, CURDATE()) <= 30";
}

// Compter le nombre total d'éléments pour la pagination
$countSql = str_replace("SELECT g.id, g.num_garantie, g.date_creation, g.date_emission, g.date_validite, 
               d.libelle as structure, d.id as direction_id, f.nom_fournisseur,
               DATEDIFF(g.date_validite, CURDATE()) as jours_restants", "SELECT COUNT(*) as total", $sql);
$stmtCount = $pdo->prepare($countSql);

// Bind direction_id si nécessaire
if (!empty($directionFiltre)) {
    $stmtCount->bindParam(':direction_id', $directionFiltre);
}

$stmtCount->execute();
$totalItems = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Ajouter l'ordre de tri selon les filtres
if (!empty($typeAlerte)) {
    switch ($typeAlerte) {
        case 'expired':
            // Pour les alertes expirées, afficher les plus récemment expirées en premier
            $sql .= " ORDER BY DATEDIFF(g.date_validite, CURDATE()) DESC";
            break;
        case 'critical':
        case 'urgent':
        case 'preventive':
            // Pour les autres types d'alertes, afficher celles qui vont expirer le plus tôt
            $sql .= " ORDER BY DATEDIFF(g.date_validite, CURDATE()) ASC";
            break;
        default:
            // Par défaut, trier par gravité (les plus critiques d'abord)
            $sql .= " ORDER BY 
                    CASE 
                        WHEN DATEDIFF(g.date_validite, CURDATE()) = 1 THEN 1 -- Critique (1 jour)
                        WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 3 AND 5 THEN 2 -- Urgent (3-5 jours)
                        WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 8 AND 21 THEN 3 -- Préventif (8-21 jours)
                        WHEN DATEDIFF(g.date_validite, CURDATE()) < 0 THEN 4 -- Expirée
                        ELSE 5 -- Autres
                    END,
                    DATEDIFF(g.date_validite, CURDATE()) ASC";
    }
} else {
    // Sans filtre de type, trier selon l'ordre demandé: critique, urgent, préventif, expiré
    $sql .= " ORDER BY 
            CASE 
                WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 0 AND 1 THEN 1 -- Critique (0-1 jour)
                WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 3 AND 5 THEN 2 -- Urgent (3-5 jours)
                WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 8 AND 21 THEN 3 -- Préventif (8-21 jours)
                WHEN DATEDIFF(g.date_validite, CURDATE()) < 0 THEN 4 -- Expirée
                ELSE 5 -- Autres
            END,
            DATEDIFF(g.date_validite, CURDATE()) ASC";
}

// Ajouter la limite pour la pagination
$sql .= " LIMIT :offset, :limit";

// Exécuter la requête
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);

// Bind direction_id si nécessaire
if (!empty($directionFiltre)) {
    $stmt->bindParam(':direction_id', $directionFiltre);
}

$stmt->execute();
$garanties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter les garanties par type d'alerte selon les nouveaux critères
$stats = [
    'total' => $totalItems,
    'expired' => 0,
    'critical' => 0,
    'urgent' => 0,
    'preventive' => 0
];

// Requête pour compter les alertes par type avec les nouveaux critères
$sqlCounts = "SELECT 
    SUM(CASE WHEN DATEDIFF(g.date_validite, CURDATE()) < 0 THEN 1 ELSE 0 END) as expired,
    SUM(CASE WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 0 AND 1 THEN 1 ELSE 0 END) as critical,
    SUM(CASE WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 3 AND 5 THEN 1 ELSE 0 END) as urgent,
    SUM(CASE WHEN DATEDIFF(g.date_validite, CURDATE()) BETWEEN 8 AND 21 THEN 1 ELSE 0 END) as preventive
FROM garantie g
LEFT JOIN liberation l ON g.id = l.garantie_id
WHERE l.id IS NULL";

// Ajouter le filtre par direction pour les statistiques si nécessaire
if (!empty($directionFiltre)) {
    $sqlCounts .= " AND g.direction_id = :direction_id";
}

$stmtCounts = $pdo->prepare($sqlCounts);

// Bind direction_id si nécessaire
if (!empty($directionFiltre)) {
    $stmtCounts->bindParam(':direction_id', $directionFiltre);
}

$stmtCounts->execute();
$counts = $stmtCounts->fetch(PDO::FETCH_ASSOC);

$stats['expired'] = $counts['expired'] ?? 0;
$stats['critical'] = $counts['critical'] ?? 0;
$stats['urgent'] = $counts['urgent'] ?? 0;
$stats['preventive'] = $counts['preventive'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertes des Garanties</title>
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <!-- CSS intégré -->
    <style>
        /* Reset CSS pour éviter les interférences externes */
        .alertes-garanties-container * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            line-height: normal;
        }

        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        .alertes-garanties-container {
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
            
            /* Alert colors */
            --alert-green: #28a745;
            --alert-yellow: #ffc107;
            --alert-orange: #fd7e14;
            --alert-red: #dc3545;
            
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

            font-family: 'Poppins', sans-serif;
            background-color: var(--grey-50);
            color: var(--grey-700);
            line-height: 1.4;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            width: 100%;
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

        /* Stats cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background-color: var(--light);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-title {
            font-size: 0.875rem;
            color: var(--grey-600);
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--grey-800);
        }

        .stat-card.expired {
            border-left: 4px solid var(--grey-500);
        }

        .stat-card.critical {
            border-left: 4px solid var(--alert-red);
        }

        .stat-card.urgent {
            border-left: 4px solid var(--alert-orange);
        }

        .stat-card.preventive {
            border-left: 4px solid var(--alert-yellow);
        }

        .stat-card.total {
            border-left: 4px solid var(--primary);
        }

        /* Filter section */
        .filter-section {
            background-color: var(--grey-50);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .filter-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--grey-700);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .filter-title i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--grey-600);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid var(--grey-300);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            color: var(--grey-700);
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        .filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .btn-primary {
            margin-top: 7px;
            height: 45px;
            background-color: var(--primary);
            color: var(--light);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            margin-top: 7px;

            height: 45px;
            background-color: var(--grey-200);
            color: var(--grey-700);
        }

        .btn-secondary:hover {
            background-color: var(--grey-300);
        }

        .btn-success {
            background-color: var(--success);
            color: var(--light);
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: var(--danger);
            color: var(--light);
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-warning {
            background-color: var(--warning);
            color: var(--dark);
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        /* Notifications list */
        .notifications-container {
            margin-top: 1.5rem;
        }

        .notifications-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .notifications-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--grey-700);
        }

        .notification-item {
            background-color: var(--light);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            padding: 0.75rem 1rem; /* Réduit le padding */
            margin-bottom: 0.75rem; /* Réduit la marge */
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
            position: relative;
            border-left: 4px solid var(--grey-300);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .notification-item.expired {
            border-left-color: var(--grey-500);
        }

        .notification-item.critical {
            border-left-color: var(--alert-red);
        }

        .notification-item.urgent {
            border-left-color: var(--alert-orange);
        }

        .notification-item.preventive {
            border-left-color: var(--alert-yellow);
        }

        .notification-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-full);
            font-size: 0.7rem;
            font-weight: 600;
        }

        .notification-badge.expired {
            background-color: rgba(108, 117, 125, 0.1);
            color: var(--grey-700);
        }

        .notification-badge.critical {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--alert-red);
        }

        .notification-badge.urgent {
            background-color: rgba(253, 126, 20, 0.1);
            color: var(--alert-orange);
        }

        .notification-badge.preventive {
            background-color: rgba(255, 193, 7, 0.1);
            color: #856404;
        }

        .notification-content {
            flex: 1;
            padding-right: 1rem;
        }

        .notification-icon {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }

        .notification-icon.expired {
            color: var(--grey-500);
        }

        .notification-icon.critical {
            color: var(--alert-red);
        }

        .notification-icon.urgent {
            color: var(--alert-orange);
        }

        .notification-icon.preventive {
            color: var(--alert-yellow);
        }

        .notification-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--grey-800);
            margin-bottom: 0.25rem;
        }

        .notification-text {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: var(--grey-500);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--grey-300);
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--grey-700);
            margin-bottom: 0.5rem;
        }

        .empty-description {
            color: var(--grey-500);
            max-width: 400px;
            margin: 0 auto;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem;
            gap: 0.25rem;
        }

        .pagination-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--grey-700);
            background-color: var(--grey-100);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .pagination-item:hover {
            background-color: var(--grey-200);
        }

        .pagination-item.active {
            background-color: var(--primary);
            color: var(--light);
        }

        .pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .filter-actions .btn {
                width: 100%;
            }
            
            .notifications-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="alertes-garanties-container">
        <div class="main-container">
            <div class="title">
                <i class='bx bx-bell'></i>
                Alertes des Garanties de soumission
            </div>
            
            <div class="page">
                <!-- Statistiques des alertes -->
                <div class="stats-container">
                    <div class="stat-card total">
                        <div class="stat-title">Total des alertes</div>
                        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                    </div>
                    <div class="stat-card critical">
                        <div class="stat-title">Alertes critiques</div>
                        <div class="stat-value"><?= $stats['critical'] ?? 0 ?></div>
                    </div>
                    <div class="stat-card urgent">
                        <div class="stat-title">Alertes urgentes</div>
                        <div class="stat-value"><?= $stats['urgent'] ?? 0 ?></div>
                    </div>
                    <div class="stat-card preventive">
                        <div class="stat-title">Alertes préventives</div>
                        <div class="stat-value"><?= $stats['preventive'] ?? 0 ?></div>
                    </div>
                    <div class="stat-card expired">
                        <div class="stat-title">Alertes expirées</div>
                        <div class="stat-value"><?= $stats['expired'] ?? 0 ?></div>
                    </div>
                </div>
                
                <!-- Filtres -->
                <div class="filter-section">
                    <div class="filter-title">
                        <i class='bx bx-filter-alt'></i>
                        Filtrer les alertes
                    </div>
                    <form action="" method="GET" class="filter-form">
                        <div class="form-group">
                            <label for="periode" class="form-label">Période</label>
                            <select id="periode" name="periode" class="form-control">
                                <option value="" <?= $periodeFiltre === '' ? 'selected' : '' ?>>Toutes les périodes</option>
                                <option value="today" <?= $periodeFiltre === 'today' ? 'selected' : '' ?>>Aujourd'hui</option>
                                <option value="this_week" <?= $periodeFiltre === 'this_week' ? 'selected' : '' ?>>Cette semaine</option>
                                <option value="next_week" <?= $periodeFiltre === 'next_week' ? 'selected' : '' ?>>Semaine prochaine</option>
                                <option value="this_month" <?= $periodeFiltre === 'this_month' ? 'selected' : '' ?>>Ce mois-ci</option>
                                <option value="next_month" <?= $periodeFiltre === 'next_month' ? 'selected' : '' ?>>Mois prochain</option>
                                <option value="expired" <?= $periodeFiltre === 'expired' ? 'selected' : '' ?>>Expirées</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="type_alerte" class="form-label">Type d'alerte</label>
                            <select id="type_alerte" name="type_alerte" class="form-control">
                                <option value="" <?= $typeAlerte === '' ? 'selected' : '' ?>>Tous les types</option>
                                <option value="expired" <?= $typeAlerte === 'expired' ? 'selected' : '' ?>>Expirée</option>
                                <option value="critical" <?= $typeAlerte === 'critical' ? 'selected' : '' ?>>Critique</option>
                                <option value="urgent" <?= $typeAlerte === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                <option value="preventive" <?= $typeAlerte === 'preventive' ? 'selected' : '' ?>>Préventif</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="direction" class="form-label">Direction</label>
                            <select id="direction" name="direction" class="form-control">
                                <option value="">Toutes les directions</option>
                                <?php foreach ($directions as $direction): ?>
                                <option value="<?= $direction['id'] ?>" <?= $directionFiltre == $direction['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($direction['libelle']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-search'></i>
                                Filtrer
                            </button>
                            <a href="alertes_garanties.php" class="btn btn-secondary">
                                <i class='bx bx-reset'></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Liste des notifications -->
                <div class="notifications-container">
                    <div class="notifications-header">
                        <div class="notifications-title">
                            <?= count($garanties) ?> alerte(s) trouvée(s) sur <?= $totalItems ?>
                        </div>
                    </div>
                    
                    <?php if (empty($garanties)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class='bx bx-bell-off'></i>
                        </div>
                        <div class="empty-title">Aucune alerte trouvée</div>
                        <div class="empty-description">
                            Aucune garantie n'arrive à échéance prochainement ou ne correspond à vos critères de recherche.
                        </div>
                    </div>
                    <?php else: ?>
                        <?php foreach ($garanties as $garantie): ?>
                            <?php
                            // Déterminer le type d'alerte selon les nouveaux critères
                            $joursRestants = $garantie['jours_restants'];
                            $alerteClass = '';
                            $alerteIcon = '';
                            $alerteTitle = '';
                            
                            if ($joursRestants < 0) {
                                $alerteClass = 'expired';
                                $alerteIcon = 'bx-time-five';
                                $alerteTitle = 'Expirée';
                            } elseif ($joursRestants <= 1) {
                                $alerteClass = 'critical';
                                $alerteIcon = 'bx-error-circle';
                                $alerteTitle = 'Alerte critique';
                            } elseif ($joursRestants >= 3 && $joursRestants <= 5) {
                                $alerteClass = 'urgent';
                                $alerteIcon = 'bx-alarm-exclamation';
                                $alerteTitle = 'Alerte urgente';
                            } elseif ($joursRestants >= 8 && $joursRestants <= 21) {
                                $alerteClass = 'preventive';
                                $alerteIcon = 'bx-bell';
                                $alerteTitle = 'Alerte préventive';
                            } else {
                                $alerteClass = 'preventive';
                                $alerteIcon = 'bx-bell';
                                $alerteTitle = 'Information';
                            }
                            
                            // Formater les dates
                            $dateValidite = new DateTime($garantie['date_validite']);
                            $dateValiditeFormatted = $dateValidite->format('d/m/Y');
                            ?>
                            <div class="notification-item <?= $alerteClass ?>">
                                <div class="notification-badge <?= $alerteClass ?>">
                                    <?= $alerteTitle ?>
                                </div>
                                
                                <div class="notification-content">
                                    <div class="notification-title">
                                        <i class='bx <?= $alerteIcon ?> notification-icon <?= $alerteClass ?>'></i>
                                        Garantie <?= htmlspecialchars($garantie['num_garantie']) ?>
                                    </div>
                                    <div class="notification-text">
                                        <?php if ($joursRestants < 0): ?>
                                            <strong>La garantie de <?= htmlspecialchars($garantie['structure'] ?? 'N/A') ?> a expiré</strong> depuis <?= abs($joursRestants) ?> jour(s) avec <?= htmlspecialchars($garantie['nom_fournisseur'] ?? 'N/A') ?>.
                                        <?php else: ?>
                                            <strong>La garantie de <?= htmlspecialchars($garantie['structure'] ?? 'N/A') ?> expire dans <?= $joursRestants ?> jour(s)</strong> (<?= $dateValiditeFormatted ?>) avec <?= htmlspecialchars($garantie['nom_fournisseur'] ?? 'N/A') ?>.
                                        <?php endif; ?>
                                    </div>
                                    <div class="notification-meta">
                                        <div>Créée le: <?= date('d/m/Y', strtotime($garantie['date_creation'])) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php
                        // Construire les paramètres d'URL pour la pagination
                        $queryParams = $_GET;
                        unset($queryParams['page']); // Supprimer le paramètre page existant
                        $queryString = http_build_query($queryParams);
                        $queryString = $queryString ? '&' . $queryString : '';
                        ?>
                        
                        <!-- Bouton précédent -->
                        <a href="<?= $page > 1 ? '?page=' . ($page - 1) . $queryString : '#' ?>" 
                           class="pagination-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <i class='bx bx-chevron-left'></i>
                        </a>
                        
                        <!-- Pages -->
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        
                        if ($endPage - $startPage < 4 && $startPage > 1) {
                            $startPage = max(1, $endPage - 4);
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <a href="?page=<?= $i . $queryString ?>" 
                               class="pagination-item <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <!-- Bouton suivant -->
                        <a href="<?= $page < $totalPages ? '?page=' . ($page + 1) . $queryString : '#' ?>" 
                           class="pagination-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <i class='bx bx-chevron-right'></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des cartes de statistiques
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
            
            // Animation des notifications
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 100);
                }, 500 + (index * 100)); // Commencer après l'animation des cartes de statistiques
            });
        });
    </script>
</body>
</html>

<?php require_once("../Template/footer.php"); ?>
