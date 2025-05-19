<?php
// Vérifier si la session est déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté et a le rôle responsable
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'responsable') {
    header("Location: ../Login/login.php");
    exit;
}

require_once("../Template/header.php");

// Récupérer les statistiques (à adapter selon votre base de données)
require_once("../../db_connection/db_conn.php");

// Nombre total de garanties
$stmt = $pdo->query("SELECT COUNT(*) as total FROM garantie");
$totalGaranties = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Nombre de garanties libérées
$stmt = $pdo->query("SELECT COUNT(*) as total FROM garantie g 
                     INNER JOIN liberation l ON g.id = l.garantie_id");
$garantiesLiberees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Nombre de garanties en cours
$garantiesEnCours = $totalGaranties - $garantiesLiberees;

// Récupérer les dernières garanties (5 dernières)
$stmt = $pdo->query("SELECT g.*, b.label as nom_banque FROM garantie g 
                    LEFT JOIN agence a ON g.agence_id = a.id
                    LEFT JOIN banque b ON a.banque_id = b.id
                    ORDER BY g.date_creation DESC LIMIT 5");
$dernieresGaranties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Responsable</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --primary: #1775f1;
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

        .dashboard-container {
            padding: 1.5rem;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-header {
            margin-bottom: 1.5rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .dashboard-title i {
            margin-right: 0.75rem;
            font-size: 1.75rem;
            color: var(--primary);
        }

        .dashboard-subtitle {
            font-size: 0.95rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
        }

        .dashboard-welcome {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .dashboard-welcome::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 60%);
            z-index: 0;
            animation: pulse 15s infinite linear;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.5;
            }
            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-message {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.25rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            display: flex;
            align-items: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-lg);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .stat-icon.blue {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }

        .stat-icon.green {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .stat-icon.orange {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--gray-600);
        }

        .actions-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            overflow: hidden;
            cursor: pointer;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-lg);
        }

        .action-card:hover .action-icon {
            transform: scale(1.1);
        }

        .action-header {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            padding: 1.25rem;
            color: white;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .action-header.alt {
            background: linear-gradient(135deg, var(--secondary), var(--gray-700));
        }

        .action-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
            transition: transform 0.3s ease;
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .action-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .action-body {
            padding: 1.25rem;
            flex: 1;
        }

        .action-description {
            font-size: 0.95rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .action-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
            background-color: var(--primary);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-action:hover {
            background-color: var(--primary-hover);
        }

        .btn-action i {
            margin-left: 0.5rem;
        }

        .recent-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .recent-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .recent-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
        }

        .recent-title i {
            margin-right: 0.75rem;
            color: var(--primary);
        }

        .recent-body {
            padding: 0;
        }

        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-table th {
            background-color: var(--gray-50);
            padding: 0.75rem 1.25rem;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
        }

        .recent-table td {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
        }

        .recent-table tr:last-child td {
            border-bottom: none;
        }

        .recent-table tr:hover td {
            background-color: var(--gray-50);
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status.active {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status.pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .status.released {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .recent-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--gray-200);
            text-align: center;
        }

        .btn-view-all {
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--primary);
            background: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-view-all:hover {
            color: var(--primary-hover);
        }

        .btn-view-all i {
            margin-left: 0.5rem;
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .actions-container {
                grid-template-columns: 1fr;
            }
            
            .recent-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">

        </div>

        <div class="dashboard-welcome">
            <div class="welcome-content">
                <h2 class="welcome-title">Bonjour, <?php echo htmlspecialchars($_SESSION['prenom_user'] . ' ' . $_SESSION['nom_user']); ?> !</h2>
                <p class="welcome-message">Vous êtes connecté en tant que responsable. Vous pouvez gérer les garanties et les libérations depuis ce tableau de bord.</p>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class='bx bx-file'></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($totalGaranties); ?></div>
                    <div class="stat-label">Garanties totales</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class='bx bx-check-circle'></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($garantiesLiberees); ?></div>
                    <div class="stat-label">Garanties libérées</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class='bx bx-time'></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($garantiesEnCours); ?></div>
                    <div class="stat-label">Garanties en cours</div>
                </div>
            </div>
        </div>

        <div class="actions-container">
            <div class="action-card" onclick="window.location.href='../Recherche/recherche_details.php'">
                <div class="action-header">
                    <div class="action-icon">
                        <i class='bx bx-search-alt'></i>
                    </div>
                    <div>
                        <h3 class="action-title">Rechercher une garantie</h3>
                        <p class="action-subtitle">Accès rapide à la recherche</p>
                    </div>
                </div>
                <div class="action-body">
                    <p class="action-description">
                        Recherchez rapidement une garantie par numéro, bénéficiaire, montant ou autres critères. Accédez aux détails complets et générez des rapports.
                    </p>
                </div>
                <div class="action-footer">
                    <button class="btn-action">
                        Accéder <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>
            
            <div class="action-card" onclick="window.location.href='recherche_liberation.php'">
                <div class="action-header alt">
                    <div class="action-icon">
                        <i class='bx bx-calendar-check'></i>
                    </div>
                    <div>
                        <h3 class="action-title">Rechercher des libérations</h3>
                        <p class="action-subtitle">Entre deux dates</p>
                    </div>
                </div>
                <div class="action-body">
                    <p class="action-description">
                        Consultez les garanties libérées dans une période spécifique. Filtrez par date de libération et générez des rapports détaillés pour vos analyses.
                    </p>
                </div>
                <div class="action-footer">
                    <button class="btn-action">
                        Accéder <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="recent-container">
            <div class="recent-header">
                <h3 class="recent-title">
                    <i class='bx bx-history'></i> Garanties récentes
                </h3>
            </div>
            <div class="recent-body">
                <table class="recent-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Banque</th>
                            <th>Montant</th>
                            <th>Date création</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dernieresGaranties)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Aucune garantie trouvée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dernieresGaranties as $garantie): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($garantie['num_garantie'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($garantie['nom_banque'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($garantie['montant'] ?? 0, 2, ',', ' '); ?> DZD</td>
                                    <td><?php echo date('d/m/Y', strtotime($garantie['date_creation'])); ?></td>
                                    <td>
                                        <?php 
                                        // Vérifier si la garantie a une libération
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM liberation WHERE garantie_id = ?");
                                        $stmt->execute([$garantie['id']]);
                                        $hasLiberation = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
                                        
                                        if ($hasLiberation): ?>
                                            <span class="status released">Libérée</span>
                                        <?php else: ?>
                                            <span class="status active">En cours</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="recent-footer">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des cartes statistiques
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });

            // Animation des cartes d'action
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 400 + (index * 100));
            });
        });
    </script>
</body>
</html>

<?php
require_once("../Template/footer.php");
?>
