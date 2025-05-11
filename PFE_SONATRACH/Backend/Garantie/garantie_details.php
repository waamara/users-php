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
        dg.id AS document_id, dg.nom_document, dg.document_path
    FROM garantie g
    LEFT JOIN direction d ON g.direction_id = d.id
    LEFT JOIN fournisseur f ON g.fournisseur_id = f.id
    LEFT JOIN monnaie m ON g.monnaie_id = m.id
    LEFT JOIN agence a ON g.agence_id = a.id
    LEFT JOIN banque b ON g.banque_id = b.id
    LEFT JOIN appel_offre ao ON g.appel_offre_id = ao.id
    LEFT JOIN document_garantie dg ON g.id = dg.garantie_id
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
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la récupération des données: " . $e->getMessage();
    header('Location: ../../Pages/Garantie/ListeGaranties.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Garantie - <?php echo htmlspecialchars($garantie['num_garantie']); ?></title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary: #0C5FCD;
            --primary-light: #3a7fd9;
            --primary-dark: #0A4DA3;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --grey-10: #f8f9fa;
            --grey-30: #dee2e6;
            --grey-50: #adb5bd;
            --grey-80: #495057;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .title {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .title i {
            margin-right: 10px;
            font-size: 28px;
        }
        
        .garantie-details {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .garantie-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--grey-30);
        }
        
        .garantie-title {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        
        .garantie-title i {
            margin-right: 10px;
        }
        
        .garantie-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        
        .garantie-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-group {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--grey-80);
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            padding: 8px 12px;
            background-color: var(--grey-10);
            border-radius: 4px;
            border: 1px solid var(--grey-30);
        }
        
        .document-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--grey-30);
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        
        .document-title i {
            margin-right: 8px;
        }
        
        .document-link {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background-color: var(--light);
            border: 1px solid var(--grey-30);
            border-radius: 4px;
            text-decoration: none;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .document-link:hover {
            background-color: var(--primary-light);
            color: white;
        }
        
        .document-link i {
            margin-right: 8px;
            font-size: 20px;
        }
        
        .no-document {
            color: var(--grey-50);
            font-style: italic;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .status-valid {
            background-color: rgba(40, 167, 69, 0.15);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .status-expired {
            background-color: rgba(220, 53, 69, 0.15);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .print-btn {
            margin-left: 10px;
            background-color: var(--info);
            color: white;
        }
        
        .print-btn:hover {
            background-color: #138496;
            transform: translateY(-2px);
        }
        
        @media print {
            body {
                background-color: white;
                color: black;
            }
            
            .garantie-actions, .btn {
                display: none !important;
            }
            
            .main-container {
                padding: 0;
                max-width: 100%;
            }
            
            .garantie-details {
                box-shadow: none;
                padding: 0;
            }
            
            .info-value {
                border: 1px solid #ddd;
            }
        }
        
        @media (max-width: 768px) {
            .garantie-content {
                grid-template-columns: 1fr;
            }
            
            .garantie-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .garantie-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="title">
            <i class='bx bx-shield'></i>
            Gestion des Garanties Bancaires
        </div>
        
        <div class="garantie-details">
            <div class="garantie-header">
                <div class="garantie-title">
                    <i class='bx bx-detail'></i>
                    Détails de la Garantie #<?php echo htmlspecialchars($garantie['num_garantie']); ?>
                </div>
                <div class="garantie-actions">
                    <a href="../../Pages/Garantie/ListeGaranties.php" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Retour
                    </a>
                    <a href="edit_garantie.php?id=<?php echo $garantie_id; ?>" class="btn btn-primary">
                        <i class='bx bx-edit'></i> Modifier
                    </a>
                    <?php if (isset($garantie['document_id']) && !empty($garantie['document_id'])): ?>
                    <a href="view_document.php?id=<?php echo $garantie['document_id']; ?>" target="_blank" class="btn btn-success">
                        <i class='bx bx-file'></i> Voir Document
                    </a>
                    <?php endif; ?>
                    <button onclick="window.print()" class="btn print-btn">
                        <i class='bx bx-printer'></i> Imprimer
                    </button>
                </div>
            </div>
            
            <div class="garantie-content">
                <div class="info-column">
                    <div class="info-group">
                        <span class="info-label">Numéro de Garantie</span>
                        <div class="info-value"><?php echo htmlspecialchars($garantie['num_garantie']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Montant</span>
                        <div class="info-value">
                            <?php echo number_format($garantie['montant'], 2, ',', ' '); ?> 
                            <?php echo htmlspecialchars(isset($garantie['monnaie_symbole']) ? $garantie['monnaie_symbole'] : ''); ?>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Monnaie</span>
                        <div class="info-value">
                            <?php echo htmlspecialchars(isset($garantie['monnaie_symbole']) ? $garantie['monnaie_symbole'] : ''); ?> - 
                            <?php echo htmlspecialchars(isset($garantie['monnaie_label']) ? $garantie['monnaie_label'] : ''); ?>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Banque</span>
                        <div class="info-value"><?php echo htmlspecialchars(isset($garantie['banque_label']) ? $garantie['banque_label'] : ''); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Direction</span>
                        <div class="info-value"><?php echo htmlspecialchars(isset($garantie['direction_libelle']) ? $garantie['direction_libelle'] : ''); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Agence</span>
                        <div class="info-value">
                            <?php echo htmlspecialchars(isset($garantie['agence_label']) ? $garantie['agence_label'] : ''); ?>
                            <?php if (isset($garantie['agence_adresse']) && !empty($garantie['agence_adresse'])): ?>
                                <br><small><?php echo htmlspecialchars($garantie['agence_adresse']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="info-column">
                    <div class="info-group">
                        <span class="info-label">Fournisseur</span>
                        <div class="info-value"><?php echo htmlspecialchars(isset($garantie['nom_fournisseur']) ? $garantie['nom_fournisseur'] : ''); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Date de Création</span>
                        <div class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_creation'])); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Date d'Émission</span>
                        <div class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_emission'])); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Date de Validité</span>
                        <div class="info-value"><?php echo date('d/m/Y', strtotime($garantie['date_validite'])); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Appel d'Offre</span>
                        <div class="info-value"><?php echo htmlspecialchars(isset($garantie['num_appel_offre']) ? $garantie['num_appel_offre'] : ''); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Statut</span>
                        <div class="info-value">
                            <?php 
                            $today = new DateTime();
                            $validite = new DateTime($garantie['date_validite']);
                            if ($validite < $today) {
                                echo '<span class="status-badge status-expired">Expirée</span>';
                            } else {
                                echo '<span class="status-badge status-valid">Valide</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="document-section">
                <div class="document-title">
                    <i class='bx bx-file-blank'></i>
                    Document associé
                </div>
                
                <?php if (isset($garantie['document_id']) && !empty($garantie['document_id'])): ?>
                <a href="view_document.php?id=<?php echo $garantie['document_id']; ?>" target="_blank" class="document-link">
                    <i class='bx bx-file-pdf'></i>
                    <?php echo htmlspecialchars($garantie['nom_document']); ?>
                </a>
                <?php else: ?>
                <p class="no-document">Aucun document associé à cette garantie.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Afficher un message de succès si présent dans l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const successMsg = urlParams.get('success');
            
            if (successMsg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: decodeURIComponent(successMsg),
                    confirmButtonColor: '#0C5FCD'
                });
            }
        });
    </script>
</body>
</html>

