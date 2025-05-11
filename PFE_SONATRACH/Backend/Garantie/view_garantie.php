<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once('../../db_connection/db_conn.php');

// Vérifier si l'ID est fourni
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "ID de garantie non spécifié.";
    header('Location: ../../Pages/Garantie/ListeGaranties.php');
    exit;
}

// Récupérer l'ID de la garantie
$garantie_id = intval($_GET['id']);

try {
    // Récupérer les informations détaillées de la garantie avec les jointures
    $sql = "SELECT 
        g.*,
        d.libelle AS direction_libelle,
        f.nom_fournisseur,
        m.symbole AS monnaie_symbole, m.label AS monnaie_label,
        a.label AS agence_label, a.adresse AS agence_adresse,
        b.label AS banque_label,
        ao.num_appel_offre,
        dg.id AS document_id, dg.nom_document, dg.document_path,
        l.id AS liberation_id, l.date_liberation, l.Type_liberation_id, l.num AS liberation_num,
        tl.label AS type_liberation_label
    FROM garantie g
    LEFT JOIN direction d ON g.direction_id = d.id
    LEFT JOIN fournisseur f ON g.fournisseur_id = f.id
    LEFT JOIN monnaie m ON g.monnaie_id = m.id
    LEFT JOIN agence a ON g.agence_id = a.id
    LEFT JOIN banque b ON a.banque_id = b.id
    LEFT JOIN appel_offre ao ON g.appel_offre_id = ao.id
    LEFT JOIN document_garantie dg ON g.id = dg.garantie_id
    LEFT JOIN liberation l ON g.id = l.garantie_id
    LEFT JOIN Type_liberation tl ON l.Type_liberation_id = tl.id
    WHERE g.id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $garantie_id);
    $stmt->execute();
    
    $garantie = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$garantie) {
        $_SESSION['error_message'] = "Garantie non trouvée.";
        header('Location: ../../Pages/Garantie/ListeGaranties.php');
        exit;
    }

    // Récupérer les amendements liés à cette garantie
    $sql_amendements = "SELECT a.*, ta.label AS type_amendement_label 
                        FROM Amandement a
                        LEFT JOIN Type_amd ta ON a.Type_amd_id = ta.id
                        WHERE a.garantie_id = :garantie_id 
                        ORDER BY a.date_sys DESC";
    $stmt_amendements = $pdo->prepare($sql_amendements);
    $stmt_amendements->bindParam(':garantie_id', $garantie_id);
    $stmt_amendements->execute();
    $amendements = $stmt_amendements->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les authentifications liées à cette garantie
    $sql_auth = "SELECT * FROM authentification WHERE garantie_id = :garantie_id ORDER BY date_auth DESC";
    $stmt_auth = $pdo->prepare($sql_auth);
    $stmt_auth->bindParam(':garantie_id', $garantie_id);
    $stmt_auth->execute();
    $authentifications = $stmt_auth->fetchAll(PDO::FETCH_ASSOC);
    
  
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la récupération des données: " . $e->getMessage();
    header('Location: ../../Pages/Garantie/ListeGaranties.php');
    exit;
}

// Déterminer le statut de la garantie
$today = new DateTime();
$validite = new DateTime($garantie['date_validite']);

if (!empty($garantie['liberation_id'])) {
    $status = 'Libérée';
    $statusClass = 'liberated';
    $statusIcon = 'bx-lock-open';
} else {
    if ($validite < $today) {
        $status = 'Expirée';
        $statusClass = 'expired';
        $statusIcon = 'bx-time';
    } else {
        $status = 'Valide';
        $statusClass = 'valid';
        $statusIcon = 'bx-check-circle';
    }
}

// Maintenant que toutes les vérifications et redirections sont terminées, on peut inclure le header
require_once("../../Pages/Template/header.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Garantie - <?php echo htmlspecialchars($garantie['num_garantie']); ?></title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
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
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .page-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-light);
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-header h1 i {
            margin-right: 0.75rem;
            font-size: 1.75rem;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--box-shadow-lg);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--gray-50);
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .card-header h2 i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35em 0.75em;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 50rem;
            margin-left: 1rem;
        }

        .status-badge.valid {
            background-color: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        .status-badge.expired {
            background-color: rgba(239, 68, 68, 0.15);
            color: #b91c1c;
        }

        .status-badge.liberated {
            background-color: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .status-badge i {
            margin-right: 0.35rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .info-group {
            margin-bottom: 1.25rem;
        }

        .info-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        .info-value {
            display: block;
            padding: 0.75rem 1rem;
            background-color: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            color: var(--gray-800);
        }

        .info-value.highlight {
            font-weight: 600;
            color: var(--primary);
            background-color: rgba(59, 130, 246, 0.05);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            border: 1px solid transparent;
            border-radius: var(--border-radius);
            transition: var(--transition);
            text-decoration: none;
        }

        .btn i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .btn-primary {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
        }

        .btn-secondary {
            color: #fff;
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-secondary:hover {
            background-color: #4b5563;
            border-color: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(100, 116, 139, 0.25);
        }

        .btn-success {
            color: #fff;
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.25);
        }

        .btn-danger {
            color: #fff;
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.25);
        }

        .btn-warning {
            color: #fff;
            background-color: var(--warning);
            border-color: var(--warning);
        }

        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.25);
        }

        .btn-outline-primary {
            color: var(--primary);
            background-color: transparent;
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
        }

        .document-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background-color: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            transition: var(--transition);
            margin-bottom: 0.75rem;
        }

        .document-card:hover {
            background-color: rgba(59, 130, 246, 0.05);
            border-color: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .document-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 1rem;
        }

        .document-info {
            flex: 1;
        }

        .document-name {
            font-weight: 500;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .document-actions {
            display: flex;
            gap: 0.5rem;
        }

        .document-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: white;
            border: 1px solid var(--gray-200);
            color: var(--gray-600);
            transition: var(--transition);
        }

        .document-btn:hover {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
        }

        .document-btn i {
            font-size: 1.25rem;
        }

        .no-document {
            padding: 1.5rem;
            text-align: center;
            background-color: var(--gray-50);
            border: 1px dashed var(--gray-300);
            border-radius: var(--border-radius);
            color: var(--gray-500);
        }

        .no-document i {
            font-size: 2.5rem;
            color: var(--gray-400);
            margin-bottom: 0.75rem;
            display: block;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1.5rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0.5rem;
            width: 2px;
            background-color: var(--gray-200);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            top: 0.25rem;
            left: -1.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background-color: var(--primary-light);
            border: 2px solid white;
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .timeline-date {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .timeline-content {
            background-color: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 1rem;
        }

        .timeline-title {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .timeline-description {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            background-color: var(--gray-50);
            border: 1px dashed var(--gray-300);
            border-radius: var(--border-radius);
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray-400);
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state-title {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .empty-state-description {
            color: var(--gray-500);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header .action-buttons {
                margin-top: 1rem;
                width: 100%;
            }

            .status-badge {
                margin-left: 0;
                margin-top: 0.5rem;
            }
        }

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
        #ret{
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <div class="page-header">
            <h1><i class='bx bx-shield'></i> Détails de la Garantie</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2>
                        <i class='bx bx-detail'></i>
                        Garantie #<?php echo htmlspecialchars($garantie['num_garantie']); ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <i class='bx <?php echo $statusIcon; ?>'></i>
                            <?php echo $status; ?>
                        </span>
                    </h2>
                </div>
                <div class="action-buttons" >
                    <a href="../../Pages/Garantie/ListeGaranties.php" class="btn btn-secondary" id="ret">
                        <i class='bx bx-arrow-back '></i> Retour
                    </a>
                   
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div>
                        <div class="info-group">
                            <span class="info-label">Numéro de Garantie</span>
                            <span class="info-value highlight"><?php echo htmlspecialchars($garantie['num_garantie']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Montant</span>
                            <span class="info-value"><?php echo number_format($garantie['montant'], 2, ',', ' ') . ' ' . htmlspecialchars($garantie['monnaie_symbole']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Monnaie</span>
                            <span class="info-value"><?php echo htmlspecialchars($garantie['monnaie_symbole'] . ' - ' . $garantie['monnaie_label']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Banque</span>
                            <span class="info-value"><?php echo htmlspecialchars($garantie['banque_label']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Agence</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($garantie['agence_label']); ?>
                                <?php if (!empty($garantie['agence_adresse'])): ?>
                                <br><small><?php echo htmlspecialchars($garantie['agence_adresse']); ?></small>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="info-group">
                            <span class="info-label">Direction</span>
                            <span class="info-value"><?php echo htmlspecialchars($garantie['direction_libelle']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Fournisseur</span>
                            <span class="info-value"><?php echo htmlspecialchars($garantie['nom_fournisseur']); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Date de Création</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_creation'])); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Date d'Émission</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_emission'])); ?></span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Date de Validité</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_validite'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="section-title">
                    <i class='bx bx-file'></i> Document associé de La Garantie 
                </div>

                <?php if (isset($garantie['document_id']) && !empty($garantie['document_id'])): ?>
                <div class="document-card">
                    <i class='bx bxs-file-pdf document-icon'></i>
                    <div class="document-info">
                        <div class="document-name"><?php echo htmlspecialchars($garantie['nom_document']); ?></div>
                        <small>Document PDF</small>
                    </div>
                    <div class="document-actions">
                        <a href="view_document.php?id=<?php echo $garantie['document_id']; ?>" target="_blank" class="document-btn" title="Voir le document">
                            <i class='bx bx-show'></i>
                        </a>
                      
                    </div>
                </div>
                <?php else: ?>
                <div class="no-document">
                    <i class='bx bx-file'></i>
                    <p>Aucun document n'est associé à cette garantie.</p>
                </div>
                <?php endif; ?>

                <?php if (!empty($garantie['liberation_id'])): ?>
                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-lock-open'></i> Informations de libération
                </div>
                <div class="info-grid">
                    <div class="info-group">
                        <span class="info-label">Numéro de libération</span>
                        <span class="info-value"><?php echo htmlspecialchars($garantie['liberation_num']); ?></span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Date de libération</span>
                        <span class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_liberation'])); ?></span>
                    </div>
                    <?php if (!empty($garantie['Type_liberation_id']) && !empty($garantie['type_liberation_label'])): ?>
                    <div class="info-group">
                        <span class="info-label">Type de libération</span>
                        <span class="info-value"><?php echo htmlspecialchars($garantie['type_liberation_label']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($documents_liberation)): ?>
                <div class="section-title" style="margin-top: 1rem;">
                    <i class='bx bx-file'></i> Documents de libération
                </div>
                <?php foreach ($documents_liberation as $doc): ?>
                <div class="document-card">
                    <i class='bx bxs-file-pdf document-icon'></i>
                    <div class="document-info">
                        <div class="document-name"><?php echo htmlspecialchars($doc['nom_document']); ?></div>
                        <small>Document de libération</small>
                    </div>
                    <div class="document-actions">
                        <a href="view_document_liberation.php?id=<?php echo $doc['id']; ?>" target="_blank" class="document-btn" title="Voir le document">
                            <i class='bx bx-show'></i>
                        </a>
                        <a href="download_document_liberation.php?id=<?php echo $doc['id']; ?>" class="document-btn" title="Télécharger le document">
                            <i class='bx bx-download'></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($amendements)): ?>
                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-edit-alt'></i> Historique des amendements
                </div>
                <div class="timeline">
                    <?php foreach ($amendements as $amendement): ?>
                    <div class="timeline-item">
                        <div class="timeline-date"><?php echo date('d/m/Y', strtotime($amendement['date_sys'])); ?></div>
                        <div class="timeline-content">
                            <div class="timeline-title">
                                Amendement #<?php echo htmlspecialchars($amendement['num_amd']); ?>
                                <?php if (!empty($amendement['type_amendement_label'])): ?>
                                <small>(<?php echo htmlspecialchars($amendement['type_amendement_label']); ?>)</small>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-description">
                                <?php if (!empty($amendement['montant_amd'])): ?>
                                <p>Montant: <?php echo number_format($amendement['montant_amd'], 2, ',', ' '); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($amendement['date_prorogation'])): ?>
                                <p>Date de prorogation: <?php echo date('d/m/Y', strtotime($amendement['date_prorogation'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($authentifications)): ?>
                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-check-shield'></i> Historique des authentifications
                </div>
                <div class="timeline">
                    <?php foreach ($authentifications as $auth): ?>
                    <div class="timeline-item">
                        <div class="timeline-date"><?php echo date('d/m/Y', strtotime($auth['date_auth'])); ?></div>
                        <div class="timeline-content">
                            <div class="timeline-title">Authentification #<?php echo htmlspecialchars($auth['num_auth']); ?></div>
                            <div class="timeline-description">
                                <p>Date de dépôt: <?php echo date('d/m/Y', strtotime($auth['date_depo'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (empty($amendements) && empty($authentifications) && empty($garantie['liberation_id'])): ?>
                <div class="empty-state" style="margin-top: 2rem;">
                    <i class='bx bx-info-circle'></i>
                    <div class="empty-state-title">Aucune action supplémentaire</div>
                    <div class="empty-state-description">Cette garantie n'a pas encore d'amendements, d'authentifications ou de libération.</div>
                    <div class="action-buttons" style="justify-content: center;">
                        <a href="../../Pages/Amandements/Amandement.php?garantie_id=<?php echo $garantie_id; ?>" class="btn btn-outline-primary">
                            <i class='bx bx-edit-alt'></i> Ajouter un amendement
                        </a>
                        <a href="../../Pages/Authentification/Authentification.php?garantie_id=<?php echo $garantie_id; ?>" class="btn btn-outline-primary">
                            <i class='bx bx-check-shield'></i> Authentifier
                        </a>
                        <a href="../../Pages/Liberation/liberation.php?garantie_id=<?php echo $garantie_id; ?>" class="btn btn-outline-primary">
                            <i class='bx bx-lock-open'></i> Libérer
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once ('../../Pages/Template/footer.php');
?>
