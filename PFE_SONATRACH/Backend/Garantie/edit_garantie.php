<?php
// Démarrer la session
session_start();
require_once("../../Pages/Template/header.php");

// Inclure la connexion à la base de données
require_once('../../db_connection/db_conn.php');

// Vérifier si l'ID est fourni
if (!isset($_POST['garantie_id']) && !isset($_GET['id'])) {
    $_SESSION['error_message'] = "ID de garantie non spécifié.";
    header('Location: ../../Pages/Garantie/ListeGaranties.php');
    exit;
}

// Récupérer l'ID de la garantie
$garantie_id = isset($_POST['garantie_id']) ? intval($_POST['garantie_id']) : intval($_GET['id']);

// Vérifier s'il y a des messages d'erreur ou de succès
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';

// Récupérer les données du formulaire en cas d'erreur
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Nettoyer les messages de session
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);

try {
    // Récupérer les informations de la garantie
    $sql = "SELECT * FROM garantie WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $garantie_id);
    $stmt->execute();
    
    $garantie = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$garantie) {
        $_SESSION['error_message'] = "Garantie non trouvée.";
        header('Location: ../../Pages/Garantie/ListeGaranties.php');
        exit;
    }
    
    // Récupérer le document associé
    $stmt_doc = $pdo->prepare("SELECT * FROM document_garantie WHERE garantie_id = :garantie_id");
    $stmt_doc->bindParam(':garantie_id', $garantie_id);
    $stmt_doc->execute();
    $document = $stmt_doc->fetch(PDO::FETCH_ASSOC);
    
    // Récupérer toutes les directions pour le dropdown
    $stmt = $pdo->prepare("SELECT id, code, libelle FROM direction ORDER BY libelle");
    $stmt->execute();
    $directions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer toutes les agences
    $stmt = $pdo->prepare("SELECT a.*, b.label as banque_label 
                          FROM agence a 
                          LEFT JOIN banque b ON a.banque_id = b.id 
                          ORDER BY a.label");
    $stmt->execute();
    $agences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer toutes les banques
    $stmt = $pdo->prepare("SELECT id, code, label FROM banque ORDER BY label");
    $stmt->execute();
    $banques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les monnaies
    $stmt = $pdo->prepare("SELECT id, symbole, label FROM monnaie");
    $stmt->execute();
    $monnaies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les fournisseurs
    $stmt = $pdo->prepare("SELECT id, nom_fournisseur FROM fournisseur");
    $stmt->execute();
    $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les appels d'offre
    $stmt = $pdo->prepare("SELECT id, num_appel_offre FROM appel_offre");
    $stmt->execute();
    $appel_offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    <title>Modifier la Garantie - <?php echo htmlspecialchars($garantie['num_garantie']); ?></title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="editGarantie.css">
</head>
<body>
    <div class="main-container">
        <div class="title">
            <i class='bx bx-shield'></i>
            Gestion des Garanties Bancaires
        </div>
        
        <div class="page">
            <div class="form-container">
                <div class="form-title">
                    <i class='bx bx-edit'></i>
                    Modifier la garantie bancaire
                </div>
                
                <!-- Message d'alerte pour les erreurs/succès -->
                <div id="alertContainer">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class='bx bx-error-circle'></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <i class='bx bx-check-circle'></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <form id="garantieForm" method="POST" action="../../Backend/Garantie/save_garantie.php" enctype="multipart/form-data">
                    <input type="hidden" name="edit_garantie" value="1">
                    <input type="hidden" name="garantie_id" value="<?php echo $garantie_id; ?>">
                    <?php if ($document): ?>
                    <input type="hidden" name="document_id" value="<?php echo $document['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <!-- Première colonne -->
                        <div class="form-column">
                            <div class="form-group">
                                <label for="num_garantie">Numéro de Garantie<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-hash'></i>
                                    <input type="text" id="num_garantie" name="num_garantie" class="form-control" value="<?php echo htmlspecialchars($garantie['num_garantie']); ?>">
                                </div>
                                <span class="error" id="num_garantieError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="montant">Montant<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-money'></i>
                                    <input type="text" id="montant" name="montant" class="form-control" value="<?php echo htmlspecialchars($garantie['montant']); ?>">
                                </div>
                                <span class="error" id="montantError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="monnaie_id">Monnaie<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-dollar-circle'></i>
                                    <select id="monnaie_id" name="monnaie_id" class="form-control">
                                        <option value="">Sélectionnez une monnaie</option>
                                        <?php
                                        if (!empty($monnaies)) {
                                            foreach ($monnaies as $monnaie) {
                                                $selected = ($garantie['monnaie_id'] == $monnaie['id']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($monnaie['id']) . "' $selected>" . 
                                                    htmlspecialchars($monnaie['symbole'] . ' - ' . $monnaie['label']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="error" id="monnaie_idError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="banque_select">Banque<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-bank'></i>
                                    <select id="banque_select" class="form-control">
                                        <option value="">Sélectionnez une banque</option>
                                        <?php
                                        if (!empty($banques)) {
                                            // Récupérer la banque associée à l'agence sélectionnée
                                            $banque_id = null;
                                            if (!empty($garantie['agence_id'])) {
                                                foreach ($agences as $agence) {
                                                    if ($agence['id'] == $garantie['agence_id']) {
                                                        $banque_id = $agence['banque_id'];
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            foreach ($banques as $banque) {
                                                $selected = ($banque_id == $banque['id']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($banque['id']) . "' $selected>" . 
                                                    htmlspecialchars($banque['label']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="error" id="banque_selectError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="direction_id">Direction<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-building'></i>
                                    <select id="direction_id" name="direction_id" class="form-control">
                                        <option value="">Sélectionnez une direction</option>
                                        <?php
                                        if (!empty($directions)) {
                                            foreach ($directions as $direction) {
                                                $selected = ($garantie['direction_id'] == $direction['id']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($direction['id']) . "' $selected>" . 
                                                    htmlspecialchars($direction['libelle']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="error" id="direction_idError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="agence_id">Agence<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-buildings'></i>
                                    <select id="agence_id" name="agence_id" class="form-control">
                                        <option value="">Sélectionnez une agence</option>
                                        <?php
                                        if (!empty($agences)) {
                                            foreach ($agences as $agence) {
                                                $selected = ($garantie['agence_id'] == $agence['id']) ? 'selected' : '';
                                                $direction_id = isset($agence['direction_id']) ? htmlspecialchars($agence['direction_id']) : '';
                                                $banque_id = isset($agence['banque_id']) ? htmlspecialchars($agence['banque_id']) : '';
                                                $adresse = isset($agence['adresse']) ? htmlspecialchars($agence['adresse']) : '';
                                                
                                                echo "<option value='" . htmlspecialchars($agence['id']) . "' 
                                                data-direction='" . $direction_id . "' 
                                                data-banque='" . $banque_id . "' $selected>" . 
                                                htmlspecialchars($agence['label']) . " - " . $adresse . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="error" id="agence_idError"></span>
                            </div>
                        </div>
                        
                        <!-- Deuxième colonne -->
                        <div class="form-column">
                            <div class="form-group">
                                <label for="fournisseur_id">Fournisseur<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-store'></i>
                                    <select id="fournisseur_id" name="fournisseur_id" class="form-control">
                                        <option value="">Sélectionnez un fournisseur</option>
                                        <?php
                                        if (!empty($fournisseurs)) {
                                            foreach ($fournisseurs as $fournisseur) {
                                                $selected = ($garantie['fournisseur_id'] == $fournisseur['id']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($fournisseur['id']) . "' $selected>" . 
                                                    htmlspecialchars($fournisseur['nom_fournisseur']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="error" id="fournisseur_idError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_creation">Date de Création<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-calendar-plus'></i>
                                    <input type="date" id="date_creation" name="date_creation" class="form-control" value="<?php echo htmlspecialchars($garantie['date_creation']); ?>">
                                </div>
                                <span class="error" id="date_creationError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_emission">Date d'Émission<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-calendar-check'></i>
                                    <input type="date" id="date_emission" name="date_emission" class="form-control" value="<?php echo htmlspecialchars($garantie['date_emission']); ?>">
                                </div>
                                <span class="error" id="date_emissionError"></span>
                            </div>
                            
                            <!-- Champ pour la période de validité -->
                            <div class="form-group">
                                <label for="periode_validite">Période de Validité<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-time'></i>
                                    <select id="periode_validite" name="periode_validite" class="form-control">
                                        <option value="">Sélectionnez une période</option>
                                        <option value="30">30 jours</option>
                                        <option value="60">60 jours</option>
                                        <option value="90">90 jours</option>
                                        <option value="180">180 jours (6 mois)</option>
                                        <option value="365">365 jours (1 an)</option>
                                        <option value="custom" selected>Personnalisée</option>
                                    </select>
                                </div>
                                <span class="error" id="periode_validiteError"></span>
                            </div>
                            
                            <div class="form-group" id="date_validite_group">
                                <label for="date_validite">Date de Validité<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-calendar-exclamation'></i>
                                    <input type="date" id="date_validite" name="date_validite" class="form-control" value="<?php echo htmlspecialchars($garantie['date_validite']); ?>">
                                </div>
                                <span class="error" id="date_validiteError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="appel_offre_id">Appel d'Offre<span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class='bx bx-file'></i>
                                    <select id="appel_offre_id" name="appel_offre_id" class="form-control">
                                        <option value="">Sélectionnez un appel d'offre</option>
                                        <?php
                                        if (!empty($appel_offres)) {
                                            foreach ($appel_offres as $offre) {
                                                $selected = (isset($garantie['appel_offre_id']) && $garantie['appel_offre_id'] == $offre['id']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($offre['id']) . "' $selected>" . 
                                                    htmlspecialchars($offre['num_appel_offre']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span class="error" id="appel_offre_idError"></span>
                            </div>
                            
                            <!-- Champ pour télécharger un PDF -->
                            <div class="form-group">
                                <label for="document_pdf">
                                    Document PDF
                                    <?php if (!$document): ?>
                                    <span class="required">*</span>
                                    <?php endif; ?>
                                </label>
                                <div class="file-upload-container">
                                    <div class="file-upload-button">
                                        <i class='bx bx-upload'></i>
                                        <span>Choisir un fichier</span>
                                    </div>
                                    <input type="file" id="document_pdf" name="document_pdf" class="file-upload-input" accept=".pdf">
                                    <div class="file-name" id="file-name-display">
                                        <?php echo $document ? htmlspecialchars($document['nom_document']) : 'Aucun fichier sélectionné'; ?>
                                    </div>
                                </div>
                                <span class="error" id="document_pdfError"></span>
                                
                                <?php if ($document): ?>
                                <div class="existing-document">
                                    <p>Document actuel: <a href="view_document.php?id=<?php echo $document['id']; ?>" target="_blank"><?php echo htmlspecialchars($document['nom_document']); ?></a></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                   
              
                    
                    <div class="form-actions">
                        <a href="../../Pages/Garantie/ListeGaranties.php" class="btn btn-cancel">
                            <i class='bx bx-x'></i> Annuler
                        </a>
                       
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-save'></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- PDF.js pour l'aperçu des PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script>
        // Stocker les agences pour le filtrage
        const agences = <?php echo json_encode($agences); ?>;
        
        // Afficher les données pour le débogage
        console.log("Agences:", agences);
        console.log("Nombre d'agences:", agences.length);
        
        // Définir l'ID de la garantie pour les vérifications AJAX
        const garantieId = <?php echo $garantie_id; ?>;
    </script>
    <script src="../../PagesAttachment/add_gar.js"></script>
</body>
</html>
<?php
 require_once('../../Pages/Template/footer.php');

?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
  // Éléments du formulaire
  const garantieForm = document.getElementById("garantieForm")
  const numGarantie = document.getElementById("num_garantie")
  const montant = document.getElementById("montant")
  const dateCreation = document.getElementById("date_creation")
  const dateEmission = document.getElementById("date_emission")
  const dateValidite = document.getElementById("date_validite")
  const periodeValidite = document.getElementById("periode_validite")
  const documentPdf = document.getElementById("document_pdf")
  const fileNameDisplay = document.getElementById("file-name-display")
  const previewPdfBtn = document.getElementById("preview-pdf-btn")
  const pdfPreviewContainer = document.getElementById("pdf-preview-container")
  const pdfPreview = document.getElementById("pdf-preview")
  const closePreview = document.getElementById("close-preview")
  const directionSelect = document.getElementById("direction_id")
  const agenceSelect = document.getElementById("agence_id")
  const banqueSelect = document.getElementById("banque_select")

  // Variable pour stocker le timer de vérification du numéro de garantie
  let numGarantieCheckTimer

  // Définir la date du jour comme valeur par défaut pour la date de création
  const today = new Date()
  const formattedDate = today.toISOString().split("T")[0]
  if (dateCreation && !dateCreation.value) dateCreation.value = formattedDate
  if (dateEmission && !dateEmission.value) dateEmission.value = formattedDate

  // Définir la date de validité par défaut (1 an après la date de création)
  const nextYear = new Date()
  nextYear.setFullYear(nextYear.getFullYear() + 1)
  if (dateValidite && !dateValidite.value) dateValidite.value = nextYear.toISOString().split("T")[0]

  // Générer un numéro de garantie par défaut si vide
  if (numGarantie && !numGarantie.value) {
    const year = today.getFullYear()
    const randomNum = Math.floor(1000 + Math.random() * 9000) // Nombre aléatoire à 4 chiffres
    numGarantie.value = `GB-${year}-${randomNum}`
  }

  // Ajouter des effets visuels aux champs de formulaire
  addFormFieldEffects()

  // Vérification AJAX pour le numéro de garantie
  if (numGarantie) {
    numGarantie.addEventListener("input", () => {
      // Annuler le timer précédent
      clearTimeout(numGarantieCheckTimer)

      // Définir un nouveau timer pour éviter trop de requêtes
      numGarantieCheckTimer = setTimeout(() => {
        const value = numGarantie.value.trim()

        if (value.length > 3) {
          // Vérifier seulement si la longueur est suffisante
          checkNumGarantieExists(value)
        }
      }, 500) // Délai de 500ms avant de lancer la requête
    })
  }

  /**
   * Vérifier si un numéro de garantie existe déjà dans la base de données
   */
  function checkNumGarantieExists(numGarantie) {
    // Afficher un indicateur de chargement
    const errorElement = document.getElementById("num_garantieError")
    if (!errorElement) return

    errorElement.innerHTML = '<i class="bx bx-loader-circle bx-spin"></i> Vérification en cours...'
    errorElement.style.color = "var(--info)"

    // Récupérer l'ID de la garantie pour l'exclusion (en cas de modification)
    const garantieId = typeof window.garantieId !== "undefined" ? window.garantieId : 0

    // Effectuer la requête AJAX
    fetch(`../../Backend/Garantie/save_garantie.php?check_num_garantie=${encodeURIComponent(numGarantie)}&exclude_id=${garantieId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.exists) {
          // Le numéro existe déjà
          errorElement.textContent = data.message
          errorElement.style.color = "red"
          document.getElementById("num_garantie").classList.add("error-input")
        } else {
          // Le numéro est disponible
          errorElement.textContent = data.message
          errorElement.style.color = "var(--success)"
          document.getElementById("num_garantie").classList.remove("error-input")

          // Faire disparaître le message après 3 secondes
          setTimeout(() => {
            errorElement.textContent = ""
          }, 3000)
        }
      })
      .catch((error) => {
        console.error("Erreur lors de la vérification:", error)
        errorElement.textContent = "Erreur lors de la vérification. Veuillez réessayer."
        errorElement.style.color = "red"
      })
  }

  /**
   * Vérifier si un document existe déjà dans la base de données
   */
  function checkDocumentExists(fileName) {
    // Afficher un indicateur de chargement
    const errorElement = document.getElementById("document_pdfError")
    if (!errorElement) return

    errorElement.innerHTML = '<i class="bx bx-loader-circle bx-spin"></i> Vérification en cours...'
    errorElement.style.color = "var(--info)"

    // Récupérer l'ID du document pour l'exclusion (en cas de modification)
    const documentId = document.querySelector('input[name="document_id"]')
      ? document.querySelector('input[name="document_id"]').value
      : 0

    // Effectuer la requête AJAX
    fetch(`../../Backend/Garantie/save_garantie.php?check_document=${encodeURIComponent(fileName)}&exclude_id=${documentId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.exists) {
          // Le document existe déjà
          errorElement.textContent = data.message
          errorElement.style.color = "red"
          documentPdf.classList.add("error-input")
          return false
        } else {
          // Le document est disponible
          errorElement.textContent = ""
          documentPdf.classList.remove("error-input")
          return true
        }
      })
      .catch((error) => {
        console.error("Erreur lors de la vérification:", error)
        errorElement.textContent = "Erreur lors de la vérification. Veuillez réessayer."
        errorElement.style.color = "red"
        return false
      })
  }

  // Gestion du téléchargement de fichier PDF
  if (documentPdf) {
    documentPdf.addEventListener("change", (e) => {
      const file = e.target.files[0]
      if (file) {
        // Vérifier si le fichier est un PDF
        if (file.type !== "application/pdf") {
          documentPdf.value = ""
          fileNameDisplay.textContent = "Aucun fichier sélectionné"
          previewPdfBtn.style.display = "none"

          // Afficher l'erreur sous le champ
          const errorElement = document.getElementById("document_pdfError")
          if (errorElement) {
            errorElement.textContent = "Veuillez sélectionner un fichier PDF valide."
            errorElement.style.color = "red"
          }
          return
        }

        // Vérifier la taille du fichier (max 5 MB)
        if (file.size > 5 * 1024 * 1024) {
          documentPdf.value = ""
          fileNameDisplay.textContent = "Aucun fichier sélectionné"
          previewPdfBtn.style.display = "none"

          // Afficher l'erreur sous le champ
          const errorElement = document.getElementById("document_pdfError")
          if (errorElement) {
            errorElement.textContent = "Le fichier est trop volumineux. La taille maximale est de 5 MB."
            errorElement.style.color = "red"
          }
          return
        }

        // Vérifier si le document existe déjà
        checkDocumentExists(file.name)

        // Afficher le nom du fichier
        fileNameDisplay.textContent = file.name
        fileNameDisplay.title = file.name

        // Activer le bouton d'aperçu
        previewPdfBtn.style.display = "inline-flex"
      } else {
        fileNameDisplay.textContent = "Aucun fichier sélectionné"
        previewPdfBtn.style.display = "none"
      }
    })
  }

  // Gestion de l'aperçu PDF
  if (previewPdfBtn) {
    previewPdfBtn.addEventListener("click", () => {
      const file = documentPdf.files[0]
      if (file) {
        const fileReader = new FileReader()

        fileReader.onload = function () {
          const typedarray = new Uint8Array(this.result)

          // Utiliser PDF.js pour afficher le PDF
          if (typeof pdfjsLib !== "undefined") {
            pdfjsLib
              .getDocument(typedarray)
              .promise.then((pdf) => {
                // Afficher la première page
                pdf.getPage(1).then((page) => {
                  const viewport = page.getViewport({ scale: 1.5 })

                  // Préparer le canvas pour le rendu
                  const canvas = document.createElement("canvas")
                  const context = canvas.getContext("2d")
                  canvas.height = viewport.height
                  canvas.width = viewport.width

                  // Vider le conteneur d'aperçu
                  pdfPreview.innerHTML = ""
                  pdfPreview.appendChild(canvas)

                  // Afficher le conteneur d'aperçu
                  pdfPreviewContainer.style.display = "block"

                  // Rendre la page PDF
                  page.render({
                    canvasContext: context,
                    viewport: viewport,
                  })
                })
              })
              .catch((error) => {
                console.error("Erreur lors du chargement du PDF:", error)
                const errorElement = document.getElementById("document_pdfError")
                if (errorElement) {
                  errorElement.textContent =
                    "Impossible d'afficher l'aperçu du PDF. Veuillez vérifier que le fichier est valide."
                  errorElement.style.color = "red"
                }
              })
          } else {
            const errorElement = document.getElementById("document_pdfError")
            if (errorElement) {
              errorElement.textContent = "La bibliothèque PDF.js n'est pas chargée."
              errorElement.style.color = "red"
            }
          }
        }

        fileReader.readAsArrayBuffer(file)
      }
    })
  }

  // Fermer l'aperçu PDF
  if (closePreview) {
    closePreview.addEventListener("click", () => {
      pdfPreviewContainer.style.display = "none"
    })
  }

  // Gestion de la période de validité
  if (periodeValidite && dateEmission && dateValidite) {
    periodeValidite.addEventListener("change", function () {
      const selectedValue = this.value

      if (selectedValue && selectedValue !== "custom") {
        // Calculer la date de validité en fonction de la période sélectionnée
        const days = Number.parseInt(selectedValue)
        const validityDate = new Date(dateEmission.value)
        validityDate.setDate(validityDate.getDate() + days)

        // Mettre à jour la date de validité
        dateValidite.value = validityDate.toISOString().split("T")[0]
      }
    })
  }

  // Validation du formulaire
  if (garantieForm) {
    garantieForm.addEventListener("submit", (e) => {
      // Empêcher la soumission par défaut pour valider d'abord
      e.preventDefault()

      // Vérifier tous les champs avant la soumission
      if (checkAllFields()) {
        // Si tout est valide, afficher une confirmation avant de soumettre
        if (typeof Swal !== "undefined") {
          const isEdit = garantieForm.querySelector('input[name="edit_garantie"]') !== null
          const actionText = isEdit ? "modifier" : "ajouter"

          Swal.fire({
            title: `Confirmation`,
            text: `Êtes-vous sûr de vouloir ${actionText} cette garantie ?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "var(--primary)",
            cancelButtonColor: "var(--secondary)",
            confirmButtonText: "Oui, confirmer",
            cancelButtonText: "Annuler",
          }).then((result) => {
            if (result.isConfirmed) {
              // Soumettre le formulaire
              garantieForm.submit()
            }
          })
        } else {
          // Fallback si SweetAlert n'est pas disponible
          if (confirm("Êtes-vous sûr de vouloir enregistrer cette garantie ?")) {
            garantieForm.submit()
          }
        }
      } else {
        // Afficher un message d'erreur global
        if (typeof Swal !== "undefined") {
          Swal.fire({
            icon: "error",
            title: "Erreur de validation",
            text: "Veuillez corriger les erreurs dans le formulaire avant de continuer.",
            confirmButtonColor: "var(--primary)",
          })
        } else {
          alert("Veuillez corriger les erreurs dans le formulaire avant de continuer.")
        }

        // Faire défiler jusqu'à la première erreur
        const firstError = document.querySelector(".error:not(:empty)")
        if (firstError) {
          firstError.scrollIntoView({ behavior: "smooth", block: "center" })
        }
      }
    })
  }

  // Vérifier tous les champs du formulaire
  function checkAllFields() {
    let isValid = true

    // Liste des champs à vérifier
    const fieldsToCheck = [
      "num_garantie",
      "montant",
      "monnaie_id",
      "direction_id",
      "fournisseur_id",
      "date_creation",
      "date_emission",
      "date_validite",
      "agence_id",
      "appel_offre_id",
    ]

    // Ajouter document_pdf seulement s'il est requis (pas de document existant)
    const existingDocument = document.querySelector(".existing-document")
    if (!existingDocument) {
      fieldsToCheck.push("document_pdf")
    }

    // Vérifier chaque champ
    fieldsToCheck.forEach((fieldId) => {
      const field = document.getElementById(fieldId)
      if (field) {
        if (!checkField(field)) {
          isValid = false
        }
      }
    })

    // Vérifier les dates
    if (!validateDates()) {
      isValid = false
    }

    return isValid
  }

  // Vérifier un champ individuel
  function checkField(field) {
    const errorElement = document.getElementById(field.id + "Error")
    if (!errorElement) return true

    let isValid = true

    // Vérifier si le champ est vide
    if (field.value.trim() === "") {
      errorElement.textContent = "Ce champ est obligatoire"
      errorElement.style.color = "red"
      field.classList.add("error-input")
      isValid = false
    } else {
      // Validations spécifiques selon le type de champ
      if (field.id === "montant") {
        const montantValue = field.value.replace(/\s/g, "").replace(/,/g, ".")
        if (isNaN(Number.parseFloat(montantValue)) || Number.parseFloat(montantValue) <= 0) {
          errorElement.textContent = "Le montant doit être un nombre positif"
          errorElement.style.color = "red"
          field.classList.add("error-input")
          isValid = false
        }
      } else if (field.id === "document_pdf" && field.files.length === 0) {
        // Vérifier si un document existant est présent
        const existingDocument = document.querySelector(".existing-document")
        if (!existingDocument) {
          errorElement.textContent = "Veuillez sélectionner un fichier PDF"
          errorElement.style.color = "red"
          field.classList.add("error-input")
          isValid = false
        }
      }

      if (isValid) {
        errorElement.textContent = ""
        field.classList.remove("error-input")
      }
    }

    return isValid
  }

  // Validation en temps réel des champs
  document.querySelectorAll("input, select").forEach((input) => {
    // Vérifier à la perte de focus
    input.addEventListener("blur", function () {
      checkField(this)
    })

    // Effacer le message d'erreur quand l'utilisateur commence à taper
    input.addEventListener("input", function () {
      if (this.value.trim() !== "") {
        const errorElement = document.getElementById(this.id + "Error")
        if (errorElement) {
          errorElement.textContent = ""
        }
        this.classList.remove("error-input")
      }
    })
  })

  // Validation spécifique pour les dates
  if (dateEmission && dateCreation && dateValidite && periodeValidite) {
    dateEmission.addEventListener("change", function () {
      validateDates()

      // Mettre à jour la date de validité si une période est sélectionnée
      const selectedPeriod = periodeValidite.value
      if (selectedPeriod && selectedPeriod !== "custom") {
        const days = Number.parseInt(selectedPeriod)
        const validityDate = new Date(this.value)
        validityDate.setDate(validityDate.getDate() + days)

        // Mettre à jour la date de validité
        dateValidite.value = validityDate.toISOString().split("T")[0]
      }
    })

    dateValidite.addEventListener("change", () => {
      validateDates()

      // Si la date de validité est modifiée manuellement, passer à "Personnalisée"
      periodeValidite.value = "custom"
    })
  }

  /**
   * Valider les dates
   */
  function validateDates() {
    if (!dateCreation || !dateEmission || !dateValidite) return false

    let isValid = true
    const dateCreationValue = new Date(dateCreation.value)
    const dateEmissionValue = new Date(dateEmission.value)
    const dateValiditeValue = new Date(dateValidite.value)

    const dateEmissionError = document.getElementById("date_emissionError")
    const dateValiditeError = document.getElementById("date_validiteError")

    // Vérifier si les dates sont valides avant de comparer
    if (!isNaN(dateCreationValue) && !isNaN(dateEmissionValue) && dateEmissionValue < dateCreationValue) {
      if (dateEmissionError) {
        dateEmissionError.textContent = "La date d'émission ne peut pas être antérieure à la date de création"
        dateEmissionError.style.color = "red"
        dateEmission.classList.add("error-input")
      }
      isValid = false
    } else {
      if (dateEmissionError) {
        dateEmissionError.textContent = ""
        dateEmission.classList.remove("error-input")
      }
    }

    if (!isNaN(dateEmissionValue) && !isNaN(dateValiditeValue) && dateValiditeValue < dateEmissionValue) {
      if (dateValiditeError) {
        dateValiditeError.textContent = "La date de validité ne peut pas être antérieure à la date d'émission"
        dateValiditeError.style.color = "red"
        dateValidite.classList.add("error-input")
      }
      isValid = false
    } else {
      if (dateValiditeError) {
        dateValiditeError.textContent = ""
        dateValidite.classList.remove("error-input")
      }
    }

    return isValid
  }

  // Formater le montant avec séparateur de milliers
  if (montant) {
    montant.addEventListener("input", function (e) {
      // Autoriser uniquement les chiffres et certains caractères spéciaux
      let value = this.value
      value = value.replace(/[^0-9.,\s]/g, "") // Garder uniquement les chiffres, points, virgules et espaces
      this.value = value
    })

    montant.addEventListener("blur", function () {
      formatMontant(this)
      checkField(this)
    })

    montant.addEventListener("focus", function () {
      // Restaurer la valeur non formatée pour l'édition
      const value = this.value
      this.value = value.replace(/\s/g, "")
    })
  }

  // Fonctions

  /**
   * Ajouter des effets visuels aux champs de formulaire
   */
  function addFormFieldEffects() {
    // Ajouter des effets de survol et de focus
    document.querySelectorAll(".form-control").forEach((field) => {
      // Effet au survol
      field.addEventListener("mouseenter", function () {
        if (!this.classList.contains("error-input") && document.activeElement !== this) {
          this.style.borderColor = "var(--primary-light)"
        }
      })

      field.addEventListener("mouseleave", function () {
        if (!this.classList.contains("error-input") && document.activeElement !== this) {
          this.style.borderColor = "var(--grey-30)"
        }
      })

      // Effet au focus
      field.addEventListener("focus", function () {
        const label = this.closest(".form-group").querySelector("label")
        if (label) {
          label.style.color = "var(--primary)"
        }
      })

      field.addEventListener("blur", function () {
        const label = this.closest(".form-group").querySelector("label")
        if (label) {
          label.style.color = "var(--grey-80)"
        }
      })
    })

    // Animation d'entrée pour les champs
    document.querySelectorAll(".form-group").forEach((group, index) => {
      group.style.opacity = "0"
      group.style.transform = "translateY(20px)"
      group.style.transition = "opacity 0.3s ease, transform 0.3s ease"

      setTimeout(
        () => {
          group.style.opacity = "1"
          group.style.transform = "translateY(0)"
        },
        100 + index * 50,
      )
    })

    // Effet pour le bouton de téléchargement de fichier
    const fileUploadButton = document.querySelector(".file-upload-button")
    if (fileUploadButton) {
      fileUploadButton.addEventListener("click", () => {
        documentPdf.click()
      })
    }
  }

  /**
   * Formater le montant avec séparateur de milliers
   */
  function formatMontant(input) {
    if (!input || input.value.trim() === "") return

    // Nettoyer la valeur (garder uniquement les chiffres et la virgule/point décimal)
    const value = input.value.replace(/\s/g, "").replace(/\./g, "").replace(/,/g, ".")

    // Vérifier si c'est un nombre valide
    if (!isNaN(Number.parseFloat(value)) && isFinite(value)) {
      // Formater avec des espaces comme séparateurs de milliers (format français)
      const parts = value.split(".")
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ")

      // Reconstruire la valeur avec ou sans décimales
      input.value = parts.join(".")
    }
  }

  // Import PDF.js library
  let pdfjsLib
  if (typeof window["pdfjs-dist/build/pdf"] !== "undefined") {
    pdfjsLib = window["pdfjs-dist/build/pdf"]
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://mozilla.github.io/pdf.js/build/pdf.worker.js"
  }

  // Declare Swal if it's not already declared
  let Swal
  if (typeof window.Swal !== "undefined") {
    Swal = window.Swal
  } else {
    console.warn("SweetAlert2 is not loaded. Falling back to native alert.")
  }
})


</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fonction pour gérer la modification d'une garantie
    function handleEditGarantie() {
        // Gestion de la sélection en cascade banque -> agence
        const banqueSelect = document.getElementById('banque_select');
        const agenceSelect = document.getElementById('agence_id');
        
        if (banqueSelect && agenceSelect) {
            // Filtrer les agences au chargement de la page
            const initialBanqueId = banqueSelect.value;
            if (initialBanqueId) {
                filterAgencesByBanque(initialBanqueId);
            }
            
            // Ajouter un écouteur d'événement pour le changement de banque
            banqueSelect.addEventListener('change', function() {
                const banqueId = this.value;
                filterAgencesByBanque(banqueId);
            });
        }
        
        // Fonction pour filtrer les agences par banque
        function filterAgencesByBanque(banqueId) {
            // Sauvegarder l'agence sélectionnée actuelle
            const currentAgenceId = agenceSelect.value;
            
            // Vider le select des agences
            agenceSelect.innerHTML = '<option value="">Sélectionnez une agence</option>';
            
            if (banqueId) {
                // Filtrer les agences pour cette banque
                const filteredAgences = agences.filter(agence => agence.banque_id == banqueId);
                
                // Ajouter les agences filtrées au select
                filteredAgences.forEach(agence => {
                    const option = document.createElement('option');
                    option.value = agence.id;
                    option.textContent = agence.label + (agence.adresse ? ` - ${agence.adresse}` : '');
                    
                    // Restaurer la sélection si l'agence appartient à la banque sélectionnée
                    if (agence.id == currentAgenceId) {
                        option.selected = true;
                    }
                    
                    agenceSelect.appendChild(option);
                });
                
                // Si aucune agence n'est trouvée
                if (filteredAgences.length === 0) {
                    const option = document.createElement('option');
                    option.value = "";
                    option.textContent = "Aucune agence disponible pour cette banque";
                    agenceSelect.appendChild(option);
                }
            }
        }
        
        // Validation du formulaire
        const form = document.getElementById('garantieForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Vérifier si l'agence est sélectionnée
                const agenceSelect = document.getElementById('agence_id');
                if (agenceSelect && !agenceSelect.value) {
                    e.preventDefault();
                    const errorElement = document.getElementById('agence_idError');
                    if (errorElement) {
                        errorElement.textContent = "Veuillez sélectionner une agence";
                        errorElement.style.color = "red";
                        agenceSelect.classList.add("error-input");
                    }
                    return false;
                }
                
                // Vérifier si la banque est sélectionnée
                const banqueSelect = document.getElementById('banque_select');
                if (banqueSelect && !banqueSelect.value) {
                    e.preventDefault();
                    const errorElement = document.getElementById('banque_selectError');
                    if (errorElement) {
                        errorElement.textContent = "Veuillez sélectionner une banque";
                        errorElement.style.color = "red";
                        banqueSelect.classList.add("error-input");
                    }
                    return false;
                }
            });
        }
    }
    
    // Initialiser les fonctionnalités
    handleEditGarantie();
});
</script>
