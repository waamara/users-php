<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'responsable') {
    header("Location: ../Login/login.php");
    exit;
  }
require_once("../Template/header.php"); 
require_once("../../db_connection/db_conn.php");

// Initialiser les variables d'erreur
$errors = [];
$num_error = '';
$date_liberation_error = '';
$file_error = '';
$form_submitted = false;
$show_sweet_alert = false;
$edit_mode = false;
$pdf_only_error = ''; // Nouvelle variable pour l'erreur de type de fichier

// Récupérer l'ID de la garantie depuis le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['garantie_id'])) {
    $garantie_id = $_POST['garantie_id'];
} else if (isset($_GET['garantie_id'])) {
    $garantie_id = $_GET['garantie_id'];
} else {
    die("ID de garantie non fourni.");
}

// Vérifier si on est en mode édition
if (isset($_GET['edit']) && $_GET['edit'] === 'true') {
    $edit_mode = true;
}

// Traitement du formulaire de libération
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_liberation'])) {
    $form_submitted = true;
    $num = trim($_POST['num'] ?? '');
    $date_liberation = trim($_POST['date_liberation'] ?? '');
    $original_filename = '';
    $liberation_id = isset($_POST['liberation_id']) ? $_POST['liberation_id'] : null;
    
    // Validation du fichier
    if (isset($_FILES['document_scanne']) && $_FILES['document_scanne']['error'] != UPLOAD_ERR_NO_FILE) {
        $original_filename = basename($_FILES['document_scanne']['name']);
        $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        // Vérifier si le fichier est un PDF
        if ($file_extension !== 'pdf') {
            $pdf_only_error = 'Seuls les fichiers PDF sont acceptés.';
            $errors[] = $pdf_only_error;
        }
    }

    // Validation du numéro de libération
    if (empty($num)) {
        $num_error = 'Ce champ est obligatoire.';
        $errors[] = $num_error;
    } else if (!$liberation_id) {
        // Vérifier l'unicité du numéro de libération seulement pour les nouvelles entrées
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM liberation WHERE num = :num");
        $stmt->bindParam(':num', $num, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $num_error = 'Ce numéro de libération existe déjà.';
            $errors[] = $num_error;
        }
    }

    // Validation de la date de libération
    if (empty($date_liberation)) {
        $date_liberation_error = 'Ce champ est obligatoire.';
        $errors[] = $date_liberation_error;
    }

    // Validation du fichier pour les nouvelles entrées
    if (!$liberation_id && (!isset($_FILES['document_scanne']) || $_FILES['document_scanne']['error'] == UPLOAD_ERR_NO_FILE)) {
        $file_error = 'Ce champ est obligatoire.';
        $errors[] = $file_error;
    } else if (isset($_FILES['document_scanne']) && $_FILES['document_scanne']['error'] != UPLOAD_ERR_NO_FILE) {
        // Vérifier l'unicité du nom de fichier
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_liberation WHERE nom_document = :nom_document AND liberation_id != :liberation_id");
        $stmt->bindParam(':nom_document', $original_filename, PDO::PARAM_STR);
        $stmt->bindParam(':liberation_id', $liberation_id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $file_error = 'Un document avec ce nom existe déjà.';
            $errors[] = $file_error;
        }
    }

    // Si aucune erreur, traiter le formulaire
    if (empty($errors)) {
        try {
            if ($liberation_id) {
                // Mise à jour d'une libération existante
                $sql_liberation = "UPDATE liberation SET num = :num, date_liberation = :date_liberation WHERE id = :liberation_id";
                $stmt_liberation = $pdo->prepare($sql_liberation);
                $stmt_liberation->bindParam(':num', $num, PDO::PARAM_STR);
                $stmt_liberation->bindParam(':date_liberation', $date_liberation, PDO::PARAM_STR);
                $stmt_liberation->bindParam(':liberation_id', $liberation_id, PDO::PARAM_INT);
                $stmt_liberation->execute();
            } else {
                // Insertion d'une nouvelle libération
                $sql_liberation = "INSERT INTO liberation (num, date_liberation, garantie_id) 
                             VALUES (:num, :date_liberation, :garantie_id)";
                $stmt_liberation = $pdo->prepare($sql_liberation);
                $stmt_liberation->bindParam(':num', $num, PDO::PARAM_STR);
                $stmt_liberation->bindParam(':date_liberation', $date_liberation, PDO::PARAM_STR);
                $stmt_liberation->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
                $stmt_liberation->execute();
                
                // Récupérer l'ID de la libération insérée
                $liberation_id = $pdo->lastInsertId();
            }
            
            // Traiter le fichier si présent
            if (isset($_FILES['document_scanne']) && $_FILES['document_scanne']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/documents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $filename = basename($_FILES['document_scanne']['name']);
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['document_scanne']['tmp_name'], $file_path)) {
                    // Vérifier si un document existe déjà pour cette libération
                    $stmt_check = $pdo->prepare("SELECT id FROM document_liberation WHERE liberation_id = :liberation_id");
                    $stmt_check->bindParam(':liberation_id', $liberation_id, PDO::PARAM_INT);
                    $stmt_check->execute();
                    
                    if ($stmt_check->rowCount() > 0) {
                        // Mettre à jour le document existant
                        $doc_id = $stmt_check->fetchColumn();
                        $sql_doc = "UPDATE document_liberation SET nom_document = :nom_document, document_path = :document_path 
                                    WHERE id = :doc_id";
                        $stmt_doc = $pdo->prepare($sql_doc);
                        $stmt_doc->bindParam(':nom_document', $filename, PDO::PARAM_STR);
                        $stmt_doc->bindParam(':document_path', $file_path, PDO::PARAM_STR);
                        $stmt_doc->bindParam(':doc_id', $doc_id, PDO::PARAM_INT);
                        $stmt_doc->execute();
                    } else {
                        // Insérer un nouveau document
                        $sql_doc = "INSERT INTO document_liberation (liberation_id, nom_document, document_path) 
                                    VALUES (:liberation_id, :nom_document, :document_path)";
                        $stmt_doc = $pdo->prepare($sql_doc);
                        $stmt_doc->bindParam(':liberation_id', $liberation_id, PDO::PARAM_INT);
                        $stmt_doc->bindParam(':nom_document', $filename, PDO::PARAM_STR);
                        $stmt_doc->bindParam(':document_path', $file_path, PDO::PARAM_STR);
                        $stmt_doc->execute();
                    }
                }
            }

            // Activer l'affichage de SweetAlert
            $show_sweet_alert = true;
        } catch (PDOException $e) {
            // Handle database errors
            $errors[] = "Une erreur s'est produite lors de l'enregistrement: " . $e->getMessage();
        }
    }
}

// Requête pour récupérer les informations de la garantie avec le nom de la banque
$sql_garantie = "SELECT 
    g.id AS garantie_id, g.num_garantie, g.date_creation, g.date_emission, g.date_validite, 
    g.montant, 
    d.libelle AS direction, 
    f.nom_fournisseur, 
    m.label AS monnaie, 
    a.label AS agence,
    b.label AS banque_nom,
    ao.num_appel_offre 
FROM garantie g
JOIN direction d ON g.direction_id = d.id
JOIN fournisseur f ON g.fournisseur_id = f.id
JOIN monnaie m ON g.monnaie_id = m.id
JOIN agence a ON g.agence_id = a.id
JOIN banque b ON a.banque_id = b.id
JOIN appel_offre ao ON g.appel_offre_id = ao.id
WHERE g.id = :garantie_id";
$stmt_garantie = $pdo->prepare($sql_garantie);
$stmt_garantie->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
$stmt_garantie->execute();
$garantie = $stmt_garantie->fetch(PDO::FETCH_ASSOC);

// Vérifier si une libération existe déjà pour cette garantie
$sql_liberation = "SELECT l.*, d.nom_document, d.document_path 
            FROM liberation l
            LEFT JOIN document_liberation d ON l.id = d.liberation_id
            WHERE l.garantie_id = :garantie_id";
$stmt_liberation = $pdo->prepare($sql_liberation);
$stmt_liberation->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
$stmt_liberation->execute();
$liberation = $stmt_liberation->fetch(PDO::FETCH_ASSOC);

// Déterminer si on affiche le formulaire ou les informations de libération
$show_form = !$liberation || $edit_mode;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libération de Garantie</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="./css/liberation.css">
</head>
<style></style>
<body>
    <main id="dynamic-content">
        <div class="container">
            <!-- Titre Principal -->
            <h2 class="main-title">
                <i class='bx bx-shield-quarter'></i> Libération de la garantie de soumission N° <?= htmlspecialchars($garantie['num_garantie']) ?>
            </h2>
            <!-- Détails de la Garantie -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-info-circle'></i> Détail de la garantie N°  <?= htmlspecialchars($garantie['num_garantie']) ?>
                    </h5>
                    <hr class="divider">
                    <!-- Première ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-hash'></i> N° de garantie:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['num_garantie']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-building'></i> Structure:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['direction']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-user-pin'></i> Fournisseurs:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['nom_fournisseur']) ?></span></p>
                        </div>
                    </div>
                    <!-- Deuxième ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-file-blank'></i> Référence AO:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['num_appel_offre']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bxs-bank'></i> Banque:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['banque_nom']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <!-- Champ vide pour aligner -->
                        </div>
                    </div>
                    <!-- Troisième ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-money'></i> Montant:</strong> <span class="detail-value"><?= number_format($garantie['montant'], 2) ?> <?= htmlspecialchars($garantie['monnaie'] ?? 'DA') ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-buildings'></i> Agence:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['agence']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <!-- Intentionally left empty for alignment -->
                        </div>
                    </div>
                    <!-- Quatrième ligne -->
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-calendar'></i> Date d'émission:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['date_emission']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong><i class='bx bx-calendar-exclamation'></i> Date d'expiration:</strong> <span class="detail-value"><?= htmlspecialchars($garantie['date_validite']) ?></span></p>
                        </div>
                        <div class="col-md-4">
                            <!-- Intentionally left empty for alignment -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Formulaire de Libération ou Informations de libération -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-shield'></i> Libération de la garantie N° <?= htmlspecialchars($garantie['num_garantie']) ?>
                    </h5>
                    <hr class="divider">
                    <?php if (!$show_form): ?>
                        <!-- Affichage des informations de libération -->
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class='bx bx-hash'></i> N° Libération:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['num']) ?></span></p>
                                <p><strong><i class='bx bx-calendar-check'></i> Date Libération:</strong> <span class="detail-value"><?= htmlspecialchars($liberation['date_liberation']) ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class='bx bx-upload'></i> Documents Scannés:</strong> 
                                    <?php if (!empty($liberation['document_path'])): ?>
                                        <a href="<?= htmlspecialchars($liberation['document_path']) ?>" target="_blank">
                                            <?= htmlspecialchars($liberation['nom_document'] ?? 'Télécharger') ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="detail-value">Aucun document disponible</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div id="not-accepting-container" class="not-accepting-container" style="display: <?= (isset($liberation['status']) && $liberation['status'] === 'rejected') ? 'flex' : 'none' ?>;">
                            <img src="../../assets/images/not-accepting.png" alt="Non accepté" class="not-accepting-image">
                            <span class="not-accepting-text">Non Accepté</span>
                        </div>
                        <!-- Back Button and Modification Button -->
                        <div class="button-container mt-4">
                            <a href="../Garantie/ListeGaranties.php" class="btn sec half-width">
                                <i class='bx bx-arrow-back'></i> Retour à la liste des garanties
                            </a>
                            <a href="?garantie_id=<?= $garantie_id ?>&edit=true" class="btn sec half-width">
                                <i class='bx bx-edit'></i> Modifier
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Formulaire de libération -->
                        <form method="POST" action="" enctype="multipart/form-data" id="liberation-form">
                            <input type="hidden" name="garantie_id" value="<?= $garantie['garantie_id'] ?>">
                            <?php if ($edit_mode && $liberation): ?>
                                <input type="hidden" name="liberation_id" value="<?= $liberation['id'] ?>">
                            <?php endif; ?>
                            <!-- Première ligne du formulaire -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="num" class="form-label">
                                            <i class='bx bx-hash'></i> N° Libération <span class="required-asterisk" style="color: red;">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="num" name="num" value="<?= $edit_mode && $liberation ? htmlspecialchars($liberation['num']) : ($form_submitted ? htmlspecialchars($_POST['num'] ?? '') : '') ?>">
                                        <span id="num_error" class="error-message"><?= $num_error ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_liberation" class="form-label">
                                            <i class='bx bx-calendar-check'></i> Date Libération<span class="required-asterisk" style="color: red;">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="date_liberation" name="date_liberation" value="<?= $edit_mode && $liberation ? htmlspecialchars($liberation['date_liberation']) : ($form_submitted ? htmlspecialchars($_POST['date_liberation'] ?? '') : date('Y-m-d')) ?>">
                                        <span id="date_liberation_error" class="error-message"><?= $date_liberation_error ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Deuxième ligne du formulaire -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="document_scanne" class="form-label">
                                            <i class='bx bx-upload'></i> Documents Scannés<?php if (!$edit_mode || !$liberation): ?><span class="required-asterisk" style="color: red;">*</span><?php endif; ?>
                                        </label>
                                        <!-- Placer l'input file en dehors de la zone de dépôt -->
                                        <input type="file" class="form-control" id="document_scanne" name="document_scanne" accept="application/pdf" style="display: none;">
                                        <div class="custom-file-input" id="file-drop-area">
                                            <i class='bx bx-upload'></i>
                                            <?php if ($edit_mode && $liberation && !empty($liberation['nom_document'])): ?>
                                                <span class="file-label">Fichier actuel: <?= htmlspecialchars($liberation['nom_document']) ?></span>
                                                <span class="file-label">Cliquez ici pour changer de fichier</span>
                                            <?php else: ?>
                                                <span class="file-label">Cliquez ici pour sélectionner un fichier</span>
                                            <?php endif; ?>
                                            <span class="file-selected" id="file-selected-name"></span>
                                        </div>
                                        <span id="nom_document_error" class="error-message"><?= $file_error ?></span>
                                        <span id="pdf_only_error" class="error-message" style="color: red;"><?= $pdf_only_error ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Boutons d'action -->
                            <div class="action-buttons d-flex justify-content-between mt-4">
                                <button type="submit" name="submit_liberation" class="btn btn-primary">
                                    <i class='bx bx-check-circle'></i> <?= $edit_mode ? 'Mettre à jour la libération' : 'Valider la libération' ?>
                                </button>
                                <a href="<?= $edit_mode ? "?garantie_id={$garantie_id}" : "../Garantie/ListeGaranties.php" ?>" class="btn btn-primary">
                                    <i class='bx bx-arrow-back'></i> <?= $edit_mode ? 'Annuler' : 'Retour à la liste des garanties' ?>
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <?php require_once('../Template/footer.php'); ?>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Éléments du DOM
    const numInput = document.getElementById('num');
    const fileInput = document.getElementById('document_scanne');
    const numError = document.getElementById('num_error');
    const nomDocumentError = document.getElementById('nom_document_error');
    const pdfOnlyError = document.getElementById('pdf_only_error');
    const fileSelectedName = document.getElementById('file-selected-name');
    const fileDropArea = document.getElementById('file-drop-area');
    const dateLiberationInput = document.getElementById('date_liberation');
    const dateLiberationError = document.getElementById('date_liberation_error');
    const form = document.getElementById('liberation-form');

    // Rendre la zone de dépôt de fichier cliquable
    if (fileDropArea && fileInput) {
        fileDropArea.addEventListener('click', function() {
            fileInput.click();
        });
    }

    // Fonction pour vérifier l'unicité via AJAX
    function checkUniqueness(field, value, errorElement) {
        // Afficher un indicateur de chargement
        errorElement.textContent = 'Vérification en cours...';
        errorElement.style.color = '#007bff';

        // Envoyer la requête AJAX
        fetch('check_uniqueness_liberation.php', {
            method: 'POST',
            body: JSON.stringify({ field, value }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                errorElement.textContent = data.message;
                errorElement.style.color = 'green';
            } else {
                errorElement.textContent = data.message;
                errorElement.style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            errorElement.textContent = 'Erreur lors de la vérification';
            errorElement.style.color = 'red';
        });
    }

    // Vérifier l'unicité du numéro de libération
    if (numInput) {
        numInput.addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                checkUniqueness('num', value, numError);
            } else {
                numError.textContent = 'Ce champ est obligatoire.';
                numError.style.color = 'red';
            }
        });
    }

    // Function to toggle the Not Accepting image
    function toggleNotAcceptingImage(show) {
        const container = document.getElementById('not-accepting-container');
        if (container) {
            container.style.display = show ? 'flex' : 'none';
        }
    }

    // Check if we need to show the Not Accepting image on page load
    <?php if (isset($liberation['status']) && $liberation['status'] === 'rejected'): ?>
        toggleNotAcceptingImage(true);
    <?php endif; ?>

    // Add event listener for file type validation
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const fileName = file.name;
                const fileType = file.type;
                
                // Check if file is a PDF
                if (fileType !== 'application/pdf') {
                    pdfOnlyError.textContent = 'Seuls les fichiers PDF sont acceptés.';
                    pdfOnlyError.style.color = 'red';
                    // Clear the file input
                    this.value = '';
                    if (fileSelectedName) {
                        fileSelectedName.style.display = 'none';
                    }
                    return;
                } else {
                    pdfOnlyError.textContent = '';
                }
                
                // Display the selected file name
                if (fileSelectedName) {
                    fileSelectedName.textContent = 'Fichier sélectionné: ' + fileName;
                    fileSelectedName.style.display = 'block';
                }
                
                // Check uniqueness of the file name
                checkUniqueness('nom_document', fileName, nomDocumentError);
            } else {
                if (fileSelectedName) {
                    fileSelectedName.style.display = 'none';
                }
                nomDocumentError.textContent = '';
                pdfOnlyError.textContent = '';
            }
        });
    }

    // Fonctionnalité de glisser-déposer
    if (fileDropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            fileDropArea.classList.add('highlight');
        }

        function unhighlight() {
            fileDropArea.classList.remove('highlight');
        }

        fileDropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            
            // Déclencher l'événement change
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    }

    // Afficher SweetAlert pour le message de succès
    <?php if ($show_sweet_alert): ?>
    Swal.fire({
        title: 'Succès!',
        text: '<?= $edit_mode ? 'Garantie mise à jour avec succès' : 'Garantie libérée avec succès' ?>',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        window.location.href = '?garantie_id=<?= $garantie_id ?>';
    });
    <?php endif; ?>

    // Validation du formulaire lors de la soumission
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Vérifier le numéro de libération
            if (!numInput.value.trim()) {
                numError.textContent = 'Ce champ est obligatoire.';
                numError.style.color = 'red';
                hasErrors = true;
            }
            
            // Vérifier le fichier (seulement pour les nouvelles entrées)
            const isNewEntry = !form.querySelector('input[name="liberation_id"]');
            if (isNewEntry && !fileInput.files.length) {
                nomDocumentError.textContent = 'Ce champ est obligatoire.';
                nomDocumentError.style.color = 'red';
                hasErrors = true;
            }
            
            // Vérifier la date de libération
            if (!dateLiberationInput.value) {
                dateLiberationError.textContent = 'Ce champ est obligatoire.';
                dateLiberationError.style.color = 'red';
                hasErrors = true;
            }
            
            if (hasErrors) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Erreur!',
                    text: 'Veuillez corriger les erreurs avant de soumettre le formulaire.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
});
    </script>
</body>
</html>
