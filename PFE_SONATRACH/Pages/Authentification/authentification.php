<?php
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Initialiser les variables d'erreur
$errors = [];
$num_auth_error = '';
$date_depo_error = '';
$date_auth_error = '';
$file_error = '';
$form_submitted = false;
$show_sweet_alert = false;
$pdf_only_error = ''; // Nouvelle variable pour l'erreur de type de fichier

// Récupérer l'ID de la garantie depuis le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['garantie_id'])) {
    $garantie_id = $_POST['garantie_id'];
} else if (isset($_GET['garantie_id'])) {
    $garantie_id = $_GET['garantie_id'];
} else {
    die("ID de garantie non fourni.");
}

// Traitement du formulaire d'authentification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_auth'])) {
    $form_submitted = true;
    $num_auth = trim($_POST['num_auth'] ?? '');
    $date_depo = trim($_POST['date_depo'] ?? '');
    $date_auth = trim($_POST['date_auth'] ?? '');
    $original_filename = '';

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

    // Validation du numéro d'authentification
    if (empty($num_auth)) {
        $num_auth_error = 'Ce champ est obligatoire.';
        $errors[] = $num_auth_error;
    } else {
        // Vérifier l'unicité du numéro d'authentification
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM authentification WHERE num_auth = :num_auth");
        $stmt->bindParam(':num_auth', $num_auth, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $num_auth_error = 'Ce numéro d\'authentification existe déjà.';
            $errors[] = $num_auth_error;
        }
    }

    // Validation de la date de dépôt
    if (empty($date_depo)) {
        $date_depo_error = 'Ce champ est obligatoire.';
        $errors[] = $date_depo_error;
    }

    // Validation de la date d'authentification
    if (empty($date_auth)) {
        $date_auth_error = 'Ce champ est obligatoire.';
        $errors[] = $date_auth_error;
    } else if (strtotime($date_auth) < strtotime($date_depo)) {
        $date_auth_error = 'La date d\'authentification doit être postérieure à la date de dépôt.';
        $errors[] = $date_auth_error;
    }

    // Validation du fichier
    if (!isset($_FILES['document_scanne']) || $_FILES['document_scanne']['error'] == UPLOAD_ERR_NO_FILE) {
        $file_error = 'Ce champ est obligatoire.';
        $errors[] = $file_error;
    } else {
        // Vérifier l'unicité du nom de fichier
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM document_auth WHERE nom_document = :nom_document");
        $stmt->bindParam(':nom_document', $original_filename, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $file_error = 'Un document avec ce nom existe déjà.';
            $errors[] = $file_error;
        }
    }

    // Si aucune erreur, traiter le formulaire
    if (empty($errors)) {
        try {
            // Insert into authentification table
            $sql_auth = "INSERT INTO authentification (num_auth, date_depo, date_auth, garantie_id) 
                         VALUES (:num_auth, :date_depo, :date_auth, :garantie_id)";
            $stmt_auth = $pdo->prepare($sql_auth);
            $stmt_auth->bindParam(':num_auth', $num_auth, PDO::PARAM_STR);
            $stmt_auth->bindParam(':date_depo', $date_depo, PDO::PARAM_STR);
            $stmt_auth->bindParam(':date_auth', $date_auth, PDO::PARAM_STR);
            $stmt_auth->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
            $stmt_auth->execute();

            // Récupérer l'ID de l'authentification insérée
            $auth_id = $pdo->lastInsertId();

            // Traiter le fichier si présent
            if (isset($_FILES['document_scanne']) && $_FILES['document_scanne']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/documents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = basename($_FILES['document_scanne']['name']);
                $file_path = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['document_scanne']['tmp_name'], $file_path)) {
                    // Insérer les informations du document dans la base de données
                    $sql_doc = "INSERT INTO document_auth (authentification_id, nom_document, document_path) 
                                VALUES (:auth_id, :nom_document, :document_path)";
                    $stmt_doc = $pdo->prepare($sql_doc);
                    $stmt_doc->bindParam(':auth_id', $auth_id, PDO::PARAM_INT);
                    $stmt_doc->bindParam(':nom_document', $filename, PDO::PARAM_STR);
                    $stmt_doc->bindParam(':document_path', $file_path, PDO::PARAM_STR);
                    $stmt_doc->execute();
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

// Vérifier si une authentification existe déjà pour cette garantie
$sql_auth = "SELECT a.*, d.nom_document, d.document_path 
            FROM authentification a
            LEFT JOIN document_auth d ON a.id = d.authentification_id
            WHERE a.garantie_id = :garantie_id";
$stmt_auth = $pdo->prepare($sql_auth);
$stmt_auth->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
$stmt_auth->execute();
$authentification = $stmt_auth->fetch(PDO::FETCH_ASSOC);

// Déterminer si on affiche le formulaire ou les informations d'authentification
$show_form = !$authentification;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification de Garantie</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="./css/authentificaton.css">
</head>
<style></style>
<body>
    <main id="dynamic-content">
        <div class="container">
            <!-- Titre Principal -->
            <h2 class="main-title">
                <i class='bx bx-shield-quarter'></i> Authentification de départ pour la garantie de soumission N° <?= htmlspecialchars($garantie['num_garantie']) ?>
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

            <!-- Formulaire d'Authentification ou Informations d'authentification -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-subtitle">
                        <i class='bx bx-shield'></i> Authentification de la garantie N° <?= htmlspecialchars($garantie['num_garantie']) ?>
                    </h5>
                    <hr class="divider">
                    <?php if (!$show_form): ?>
                        <!-- Affichage des informations d'authentification -->
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class='bx bx-hash'></i> N° Authentification:</strong> <span class="detail-value"><?= htmlspecialchars($authentification['num_auth']) ?></span></p>
                                <p><strong><i class='bx bx-calendar'></i> Date Dépôt:</strong> <span class="detail-value"><?= htmlspecialchars($authentification['date_depo']) ?></span></p>
                                <p><strong><i class='bx bx-calendar-check'></i> Date Authentification:</strong> <span class="detail-value"><?= htmlspecialchars($authentification['date_auth']) ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class='bx bx-upload'></i> Documents Scannés:</strong> 
                                    <?php if (!empty($authentification['document_path'])): ?>
                                        <a href="<?= htmlspecialchars($authentification['document_path']) ?>" target="_blank">
                                            <?= htmlspecialchars($authentification['nom_document'] ?? 'Télécharger') ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="detail-value">Aucun document disponible</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Back Button After Validation -->
                        <div class="text-center mt-4">
                            <a href="../Garantie/ListeGaranties.php" class="btn sec">
                                <i class='bx bx-arrow-back'></i> Retour à la liste des garanties
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Formulaire d'authentification -->
                        <form method="POST" action="" enctype="multipart/form-data" id="auth-form">
                            <input type="hidden" name="garantie_id" value="<?= $garantie['garantie_id'] ?>">
                            <!-- Première ligne du formulaire -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="num_auth" class="form-label">
                                            <i class='bx bx-hash'></i> N° Authentification <span class="required-asterisk" style="color: red;">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="num_auth" name="num_auth" value="<?= $form_submitted ? htmlspecialchars($_POST['num_auth'] ?? '') : '' ?>">
                                        <span id="num_auth_error" class="error-message"><?= $num_auth_error ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_depo" class="form-label">
                                            <i class='bx bx-calendar'></i> Date Dépôt<span class="required-asterisk" style="color: red;">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="date_depo" name="date_depo" value="<?= $form_submitted ? htmlspecialchars($_POST['date_depo'] ?? '') : date('Y-m-d') ?>">
                                        <span id="date_depo_error" class="error-message"><?= $date_depo_error ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_auth" class="form-label">
                                            <i class='bx bx-calendar-check'></i> Date Authentification<span class="required-asterisk" style="color: red;">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="date_auth" name="date_auth" value="<?= $form_submitted ? htmlspecialchars($_POST['date_auth'] ?? '') : date('Y-m-d') ?>">
                                        <span id="date_auth_error" class="error-message"><?= $date_auth_error ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Deuxième ligne du formulaire -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="document_scanne" class="form-label">
                                            <i class='bx bx-upload'></i> Documents Scannés<span class="required-asterisk" style="color: red;">*</span>
                                        </label>
                                        <!-- Placer l'input file en dehors de la zone de dépôt -->
                                        <input type="file" class="form-control" id="document_scanne" name="document_scanne" accept=".pdf" style="display: none;">
                                        <div class="custom-file-input" id="file-drop-area">
                                            <i class='bx bx-upload'></i>
                                            <span class="file-label">Cliquez ici pour sélectionner un fichier</span>
                                            <span class="file-selected" id="file-selected-name"></span>
                                        </div>
                                        <span id="nom_document_error" class="error-message"><?= $file_error ?></span>
                                        <span id="pdf_only_error" class="error-message" style="color: red;"><?= $pdf_only_error ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Boutons d'action -->
                            <div class="action-buttons d-flex justify-content-between mt-4">
                                <button type="submit" name="submit_auth" class="btn btn-primary">
                                    <i class='bx bx-check-circle'></i> Valider l'authentification
                                </button>
                                <a href="../Garantie/ListeGaranties.php" class="btn btn-primary">
                                    <i class='bx bx-arrow-back'></i> Retour à la liste des garanties
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
    const numAuthInput = document.getElementById('num_auth');
    const fileInput = document.getElementById('document_scanne');
    const numAuthError = document.getElementById('num_auth_error');
    const nomDocumentError = document.getElementById('nom_document_error');
    const pdfOnlyError = document.getElementById('pdf_only_error');
    const fileSelectedName = document.getElementById('file-selected-name');
    const fileDropArea = document.getElementById('file-drop-area');
    const dateDepoInput = document.getElementById('date_depo');
    const dateAuthInput = document.getElementById('date_auth');
    const dateDepoError = document.getElementById('date_depo_error');
    const dateAuthError = document.getElementById('date_auth_error');
    const form = document.getElementById('auth-form');

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
        fetch('check_uniqueness.php', {
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

    // Vérifier l'unicité du numéro d'authentification
    if (numAuthInput) {
        numAuthInput.addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                checkUniqueness('num_auth', value, numAuthError);
            } else {
                numAuthError.textContent = 'Ce champ est obligatoire.';
                numAuthError.style.color = 'red';
            }
        });
    }

    // Gérer la sélection de fichier
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                const fileExtension = fileName.split('.').pop().toLowerCase();

                // Vérifier si le fichier est un PDF
                if (fileExtension !== 'pdf') {
                    pdfOnlyError.textContent = 'Seuls les fichiers PDF sont acceptés.';
                    pdfOnlyError.style.color = 'red';
                } else {
                    pdfOnlyError.textContent = '';
                }

                // Afficher le nom du fichier sélectionné
                if (fileSelectedName) {
                    fileSelectedName.textContent = 'Fichier sélectionné: ' + fileName;
                    fileSelectedName.style.display = 'block';
                }

                // Vérifier l'unicité du nom de fichier
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

    // Vérifier la cohérence des dates en temps réel
    if (dateDepoInput && dateAuthInput) {
        function checkDates() {
            const dateDepo = dateDepoInput.value;
            const dateAuth = dateAuthInput.value;
            if (dateDepo && dateAuth) {
                if (new Date(dateAuth) < new Date(dateDepo)) {
                    dateAuthError.textContent = 'La date d\'authentification doit être postérieure à la date de dépôt.';
                    dateAuthError.style.color = 'red';
                } else {
                    dateAuthError.textContent = '';
                }
            }
        }
        dateDepoInput.addEventListener('change', checkDates);
        dateAuthInput.addEventListener('change', checkDates);
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
        text: 'Garantie authentifiée avec succès',
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

            // Vérifier le numéro d'authentification
            if (!numAuthInput.value.trim()) {
                numAuthError.textContent = 'Ce champ est obligatoire.';
                numAuthError.style.color = 'red';
                hasErrors = true;
            }

            // Vérifier le fichier
            if (!fileInput.files.length) {
                nomDocumentError.textContent = 'Ce champ est obligatoire.';
                nomDocumentError.style.color = 'red';
                hasErrors = true;
            }

            // Vérifier les dates
            if (!dateDepoInput.value) {
                dateDepoError.textContent = 'Ce champ est obligatoire.';
                dateDepoError.style.color = 'red';
                hasErrors = true;
            }

            if (!dateAuthInput.value) {
                dateAuthError.textContent = 'Ce champ est obligatoire.';
                dateAuthError.style.color = 'red';
                hasErrors = true;
            } else if (dateDepoInput.value && new Date(dateAuthInput.value) < new Date(dateDepoInput.value)) {
                dateAuthError.textContent = 'La date d\'authentification doit être postérieure à la date de dépôt.';
                dateAuthError.style.color = 'red';
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