<?php
session_start(); // Start session at the beginning
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Récupérer l'ID de la libération
$liberation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($liberation_id <= 0) {
    // Rediriger vers la page de recherche si l'ID n'est pas valide
    header('Location: liberation-filter.php');
    exit;
}

// Requête pour récupérer les détails de la libération
$sql = "SELECT l.id, l.num, l.date_liberation, 
               g.id AS garantie_id, g.num_garantie, g.montant, g.date_emission, g.date_validite, 
               d.libelle AS direction, f.nom_fournisseur, m.label AS monnaie, 
               a.label AS agence, b.label AS banque_nom, ao.num_appel_offre,
               dl.nom_document, dl.document_path
        FROM liberation l
        JOIN garantie g ON l.garantie_id = g.id
        JOIN direction d ON g.direction_id = d.id
        JOIN fournisseur f ON g.fournisseur_id = f.id
        JOIN monnaie m ON g.monnaie_id = m.id
        JOIN agence a ON g.agence_id = a.id
        JOIN banque b ON a.banque_id = b.id
        JOIN appel_offre ao ON g.appel_offre_id = ao.id
        LEFT JOIN document_liberation dl ON l.id = dl.liberation_id
        WHERE l.id = :liberation_id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':liberation_id', $liberation_id, PDO::PARAM_INT);
    $stmt->execute();
    $liberation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$liberation) {
        // Rediriger si la libération n'existe pas
        header('Location: liberation-filter.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erreur de base de données: " . $e->getMessage());
}

// Helper function to format date
function formatDate($dateString) {
    if (empty($dateString)) return '';
    $date = new DateTime($dateString);
    return $date->format('d/m/Y');
}

// Helper function to format currency
function formatCurrency($amount, $currency = 'DA') {
    return number_format($amount, 2, ',', ' ') . ' ' . $currency;
}

// Check if user is coming from outside the system
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    // If not coming from our filter page, mark as left pages
    // if (strpos($referer, 'liberation-filter.php') === false) {
    //     sessionStorage.setItem('leftLiberationPages', 'true');
    // }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Libération <?= htmlspecialchars($liberation['num']) ?></title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
 
    <style>
        #dynamic-content .container { max-width: 1400px; margin: 0 auto; padding: 20px; width: 100%; }
        #dynamic-content .card { background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        #dynamic-content .main-title { font-size: 1.8rem; color: #007bff; font-weight: bold; margin-bottom: 30px; }
        #dynamic-content .card-subtitle { font-size: 1.2rem; font-weight: bold; color: #333; margin-bottom: 15px; }
        #dynamic-content .divider { border-top: 1px solid #ddd; margin: 15px 0 20px 0; }
        #dynamic-content .row { display: flex; flex-wrap: wrap; margin: 0 -10px; margin-bottom: 20px; }
        #dynamic-content .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 0 10px; }
        #dynamic-content .col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 0 10px; }
        #dynamic-content .detail-value { color: #555; }
        #dynamic-content a { color: #007bff; text-decoration: none; }
        #dynamic-content a:hover { text-decoration: underline; }
        
        /* Back button styles */
        .back-button-container { margin-bottom: 20px; }
        .btn-back { display: inline-flex; align-items: center; background-color: #f8f9fa; color: #495057; padding: 8px 15px; border-radius: 4px; text-decoration: none; border: 1px solid #dee2e6; }
        .btn-back i { margin-right: 5px; }
        .btn-back:hover { background-color: #e9ecef; color: #212529; text-decoration: none; }
        
        /* Document link styling */
        .document-link { display: inline-flex; align-items: center; color: #007bff; text-decoration: none; padding: 5px 10px; border-radius: 4px; background-color: #f8f9fa; }
        .document-link i { margin-right: 5px; }
        .document-link:hover { background-color: #e9ecef; text-decoration: underline; }
        
        @media (max-width: 768px) {
            #dynamic-content .col-md-4, #dynamic-content .col-md-6 { flex: 0 0 100%; max-width: 100%; }
            .btn-back { font-size: 0.9rem; padding: 6px 12px; }
        }
    </style>
</head>
<body>
    <main id="dynamic-content">
        <div class="container">
            <!-- Bouton de retour -->
            <div class="back-button-container">
                <a href="liberation-filter.php?from_details=true" class="btn-back">
                    <i class='bx bx-arrow-back'></i> Retour à la recherche
                </a>
            </div>
            
            <!-- Titre Principal -->
            <h2 class="main-title">
                <i class='bx bx-shield-quarter'></i> Détails de la Libération <?= htmlspecialchars($liberation['num']) ?>
            </h2>
            
            <!-- Détails de la Garantie -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-info-circle'></i> Détail de la garantie N° <?= htmlspecialchars($liberation['num_garantie']) ?>
                    </h5>
                    <hr class="divider">
                    <!-- Première ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-hash'></i> N° de garantie:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['num_garantie']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-building'></i> Structure:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['direction']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-user-pin'></i> Fournisseurs:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['nom_fournisseur']) ?></span></p>
                        </div>
                    </div>
                    <!-- Deuxième ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-file-blank'></i> Référence AO:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['num_appel_offre']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bxs-bank'></i> Banque:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['banque_nom']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <!-- Champ vide pour aligner -->
                        </div>
                    </div>
                    <!-- Troisième ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-money'></i> Montant:</strong> <span class="detail-value"><?= formatCurrency($liberation['montant'], $liberation['monnaie']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-buildings'></i> Agence:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['agence']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <!-- Intentionally left empty for alignment -->
                        </div>
                    </div>
                    <!-- Quatrième ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-calendar'></i> Date d'émission:</strong> <span class="detail-value"><?= formatDate($liberation['date_emission']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-calendar-exclamation'></i> Date d'expiration:</strong> <span class="detail-value"><?= formatDate($liberation['date_validite']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <!-- Intentionally left empty for alignment -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations de Libération -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-shield'></i> Informations de libération
                    </h5>
                    <hr class="divider">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class='bx bx-hash'></i> N° Libération:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['num']) ?></span></p>
                            <p><strong><i class='bx bx-calendar-check'></i> Date Libération:</strong> <span class="detail-value"><?= formatDate($liberation['date_liberation']) ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class='bx bx-upload'></i> Documents Scannés:</strong> 
                                <?php if (!empty($liberation['document_path'])): ?>
                                    <a href="<?= htmlspecialchars($liberation['document_path']) ?>" target="_blank" class="document-link">
                                        <i class='bx bx-file'></i> <?= htmlspecialchars($liberation['nom_document'] ?? 'Télécharger') ?>
                                    </a>
                                <?php else: ?>
                                    <span class="detail-value no-document">Aucun document disponible</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bouton pour revenir à la recherche -->
            <div class="action-buttons mt-4">
                <a href="liberation-filter.php?from_details=true" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Retour à la recherche
                </a>
            </div>
        </div>
    </main>
    
    <?php require_once('../Template/footer.php'); ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Track page visibility to detect when user leaves the page
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    // Only set the flag if we're actually leaving the site, not just switching tabs
                    // This will be checked against document.referrer when loading the page
                    sessionStorage.setItem('leftLiberationPages', 'true');
                }
            });
        });
    </script>
</body>
</html>
