<?php require_once("../Template/header.php"); ?>

<div class="main-container">
    <h1 class="title" id="pageTitle">Gestion des Agences</h1>
    <div class="page">       
        <div class="action-bar">
            <button class="btn btn-primary" id="addAgenceBtn">
                <i class='bx bx-plus'></i> Ajouter Agence
            </button>
            <!-- Bouton Retour -->
            <button class="btn btn-back" id="backButton" onclick="window.location.href='../Banque/banque.php';">
                <i class='bx bx-arrow-back'></i> Retour
            </button>
        </div>

        <!-- Tableau des agences -->
        <div class="table-container">
            <table id="agencesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Label</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Données dynamiques -->
                </tbody>
            </table>
        </div>

        <!-- Modal for Editing -->
        <div class="modal" id="editAgenceModal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2 id="editModalTitle">Modifier Agence</h2>
                <form id="editAgenceForm" autocomplete="off">
                    <input type="hidden" id="editAgenceId">
                    <div class="form-group">
                        <label>Code<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="editCode">
                        <div class="error-message" id="editCodeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Label<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="editLabel">
                        <div class="error-message" id="editLabelError"></div>
                    </div>
                    <div class="form-group">
                        <label>Adresse<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="editAdresse">
                        <div class="error-message" id="editAdresseError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Modal for Adding -->
        <div class="modal" id="agenceModal" style="display: none;">
            <div class="modal-content">
                <span class="addClose-btn">&times;</span>
                <h2 id="modalTitle">Ajouter Agence</h2>
                <form id="agenceForm" autocomplete="off">
                    <input type="hidden" id="agenceId">
                    <div class="form-group">
                        <label>Code<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="code">
                        <div class="error-message" id="codeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Label<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="label">
                        <div class="error-message" id="labelError"></div>
                    </div>
                    <div class="form-group">
                        <label>Adresse<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="adresse">
                        <div class="error-message" id="adresseError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="css/agence.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.0/dist/sweetalert2.all.min.js"></script>
<script src="js/agence.js"></script>

<?php require_once("../Template/footer.php"); ?>