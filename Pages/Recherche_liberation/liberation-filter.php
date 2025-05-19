<?php
session_start(); // Start session at the beginning
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// For debugging - uncomment to see the current directory path
// echo "Current directory: " . __DIR__;

// Check if we're coming back from details page
$comingFromDetails = isset($_GET['from_details']) && $_GET['from_details'] == 'true';

// Initialize variables
$startDate = '';
$endDate = '';
$hasSearched = false;
$error = '';

// If coming from details page, restore search parameters from session
if ($comingFromDetails && isset($_SESSION['liberation_search'])) {
    $startDate = $_SESSION['liberation_search']['startDate'] ?? '';
    $endDate = $_SESSION['liberation_search']['endDate'] ?? '';
    $hasSearched = true;
} 
// If form was submitted, use the submitted values
else if (isset($_GET['startDate']) || isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'] ?? '';
    $endDate = $_GET['endDate'] ?? '';
    $hasSearched = true;
    
    // Save search parameters to session
    $_SESSION['liberation_search'] = [
        'startDate' => $startDate,
        'endDate' => $endDate
    ];
} 
// If not coming from details and not a new search, clear any previous search
else {
    // Clear session search data when arriving fresh to the page
    unset($_SESSION['liberation_search']);
}

// Validate dates if both are provided
if (!empty($startDate) && !empty($endDate) && strtotime($startDate) > strtotime($endDate)) {
    $error = "La date de début doit être antérieure à la date de fin";
}

// Build the SQL query based on provided dates
$sql = "SELECT l.id, l.num, l.date_liberation, 
               g.num_garantie, g.montant, g.date_emission, g.date_validite, 
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
        WHERE 1=1";

// Add date filters if provided
if (!empty($startDate) && empty($error)) {
    $sql .= " AND l.date_liberation >= :startDate";
}

if (!empty($endDate) && empty($error)) {
    $sql .= " AND l.date_liberation <= :endDate";
}

// Order by date
$sql .= " ORDER BY l.date_liberation DESC";

// Execute query if no validation errors
$liberations = [];
if ($hasSearched && empty($error)) {
    try {
        $stmt = $pdo->prepare($sql);
        
        if (!empty($startDate)) {
            $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        }
        
        if (!empty($endDate)) {
            $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $liberations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Erreur de base de données: " . $e->getMessage();
    }
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche des Libérations par Date</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- CSS Files with multiple path options to ensure one works -->
    <link rel="stylesheet" href="../css/liberation.css">
    <link rel="stylesheet" href="css/liberation-filter.css">
    
    <!-- Fallback inline styles for critical elements -->
    <style>
        #dynamic-content .container { max-width: 1400px; margin: 0 auto; padding: 20px; width: 100%; }
        #dynamic-content .card { background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        #dynamic-content .main-title { font-size: 1.8rem; color: #007bff; font-weight: bold; margin-bottom: 30px; }
        #dynamic-content .card-subtitle { font-size: 1.2rem; font-weight: bold; color: #333; margin-bottom: 15px; }
        #dynamic-content .divider { border-top: 1px solid #ddd; margin: 15px 0 20px 0; }
        #dynamic-content .row { display: flex; flex-wrap: wrap; margin: 0 -10px; margin-bottom: 20px; }
        #dynamic-content .col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 0 10px; }
        #dynamic-content .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        #dynamic-content .btn-primary { background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; }
        #dynamic-content .error-message { color: #dc3545; margin-top: 5px; display: block; }
        #dynamic-content .table { width: 100%; border-collapse: collapse; }
        #dynamic-content .table th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-weight: bold; border-bottom: 2px solid #dee2e6; }
        #dynamic-content .table td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; }
        
        /* Specific styles from liberation-filter.css */
        .result-count { color: #6c757d; font-size: 0.9rem; margin-top: 5px; margin-bottom: 15px; }
        .no-results { text-align: center; padding: 30px 0; color: #6c757d; }
        .no-results i { font-size: 3rem; color: #adb5bd; margin-bottom: 15px; display: block; }
        .document-link { display: flex; align-items: center; color: #007bff; text-decoration: none; }
        .btn-view { display: inline-flex; align-items: center; background-color: #28a745; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }
        
        /* Fix for date error message - increased spacing */
        .date-error-container {
            width: 100%;
            clear: both;
            padding-top: 15px;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            #dynamic-content .col-md-6 { flex: 0 0 100%; max-width: 100%; }
            #dynamic-content .table th, #dynamic-content .table td { padding: 8px 10px; font-size: 0.9rem; }
        }
</style>
</head>
<body>
    <main id="dynamic-content">
        <div class="container">
            <!-- Titre Principal -->
            <h2 class="main-title">
                <i class='bx bx-search'></i> Recherche des Libérations par Date
            </h2>
            
            <!-- Formulaire de recherche -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-calendar'></i> Filtrer par Période
                    </h5>
                    <hr class="divider">
                    
                    <form method="GET" action="" id="filter-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="startDate" class="form-label">
                                        <i class='bx bx-calendar'></i> Date de début
                                    </label>
                                    <input type="date" class="form-control" id="startDate" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="endDate" class="form-label">
                                        <i class='bx bx-calendar-check'></i> Date de fin
                                    </label>
                                    <input type="date" class="form-control" id="endDate" name="endDate" value="<?= htmlspecialchars($endDate) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Error message container below both date inputs -->
                        <div class="date-error-container">
                            <span id="date-error" class="error-message"><?= $error ?></span>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary mt-3" id="search-btn">
                                <i class='bx bx-search'></i> Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($hasSearched): ?>
            <!-- Résultats de la recherche -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-list-ul'></i> Résultats de la Recherche
                    </h5>
                    <p class="result-count">
                        <?= count($liberations) ?> libération(s) trouvée(s)
                        <?php if (!empty($startDate) && !empty($endDate)): ?>
                            entre le <?= formatDate($startDate) ?> et le <?= formatDate($endDate) ?>
                        <?php elseif (!empty($startDate)): ?>
                            après le <?= formatDate($startDate) ?>
                        <?php elseif (!empty($endDate)): ?>
                            avant le <?= formatDate($endDate) ?>
                        <?php endif; ?>
                    </p>
                    <hr class="divider">
                    
                    <?php if (count($liberations) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>N° Libération</th>
                                        <th>Date Libération</th>
                                        <th>N° Garantie</th>
                                        <th>Fournisseur</th>
                                        <th>Direction</th>
                                        <th>Montant</th>
                                        <th>Document</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($liberations as $liberation): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($liberation['num']) ?></td>
                                            <td><?= formatDate($liberation['date_liberation']) ?></td>
                                            <td><?= htmlspecialchars($liberation['num_garantie']) ?></td>
                                            <td><?= htmlspecialchars($liberation['nom_fournisseur']) ?></td>
                                            <td><?= htmlspecialchars($liberation['direction']) ?></td>
                                            <td><?= formatCurrency($liberation['montant'], $liberation['monnaie']) ?></td>
                                            <td>
                                                <?php if (!empty($liberation['document_path'])): ?>
                                                    <a href="<?= htmlspecialchars($liberation['document_path']) ?>" target="_blank" class="document-link">
                                                        <i class='bx bx-file'></i> <?= htmlspecialchars($liberation['nom_document']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="no-document">Aucun document</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="liberation-details.php?id=<?= $liberation['id'] ?>" class="btn-view">
                                                    <i class='bx bx-show'></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <i class='bx bx-search-alt'></i>
                            <p>Aucune libération ne correspond aux critères de recherche</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php require_once('../Template/footer.php'); ?>
    
    <!-- JavaScript file with multiple path options -->
    <script src="js/liberation-filter.js"></script>
    
    <!-- Fallback inline JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Basic form validation
            const filterForm = document.getElementById('filter-form');
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            const dateError = document.getElementById('date-error');
            
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    if (dateError) {
                        dateError.textContent = '';
                    }
                    
                    // Check if at least one date is provided
                    if ((!startDateInput || !startDateInput.value) && (!endDateInput || !endDateInput.value)) {
                        e.preventDefault();
                        if (dateError) {
                            dateError.textContent = 'Veuillez sélectionner au moins une date';
                        }
                        return false;
                    }
                    
                    // Check if dates are valid when both are provided
                    if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
                        const startDate = new Date(startDateInput.value);
                        const endDate = new Date(endDateInput.value);
                        
                        if (startDate > endDate) {
                            e.preventDefault();
                            if (dateError) {
                                dateError.textContent = 'La date de début doit être antérieure à la date de fin';
                            }
                            return false;
                        }
                    }
                    
                    return true;
                });
            }
            
            // Reset error on input change
            if (endDateInput) {
                endDateInput.addEventListener('input', function() {
                    if (dateError) {
                        dateError.textContent = '';
                    }
                });
            }
            
            if (startDateInput) {
                startDateInput.addEventListener('input', function() {
                    if (dateError) {
                        dateError.textContent = '';
                    }
                });
            }
            
            // Track page visibility to detect when user leaves the page
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    // Set a flag in sessionStorage to indicate the user left the page
                    sessionStorage.setItem('leftLiberationPages', 'true');
                }
            });
        });
</script>
</body>
</html>


