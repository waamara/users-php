<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../Login/login.php");
    exit;
  }
require_once("../Template/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="css/banque.css">
    <title>Banques</title>
</head>
<body>

<div class="main-container">
    <div class="title">
        <i class='bx bx-building-house'></i>
        Gestion des Banques
    </div>
    
    <div class="page">
        <div class="form-container">
            <div class="form-title">
                <i class='bx bx-list-ul'></i>
                Liste des Banques
            </div>
            
            <!-- Message d'alerte pour les erreurs/succès -->
            <div id="alertContainer"></div>
            
            <!-- Barre d'action pour rechercher et ajouter -->
            <div class="action-bar">
                <!-- Recherche à gauche -->
                <div class="search-container">
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class='bx bx-search'></i>
                            <input type="text" id="searchInput" placeholder="Rechercher une banque..." class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- Bouton d'ajout à droite -->
                <div class="action-buttons">
                    <button id="addBanqueBtn" class="btn btn-primary">
                        <i class='bx bx-plus-circle'></i> Ajouter une Banque
                    </button>
                </div>
            </div>

            <div class="table-container slideUp">
                <!-- En-tête du tableau -->
                <div class="table-header">
                    <div class="header-number">#</div>
                    <div class="header-code">Code</div>
                    <div class="header-libelle">Désignation</div>
                    <div class="header-actions">Actions</div>
                </div>
                
                <ul class="directions-list" id="banquesData">
                    <!-- Les données seront chargées dynamiquement ici -->
                </ul>
                
                <!-- Pagination sera ajoutée dynamiquement si nécessaire -->
                <div class="pagination-container" id="paginationContainer" style="display: none;">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <ul class="pagination" id="pagination"></ul>
                </div>
            </div>
            
            <!-- Message pour aucun résultat de recherche -->
            <div class="no-results" id="noResults">
                <i class='bx bx-search'></i>
                <p>Aucune banque ne correspond à votre recherche.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter une banque -->
<div id="banqueModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="form-title">
            <i class='bx bx-plus-circle'></i>
            Ajouter une Banque
        </div>
        <form id="banqueForm">
            <input type="hidden" id="banqueId">
            <div class="form-group">
                <label for="code">Code<span class="required">*</span></label>
                <div class="input-with-icon">
                    <i class='bx bx-code-alt'></i>
                    <input type="text" id="code" name="code" class="form-control" maxlength="50" required>
                </div>
                <span class="error" id="codeError"></span>
            </div>
            <div class="form-group">
                <label for="designation">Désignation<span class="required">*</span></label>
                <div class="input-with-icon">
                    <i class='bx bx-tag'></i>
                    <input type="text" id="designation" name="designation" class="form-control" maxlength="50" required>
                </div>
                <span class="error" id="designationError"></span>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-cancel" id="btnAnnulerAdd">
                    <i class='bx bx-x'></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour modifier une banque -->
<div id="editBanqueModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="form-title">
            <i class='bx bx-edit'></i>
            Modifier une Banque
        </div>
        <form id="editBanqueForm">
            <input type="hidden" id="editBanqueId" name="id">
            <div class="form-group">
                <label for="editCode">Code<span class="required">*</span></label>
                <div class="input-with-icon">
                    <i class='bx bx-code-alt'></i>
                    <input type="text" id="editCode" name="code" class="form-control" maxlength="50" required>
                </div>
                <span class="error" id="editCodeError"></span>
            </div>
            <div class="form-group">
                <label for="editDesignation">Désignation<span class="required">*</span></label>
                <div class="input-with-icon">
                    <i class='bx bx-tag'></i>
                    <input type="text" id="editDesignation" name="designation" class="form-control" maxlength="50" required>
                </div>
                <span class="error" id="editDesignationError"></span>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-cancel" id="btnAnnulerEdit">
                    <i class='bx bx-x'></i> Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.0/dist/sweetalert2.all.min.js"></script>
<script src="../Banque/js/banque.js"></script>

<?php require_once("../Template/footer.php"); ?>

</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.0/dist/sweetalert2.all.min.js"></script>
<script src="../Banque/js/banque.js"></script>

<?php require_once("../Template/footer.php"); ?>
