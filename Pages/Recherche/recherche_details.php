<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'agent') {
    header("Location: ../Login/login.php");
    exit;
  }
require_once("../Template/header.php");
require_once('../../db_connection/db_conn.php');

// Variables pour stocker les résultats et messages
$garantie = null;
$error_message = null;
$success_message = null;
$search_performed = false;

// Traitement de la recherche
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_performed = true;
    
    // Récupérer et nettoyer les paramètres de recherche
    $code = isset($_POST['code']) ? trim(htmlspecialchars($_POST['code'])) : '';
    $fournisseur = isset($_POST['fournisseur']) ? trim(htmlspecialchars($_POST['fournisseur'])) : '';
    $direction = isset($_POST['direction']) ? trim(htmlspecialchars($_POST['direction'])) : '';
    
    // Vérifier si au moins un critère de recherche est fourni
    if (empty($code) && empty($fournisseur) && empty($direction)) {
        $error_message = "Veuillez saisir au moins un critère de recherche.";
    } else {
        try {
            // Construction de la requête SQL de base
            $sql = "SELECT 
                g.*,
                d.libelle AS direction_libelle,
                f.nom_fournisseur,
                m.symbole AS monnaie_symbole, m.label AS monnaie_label,
                a.label AS agence_label, a.adresse AS agence_adresse,
                b.label AS banque_label,
                ao.num_appel_offre,
                dg.id AS document_id, dg.nom_document, dg.document_path,
                l.id AS liberation_id, l.date_liberation
            FROM garantie g
            LEFT JOIN direction d ON g.direction_id = d.id
            LEFT JOIN fournisseur f ON g.fournisseur_id = f.id
            LEFT JOIN monnaie m ON g.monnaie_id = m.id
            LEFT JOIN agence a ON g.agence_id = a.id
            LEFT JOIN banque b ON a.banque_id = b.id
            LEFT JOIN appel_offre ao ON g.appel_offre_id = ao.id
            LEFT JOIN document_garantie dg ON g.id = dg.garantie_id
            LEFT JOIN liberation l ON g.id = l.garantie_id
            WHERE 1=1";
            
            $params = [];
            
            // Ajouter les conditions de recherche si elles sont fournies
            if (!empty($code)) {
                $sql .= " AND g.num_garantie = :code";
                $params[':code'] = $code;
            }
            
            if (!empty($fournisseur)) {
                $sql .= " AND f.nom_fournisseur LIKE :fournisseur";
                $params[':fournisseur'] = "%$fournisseur%";
            }
            
            if (!empty($direction)) {
                $sql .= " AND d.libelle LIKE :direction";
                $params[':direction'] = "%$direction%";
            }
            
            // Limiter les résultats si recherche par code exact
            if (!empty($code) && empty($fournisseur) && empty($direction)) {
                $sql .= " LIMIT 1";
            }
            
            $stmt = $pdo->prepare($sql);
            
            // Lier les paramètres
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            // Récupérer les résultats
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                $error_message = "Aucune garantie trouvée avec les critères spécifiés.";
            } else {
                $success_message = count($results) . " garantie(s) trouvée(s).";
                
                // Si recherche par code exact et un seul résultat, afficher directement
                if (!empty($code) && empty($fournisseur) && empty($direction) && count($results) === 1) {
                    $garantie = $results[0];
                }
            }
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la recherche: " . $e->getMessage();
        }
    }
}

// Récupérer la liste des directions pour le dropdown
$directions = [];
try {
    $stmt = $pdo->query("SELECT id, libelle FROM direction ORDER BY libelle");
    $directions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silencieux en cas d'erreur
}

// Fonction pour déterminer le statut d'une garantie
function determinerStatut($garantie) {
    $today = new DateTime();
    $validite = new DateTime($garantie['date_validite']);
    
    if (!empty($garantie['liberation_id'])) {
        return [
            'status' => 'Libérée',
            'statusClass' => 'liberated',
            'icon' => 'bx-lock-open'
        ];
    } else if ($validite < $today) {
        return [
            'status' => 'Expirée',
            'statusClass' => 'expired',
            'icon' => 'bx-time'
        ];
    } else {
        return [
            'status' => 'Valide',
            'statusClass' => 'valid',
            'icon' => 'bx-check-circle'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Garanties</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --grey: #F1F0F6;
            --dark-grey: #8D8D8D;
            --light: #fff;
            --dark: #000;
            --green: #81D43A;
            --light-green: #E3FFCB;
            --blue: #1775F1;
            --light-blue: #D0E4FF;
            --dark-blue: #0C5FCD;
            --red: #FC3B56;
            --light-red: #FFD0D6;
            --orange: #FF9F45;
            --light-orange: #FFE9D0;
            
            --border-radius-sm: 4px;
            --border-radius: 8px;
            --border-radius-lg: 16px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --box-shadow-hover: 0 5px 15px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--grey);
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--blue);
        }
        
        .page-title {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-blue);
        }
        
        .page-title i {
            margin-right: 12px;
            font-size: 28px;
            color: var(--blue);
        }
        
        .page-actions {
            display: flex;
            gap: 10px;
        }
        
        .card {
            background-color: var(--light);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 25px;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: var(--box-shadow-hover);
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--grey);
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-blue);
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 10px;
            font-size: 22px;
            color: var(--blue);
        }
        
        .card-body {
            padding: 25px;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius-sm);
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--light);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px var(--light-blue);
        }
        
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius-sm);
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--light);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%238D8D8D' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 12px;
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px var(--light-blue);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 10px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 10px;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 15px;
            text-decoration: none;
        }
        
        .btn i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .btn-primary {
            margin-top: 20px;
            height: 50px;
            background-color: var(--blue);
            color: var(--light);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(23, 117, 241, 0.25);
        }
        
        .btn-secondary {
            margin-top: 20px;
            height: 50px;
            background-color: var(--dark-grey);
            color: var(--light);
        }
        
        .btn-secondary:hover {
            background-color: #777;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(141, 141, 141, 0.25);
        }
        
        .btn-success {
            background-color: var(--green);
            color: var(--light);
        }
        
        .btn-success:hover {
            background-color: #6abb30;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(129, 212, 58, 0.25);
        }
        
        .btn-danger {
            background-color: var(--red);
            color: var(--light);
        }
        
        .btn-danger:hover {
            background-color: #e02e47;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(252, 59, 86, 0.25);
        }
        
        .btn-outline-primary {
            background-color: transparent;
            color: var(--blue);
            border: 1px solid var(--blue);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--light-blue);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert i {
            margin-right: 12px;
            font-size: 22px;
        }
        
        .alert-danger {
            background-color: var(--light-red);
            color: var(--red);
            border-left: 4px solid var(--red);
        }
        
        .alert-success {
            background-color: var(--light-green);
            color: var(--green);
            border-left: 4px solid var(--green);
        }
        
        .alert-info {
            background-color: var(--light-blue);
            color: var(--blue);
            border-left: 4px solid var(--blue);
        }
        
        .alert-warning {
            background-color: var(--light-orange);
            color: var(--orange);
            border-left: 4px solid var(--orange);
        }
        
        .garantie-details {
            background-color: var(--light);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            animation: fadeIn 0.5s ease;
            overflow: hidden;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .garantie-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid var(--grey);
            background-color: var(--light);
        }
        
        .garantie-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-blue);
            display: flex;
            align-items: center;
        }
        
        .garantie-title i {
            margin-right: 12px;
            font-size: 24px;
            color: var(--blue);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-badge i {
            margin-right: 6px;
            font-size: 16px;
        }
        
        .status-valid {
            background-color: var(--light-green);
            color: var(--green);
        }
        
        .status-expired {
            background-color: var(--light-red);
            color: var(--red);
        }
        
        .status-liberated {
            background-color: var(--light-blue);
            color: var(--blue);
        }
        
        .garantie-body {
            padding: 25px;
        }
        
        .garantie-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .info-group {
            margin-bottom: 20px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark-grey);
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }
        
        .info-value {
            padding: 12px 15px;
            background-color: var(--grey);
            border-radius: var(--border-radius-sm);
            font-size: 15px;
            color: var(--dark);
            border: 1px solid transparent;
            transition: var(--transition);
        }
        
        .info-value:hover {
            border-color: var(--blue);
            background-color: var(--light);
        }
        
        .info-value.highlight {
            background-color: var(--light-blue);
            color: var(--dark-blue);
            font-weight: 500;
        }
        
        .document-section {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--grey);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-blue);
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 22px;
            color: var(--blue);
        }
        
        .document-card {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: var(--grey);
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
            margin-bottom: 15px;
            text-decoration: none;
            color: var(--dark);
        }
        
        .document-card:hover {
            background-color: var(--light-blue);
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .document-icon {
            font-size: 28px;
            color: var(--blue);
            margin-right: 15px;
        }
        
        .document-info {
            flex: 1;
        }
        
        .document-name {
            font-weight: 500;
            margin-bottom: 3px;
        }
        
        .document-meta {
            font-size: 13px;
            color: var(--dark-grey);
        }
        
        .document-actions {
            display: flex;
            gap: 10px;
        }
        
        .document-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light);
            color: var(--blue);
            border: 1px solid var(--grey);
            transition: var(--transition);
            text-decoration: none;
        }
        
        .document-btn:hover {
            background-color: var(--blue);
            color: var(--light);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(23, 117, 241, 0.25);
        }
        
        .document-btn i {
            font-size: 18px;
        }
        
        .no-document {
            padding: 20px;
            text-align: center;
            background-color: var(--grey);
            border-radius: var(--border-radius-sm);
            color: var(--dark-grey);
            font-style: italic;
        }
        
        .no-document i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }
        
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .results-table th {
            background-color: var(--light-blue);
            color: var(--dark-blue);
            font-weight: 600;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid var(--blue);
        }
        
        .results-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--grey);
            vertical-align: middle;
        }
        
        .results-table tr:hover {
            background-color: var(--grey);
        }
        
        .results-table .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .results-table .status-pill i {
            margin-right: 4px;
            font-size: 14px;
        }
        
        .results-table .status-valid {
            background-color: var(--light-green);
            color: var(--green);
        }
        
        .results-table .status-expired {
            background-color: var(--light-red);
            color: var(--red);
        }
        
        .results-table .status-liberated {
            background-color: var(--light-blue);
            color: var(--blue);
        }
        
        .table-actions {
            display: flex;
            gap: 5px;
        }
        
        .table-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light);
            color: var(--blue);
            border: 1px solid var(--grey);
            transition: var(--transition);
            text-decoration: none;
        }
        
        .table-btn:hover {
            background-color: var(--blue);
            color: var(--light);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(23, 117, 241, 0.15);
        }
        
        .table-btn i {
            font-size: 16px;
        }
        
        .no-results {
            padding: 40px 20px;
            text-align: center;
            background-color: var(--grey);
            border-radius: var(--border-radius);
            margin-top: 20px;
        }
        
        .no-results i {
            font-size: 48px;
            color: var(--dark-grey);
            margin-bottom: 15px;
            display: block;
        }
        
        .no-results h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .no-results p {
            color: var(--dark-grey);
            margin-bottom: 20px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .search-tips {
            background-color: var(--light-blue);
            border-radius: var(--border-radius);
            padding: 15px 20px;
            margin-top: 20px;
        }
        
        .search-tips-title {
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .search-tips-title i {
            margin-right: 8px;
        }
        
        .search-tips ul {
            margin-left: 30px;
        }
        
        .search-tips li {
            margin-bottom: 5px;
        }
        
        @media (max-width: 768px) {
            .search-form {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .garantie-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-badge {
                margin-top: 10px;
            }
            
            .garantie-content {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .actions .btn {
                width: 100%;
            }
            
            .results-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        /* Tooltip */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }
        
        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background-color: var(--dark);
            color: var(--light);
            border-radius: var(--border-radius-sm);
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 10;
        }
        
        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 10px);
        }
        
        /* Print styles */
        @media print {
            body {
                background-color: white;
                color: black;
            }
            
            .container {
                width: 100%;
                max-width: none;
                padding: 0;
            }
            
            .page-header, .card-header, .form-actions, .actions, .no-print {
                display: none !important;
            }
            
            .card, .garantie-details {
                box-shadow: none;
                border: 1px solid #ddd;
                break-inside: avoid;
            }
            
            .info-value {
                background-color: white;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div class="page-title">
                <i class='bx bx-search-alt'></i>
                Recherche de Garanties Bancaires
            </div>
           
        </div>
        
        <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class='bx bx-error-circle'></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success_message && !$garantie && isset($results) && count($results) > 1): ?>
        <div class="alert alert-success">
            <i class='bx bx-check-circle'></i>
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class='bx bx-filter-alt'></i>
                    Critères de recherche
                </div>
            </div>
            <div class="card-body">
                <form method="POST" class="search-form">
                    <div>
                        <div class="form-group">
                            <label for="code" class="form-label">Numéro de garantie</label>
                            <input type="text" id="code" name="code" class="form-control" 
                                   placeholder="Ex: GAR001" 
                                   value="<?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label for="fournisseur" class="form-label">Fournisseur</label>
                            <input type="text" id="fournisseur" name="fournisseur" class="form-control" 
                                   placeholder="Nom du fournisseur" 
                                   value="<?php echo isset($_POST['fournisseur']) ? htmlspecialchars($_POST['fournisseur']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label for="direction" class="form-label">Direction</label>
                            <select id="direction" name="direction" class="form-select">
                                <option value="">Toutes les directions</option>
                                <?php foreach ($directions as $dir): ?>
                                <option value="<?php echo htmlspecialchars($dir['libelle']); ?>" 
                                        <?php echo (isset($_POST['direction']) && $_POST['direction'] == $dir['libelle']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dir['libelle']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-search'></i> Rechercher
                        </button>
                        <button type="reset" class="btn btn-secondary" id="resetBtn">
                            <i class='bx bx-reset'></i> Réinitialiser
                        </button>
                    </div>
                </form>
                
                <?php if ($search_performed && !$error_message): ?>
                    <?php if (isset($results) && count($results) > 1): ?>
                        <!-- Affichage des résultats multiples -->
                        <div class="section-title" style="margin-top: 30px;">
                            <i class='bx bx-list-check'></i>
                            Résultats de la recherche (<?php echo count($results); ?> garanties)
                        </div>
                        
                        <div class="table-responsive">
                            <table class="results-table">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Fournisseur</th>
                                        <th>Direction</th>
                                        <th>Montant</th>
                                        <th>Date validité</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $result): 
                                        $statusInfo = determinerStatut($result);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($result['num_garantie']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($result['nom_fournisseur']); ?></td>
                                        <td><?php echo htmlspecialchars($result['direction_libelle']); ?></td>
                                        <td>
                                            <?php echo number_format($result['montant'], 2, ',', ' '); ?> 
                                            <?php echo htmlspecialchars($result['monnaie_symbole']); ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($result['date_validite'])); ?></td>
                                        <td>
                                            <span class="status-pill status-<?php echo $statusInfo['statusClass']; ?>">
                                                <i class='bx <?php echo $statusInfo['icon']; ?>'></i>
                                                <?php echo $statusInfo['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                
                                                <?php if (isset($result['document_id']) && !empty($result['document_id'])): ?>
                                                <a href="../../Backend/Garantie/view_document.php?id=<?php echo $result['document_id']; ?>" target="_blank" class="table-btn" data-tooltip="Voir document">
                                                    <i class='bx bx-file'></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($garantie): ?>
                        <!-- Affichage détaillé d'une garantie -->
                        <?php $statusInfo = determinerStatut($garantie); ?>
                        
                        <div class="alert alert-success" style="margin-top: 30px;">
                            <i class='bx bx-check-circle'></i>
                            Garantie trouvée avec succès.
                        </div>
                        
                        <div class="garantie-details">
                            <div class="garantie-header">
                                <div class="garantie-title">
                                    <i class='bx bx-shield'></i>
                                    Garantie #<?php echo htmlspecialchars($garantie['num_garantie']); ?>
                                </div>
                                <span class="status-badge status-<?php echo $statusInfo['statusClass']; ?>">
                                    <i class='bx <?php echo $statusInfo['icon']; ?>'></i>
                                    <?php echo $statusInfo['status']; ?>
                                </span>
                            </div>
                            
                            <div class="garantie-body">
                                <div class="garantie-content">
                                    <div class="info-column">
                                        <div class="info-group">
                                            <span class="info-label">Numéro de Garantie</span>
                                            <div class="info-value highlight"><?php echo htmlspecialchars($garantie['num_garantie']); ?></div>
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
                                            <span class="info-label">Agence</span>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars(isset($garantie['agence_label']) ? $garantie['agence_label'] : ''); ?>
                                                <?php if (isset($garantie['agence_adresse']) && !empty($garantie['agence_adresse'])): ?>
                                                    <br><small><?php echo htmlspecialchars($garantie['agence_adresse']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="document-section">
                                    <div class="section-title">
                                        <i class='bx bx-file'></i>
                                        Document associé
                                    </div>
                                    
                                    <?php if (isset($garantie['document_id']) && !empty($garantie['document_id'])): ?>
                                    <div class="document-card">
                                        <i class='bx bxs-file-pdf document-icon'></i>
                                        <div class="document-info">
                                            <div class="document-name"><?php echo htmlspecialchars($garantie['nom_document']); ?></div>
                                            <div class="document-meta">Document PDF</div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../../Backend/Garantie/view_document.php?id=<?php echo $garantie['document_id']; ?>" target="_blank" class="document-btn" data-tooltip="Voir le document">
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
                                </div>
                                
                
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($search_performed && !$garantie && !isset($results)): ?>
                    <div class="no-results">
                        <i class='bx bx-search-alt'></i>
                        <h3>Aucune garantie trouvée</h3>
                        <p>Aucune garantie ne correspond aux critères de recherche spécifiés. Veuillez essayer avec d'autres termes ou critères.</p>
                        <button id="newSearchBtn" class="btn btn-primary">
                            <i class='bx bx-refresh'></i> Nouvelle recherche
                        </button>
                    </div>
                    
                    <div class="search-tips">
                        <div class="search-tips-title">
                            <i class='bx bx-bulb'></i> Conseils de recherche
                        </div>
                        <ul>
                            <li>Vérifiez l'orthographe du numéro de garantie</li>
                            <li>Essayez de rechercher par fournisseur ou direction</li>
                            <li>Utilisez des termes plus généraux pour élargir les résultats</li>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus sur le champ de recherche au chargement de la page
            document.getElementById('code').focus();
            
            // Animation pour faire disparaître les alertes après 5 secondes
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 1s ease';
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 1000);
                    });
                }, 5000);
            }
            
            // Réinitialiser le formulaire
            document.getElementById('resetBtn').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('code').value = '';
                document.getElementById('fournisseur').value = '';
                document.getElementById('direction').value = '';
                document.getElementById('code').focus();
            });
            
            // Nouvelle recherche
            const newSearchBtn = document.getElementById('newSearchBtn');
            if (newSearchBtn) {
                newSearchBtn.addEventListener('click', function() {
                    document.getElementById('resetBtn').click();
                });
            }
        });
        
        // Fonction supprimée: printGarantie

        // Fonction pour vérifier si un document existe
        function checkDocumentExists(documentPath) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => resolve(true);
                img.onerror = () => resolve(false);
                img.src = documentPath;
            });
        }

        // Vérifier les liens de documents au chargement
        document.querySelectorAll('a[href*="view_document.php"]').forEach(link => {
            link.addEventListener('click', async function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                
                // Afficher un message de chargement
                const loadingToast = document.createElement('div');
                loadingToast.className = 'alert alert-info';
                loadingToast.style.position = 'fixed';
                loadingToast.style.top = '20px';
                loadingToast.style.right = '20px';
                loadingToast.style.zIndex = '9999';
                loadingToast.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Chargement du document...';
                document.body.appendChild(loadingToast);
                
                try {
                    // Ouvrir le document dans un nouvel onglet
                    window.open(href, '_blank');
                    
                    // Supprimer le message après 2 secondes
                    setTimeout(() => {
                        loadingToast.remove();
                    }, 2000);
                } catch (error) {
                    // En cas d'erreur, afficher un message
                    loadingToast.className = 'alert alert-danger';
                    loadingToast.innerHTML = '<i class="bx bx-error-circle"></i> Impossible d\'ouvrir le document. Vérifiez que le fichier existe.';
                    
                    // Supprimer le message après 5 secondes
                    setTimeout(() => {
                        loadingToast.remove();
                    }, 5000);
                }
            });
        });
    </script>
</body>

<?php
require_once ('../Template/footer.php');
?>
