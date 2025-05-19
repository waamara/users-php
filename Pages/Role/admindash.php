<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../Login/login.php");
    exit;
}
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Récupération des statistiques globales
try {
    // 1. Nombre total de garanties
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM garantie");
    $stmt->execute();
    $totalGaranties = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Montant total des garanties
    $stmt = $pdo->prepare("SELECT SUM(montant) as total_montant FROM garantie");
    $stmt->execute();
    $totalMontant = $stmt->fetch(PDO::FETCH_ASSOC)['total_montant'];

    // 3. Nombre de garanties par statut
    $today = date('Y-m-d');
    
    // Garanties valides (non expirées et non libérées)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM garantie g
        LEFT JOIN liberation l ON g.id = l.garantie_id
        WHERE g.date_validite >= :today AND l.id IS NULL
    ");
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $validCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Garanties expirées (date de validité dépassée et non libérées)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM garantie g
        LEFT JOIN liberation l ON g.id = l.garantie_id
        WHERE g.date_validite < :today AND l.id IS NULL
    ");
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $expiredCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Garanties libérées
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM garantie g
        JOIN liberation l ON g.id = l.garantie_id
    ");
    $stmt->execute();
    $liberatedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // 4. Garanties par direction
    $stmt = $pdo->prepare("
        SELECT d.libelle, COUNT(g.id) as count
        FROM garantie g
        JOIN direction d ON g.direction_id = d.id
        GROUP BY d.id
        ORDER BY count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $garantiesParDirection = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Garanties par banque
    $stmt = $pdo->prepare("
        SELECT b.label, COUNT(g.id) as count
        FROM garantie g
        JOIN agence a ON g.agence_id = a.id
        JOIN banque b ON a.banque_id = b.id
        GROUP BY b.id
        ORDER BY count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $garantiesParBanque = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Garanties par mois (12 derniers mois)
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(date_creation, '%Y-%m') as mois,
            COUNT(*) as count,
            SUM(montant) as montant_total
        FROM garantie
        WHERE date_creation >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
        ORDER BY mois ASC
    ");
    $stmt->execute();
    $garantiesParMois = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 7. Garanties expirant bientôt (dans les 30 prochains jours)
    $expirationDate = date('Y-m-d', strtotime('+30 days'));
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as count
        FROM garantie g
        LEFT JOIN liberation l ON g.id = l.garantie_id
        WHERE g.date_validite BETWEEN CURRENT_DATE() AND :expiration_date
        AND l.id IS NULL
    ");
    $stmt->bindParam(':expiration_date', $expirationDate);
    $stmt->execute();
    $expiringCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // 8. Montant total par monnaie
    $stmt = $pdo->prepare("
        SELECT 
            m.label, m.symbole, SUM(g.montant) as total
        FROM garantie g
        JOIN monnaie m ON g.monnaie_id = m.id
        GROUP BY g.monnaie_id
        ORDER BY total DESC
    ");
    $stmt->execute();
    $montantParMonnaie = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Erreur de base de données: " . $e->getMessage();
}

// Préparer les données pour les graphiques
// Données pour le graphique des garanties par direction
$directionLabels = [];
$directionData = [];
foreach ($garantiesParDirection as $index => $item) {
    $directionLabels[] = $item['libelle'];
    $directionData[] = $item['count'];
}

// Données pour le graphique des garanties par banque
$banqueLabels = [];
$banqueData = [];
foreach ($garantiesParBanque as $index => $item) {
    $banqueLabels[] = $item['label'];
    $banqueData[] = $item['count'];
}

// Données pour le graphique des garanties par mois
$moisLabels = [];
$moisData = [];
$moisMontants = [];

// Initialiser les 12 derniers mois
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $moisLabels[] = date('M', strtotime($month));
    $moisData[$month] = 0;
    $moisMontants[$month] = 0;
}

// Remplir avec les données réelles
foreach ($garantiesParMois as $item) {
    $mois = $item['mois'];
    $moisData[$mois] = (int)$item['count'];
    $moisMontants[$mois] = (float)$item['montant_total'];
}

// Convertir en tableaux pour Chart.js
$moisDataArray = array_values($moisData);
$moisMontantsArray = array_values($moisMontants);

// Calculer les pourcentages pour le graphique des statuts
$totalStatuts = $validCount + $expiredCount + $liberatedCount;
$validPercent = $totalStatuts > 0 ? round(($validCount / $totalStatuts) * 100) : 0;
$expiredPercent = $totalStatuts > 0 ? round(($expiredCount / $totalStatuts) * 100) : 0;
$liberatedPercent = $totalStatuts > 0 ? round(($liberatedCount / $totalStatuts) * 100) : 0;

// Calculer les tendances (pour les indicateurs de croissance)
$croissanceGaranties = "+5.2%";
$croissanceMontant = "+3.8%";
$croissanceExpiration = "-2.1%";
$croissanceLiberation = "+7.4%";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Garanties</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #E6F0FF;
            --primary-dark: #1e40af;
            --secondary:rgb(26, 74, 147);
            --secondary-dark: #5A8DE6;
            --success: #10B981;
            --success-light: #D1FAE5;
            --danger: #EF4444;
            --danger-light: #FEE2E2;
            --warning: #F59E0B;
            --warning-light: #FEF3C7;
            --info: #38BDF8;
            --info-light: #E0F7FF;
            --dark: #111827;
            --dark-blue: #1e40af;
            --gray: #6B7280;
            --gray-light: #F9FAFB;
            --gray-lighter: #F3F4F6;
            --border-color: #E5E7EB;
            --card-bg: #FFFFFF;
            --body-bg: #F9FAFB;
            --text-color: #111827;
            --text-muted: #6B7280;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
            --chart-grid: rgba(107, 114, 128, 0.1);
            --positive: #10B981;
            --negative: #EF4444;
            --border-radius: 12px;
            --gradient-start: #4F9CF9;
            --gradient-middle: #1e40af;
            --gradient-end: #1e40af;
            --header-bg: #1e40af;
            --header-text: #FFFFFF;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-color);
            line-height: 1.5;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .dashboard-header {
            margin-bottom: 1.5rem;
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            background-image: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }

        .dashboard-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--header-text);
            margin-bottom: 0.25rem;
        }

        .dashboard-subtitle {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            
        }

        .stat-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }

        .stat-title {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.25rem;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .trend-up {
            color: var(--positive);
        }

        .trend-down {
            color: var(--negative);
        }

        .stat-badge {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-success {
            background-color: var(--success-light);
            color: var(--success);
        }

        .badge-danger {
            background-color: var(--danger-light);
            color: var(--danger);
        }

        .badge-info {
            background-color: var(--info-light);
            color: var(--info);
        }

        .badge-warning {
            background-color: var(--warning-light);
            color: var(--warning);
        }

        /* Chart Cards */
        .chart-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
          
        }

        .chart-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-light);
        }

        .chart-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-action {
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: none;
            color: var(--primary-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .chart-action:hover {
            background-color: rgba(45, 125, 210, 0.1);
            color: var(--primary-dark);
        }

        .chart-body {
            padding: 1.25rem;
            position: relative;
        }

        .chart-footer {
            padding: 1rem 1.25rem;
        
            
            background-color: var(--primary-light);
            font-size: 0.75rem;
            color: var(--primary-dark);
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
        }

        .legend-color {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
        }

        /* Revenue Card */
        .revenue-card {
            display: flex;
            flex-direction: column;
        }

        .revenue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .revenue-title {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .revenue-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .revenue-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--positive);
        }

        .revenue-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 0 1.25rem;
        }

        .revenue-tab {
            font-size: 0.75rem;
            color: var(--primary-dark);
            cursor: pointer;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid transparent;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }

        .revenue-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* Reports Section */
        .reports-section {
            margin-top: 2rem;
        }

        .reports-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            background-image: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }

        .reports-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--header-text);
        }

        .reports-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        /* Gauge Chart */
        .gauge-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
      
        }

        .gauge-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 1rem 0;
        }

        .gauge-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-color);
            text-align: center;
        }

        .gauge-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }

        /* Direction Distribution */
        .direction-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.25rem;
          
        }

        .direction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .direction-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .direction-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .direction-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .direction-item:last-child {
            border-bottom: none;
        }

        .direction-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .direction-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: white;
        }

        .direction-name {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .direction-count {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .direction-progress {
            flex: 1;
            height: 0.25rem;
            background-color: var(--gray-lighter);
            border-radius: 9999px;
            margin: 0 1rem;
            overflow: hidden;
        }

        .direction-progress-bar {
            height: 100%;
            border-radius: 9999px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .chart-row {
                grid-template-columns: 1fr;
            }
            
            .reports-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Bienvenue</h1>
            <p class="dashboard-subtitle">Gérez vos garanties bancaires en toute simplicité</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class='bx bx-error-circle'></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Cartes de statistiques -->
        <div class="stats-row">
            <div class="stat-card animate-fade-in delay-1">
                <div class="stat-title">Total Garanties</div>
                <div class="stat-value"><?php echo number_format($totalGaranties, 0, ',', ' '); ?></div>
                <div class="stat-trend trend-up">
                    <i class='bx bx-up-arrow-alt'></i> <?php echo $croissanceGaranties; ?>
                </div>
                <div class="stat-badge badge-success">ACTIF</div>
            </div>
            <div class="stat-card animate-fade-in delay-2">
                <div class="stat-title">Montant Total</div>
                <div class="stat-value"><?php echo number_format($totalMontant / 1000000, 1, ',', ' '); ?>M</div>
                <div class="stat-trend trend-up">
                    <i class='bx bx-up-arrow-alt'></i> <?php echo $croissanceMontant; ?>
                </div>
                <div class="stat-badge badge-info">FCFA</div>
            </div>
            <div class="stat-card animate-fade-in delay-3">
                <div class="stat-title">Expirant Bientôt</div>
                <div class="stat-value"><?php echo number_format($expiringCount, 0, ',', ' '); ?></div>
                <div class="stat-trend trend-down">
                    <i class='bx bx-down-arrow-alt'></i> <?php echo $croissanceExpiration; ?>
                </div>
                <div class="stat-badge badge-warning">30J</div>
            </div>
            <div class="stat-card animate-fade-in delay-4">
                <div class="stat-title">Garanties Libérées</div>
                <div class="stat-value"><?php echo number_format($liberatedCount, 0, ',', ' '); ?></div>
                <div class="stat-trend trend-up">
                    <i class='bx bx-up-arrow-alt'></i> <?php echo $croissanceLiberation; ?>
                </div>
                <div class="stat-badge badge-success">ACTIF</div>
            </div>
        </div>

        <!-- Graphiques principaux -->
        <div class="chart-row">
            <div class="chart-card animate-fade-in delay-1">
                <div class="chart-header">
                    <div class="revenue-header">
                        <div>
                            <div class="revenue-title">Total revenus</div>
                            <div class="revenue-value"><?php echo number_format($totalMontant, 0, ',', ' '); ?> FCFA</div>
                        </div>
                        <div class="revenue-trend">
                            <i class='bx bx-up-arrow-alt'></i> <?php echo $croissanceMontant; ?>
                        </div>
                    </div>
                    <div class="chart-actions">
                        <button class="chart-action" id="toggleChartView" title="Basculer entre nombre et montant">
                            <i class='bx bx-refresh'></i>
                        </button>
                    </div>
                </div>
                <div class="revenue-tabs">
                    <div class="revenue-tab active" data-tab="revenue">Revenus</div>
                    <div class="revenue-tab" data-tab="expenses">Dépenses</div>
                    <div class="revenue-tab" data-tab="profit">Profit</div>
                </div>
                <div class="chart-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>

            <div class="chart-card animate-fade-in delay-2">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class='bx bx-pie-chart-alt-2'></i> Répartition par statut
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #10b981;"></span>
                            <span>Valides: <?php echo $validCount; ?></span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #ef4444;"></span>
                            <span>Expirées: <?php echo $expiredCount; ?></span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #4F9CF9;"></span>
                            <span>Libérées: <?php echo $liberatedCount; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration des couleurs
            const colors = {
                primary: '#4F9CF9',
                primaryLight: '#E6F0FF',
                primaryDark: '#2D7DD2',
                secondary: '#6BA6FF',
                secondaryDark: '#5A8DE6',
                success: '#10B981',
                successLight: '#D1FAE5',
                danger: '#EF4444',
                dangerLight: '#FEE2E2',
                warning: '#F59E0B',
                warningLight: '#FEF3C7',
                info: '#38BDF8',
                infoLight: '#E0F7FF',
                light: '#FFFFFF',
                dark: '#111827',
                darkBlue: '#2563EB',
                grey: '#F9FAFB',
                greyDark: '#6B7280',
                chartGrid: 'rgba(107, 114, 128, 0.1)'
            };

            // Graphique des revenus
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            
            // Créer un dégradé pour l'arrière-plan
            const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
            revenueGradient.addColorStop(0, 'rgba(79, 156, 249, 0.3)');
            revenueGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            
            // Données pour le graphique des revenus
            const revenueData = {
                labels: <?php echo json_encode($moisLabels); ?>,
                datasets: [{
                    label: 'Montant total',
                    data: <?php echo json_encode($moisMontantsArray); ?>,
                    backgroundColor: revenueGradient,
                    borderColor: colors.primary,
                    borderWidth: 2,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true
                }]
            };
            
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: revenueData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: colors.chartGrid,
                                lineWidth: 1
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 10
                                },
                                color: colors.greyDark,
                                padding: 10,
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR') + ' FCFA';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 10
                                },
                                color: colors.greyDark,
                                padding: 10
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: colors.darkBlue,
                            titleFont: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 11
                            },
                            padding: 12,
                            cornerRadius: 8,
                            caretSize: 6,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Montant: ' + context.raw.toLocaleString('fr-FR') + ' FCFA';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    },
                    hover: {
                        mode: 'index',
                        intersect: false
                    },
                    elements: {
                        line: {
                            tension: 0.3
                        }
                    }
                }
            });

            // Données pour le nombre de garanties
            const countGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
            countGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            countGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            
            const countData = {
                labels: <?php echo json_encode($moisLabels); ?>,
                datasets: [{
                    label: 'Nombre de garanties',
                    data: <?php echo json_encode($moisDataArray); ?>,
                    backgroundColor: countGradient,
                    borderColor: colors.success,
                    borderWidth: 2,
                    pointBackgroundColor: colors.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true
                }]
            };

            // Bouton pour basculer entre les vues
            let showingAmount = true;
            document.getElementById('toggleChartView').addEventListener('click', function() {
                if (showingAmount) {
                    revenueChart.data = countData;
                    document.querySelector('.revenue-title').textContent = 'Nombre de garanties';
                    document.querySelector('.revenue-value').textContent = '<?php echo number_format($totalGaranties, 0, ',', ' '); ?>';
                    revenueChart.options.scales.y.ticks.callback = function(value) {
                        return value;
                    };
                    revenueChart.options.plugins.tooltip.callbacks.label = function(context) {
                        return 'Nombre: ' + context.raw;
                    };
                } else {
                    revenueChart.data = revenueData;
                    document.querySelector('.revenue-title').textContent = 'Total revenus';
                    document.querySelector('.revenue-value').textContent = '<?php echo number_format($totalMontant, 0, ',', ' '); ?> FCFA';
                    revenueChart.options.scales.y.ticks.callback = function(value) {
                        return value.toLocaleString('fr-FR') + ' FCFA';
                    };
                    revenueChart.options.plugins.tooltip.callbacks.label = function(context) {
                        return 'Montant: ' + context.raw.toLocaleString('fr-FR') + ' FCFA';
                    };
                }
                showingAmount = !showingAmount;
                revenueChart.update();
            });

            // Onglets de revenus
            const revenueTabs = document.querySelectorAll('.revenue-tab');
            revenueTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    revenueTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    // Ici vous pourriez changer les données du graphique en fonction de l'onglet
                });
            });

            // Graphique des statuts
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Valides', 'Expirées', 'Libérées'],
                    datasets: [{
                        data: [<?php echo $validCount; ?>, <?php echo $expiredCount; ?>, <?php echo $liberatedCount; ?>],
                        backgroundColor: [colors.success, colors.danger, colors.primary],
                        borderWidth: 0,
                        hoverOffset: 10,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: colors.darkBlue,
                            titleFont: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 11
                            },
                            padding: 12,
                            cornerRadius: 8,
                            caretSize: 6,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1500,
                        easing: 'easeOutQuart'
                    },
                    elements: {
                        arc: {
                            borderWidth: 0
                        }
                    }
                }
            });

            // Graphique de jauge
            const gaugeCtx = document.getElementById('gaugeChart').getContext('2d');
            
            // Créer un dégradé pour la jauge
            const gaugeGradient = gaugeCtx.createLinearGradient(0, 0, 200, 0);
            gaugeGradient.addColorStop(0, '#4F9CF9');
            gaugeGradient.addColorStop(0.5, '#6BA6FF');
            gaugeGradient.addColorStop(1, '#93C5FD');
            
            new Chart(gaugeCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [75, 25], // 75% de progression
                        backgroundColor: [
                            gaugeGradient,
                            colors.chartGrid
                        ],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
</body>

<?php
require_once ('../Template/footer.php');
?>
</html>