<?php require_once("../Template/header.php"); ?>

<div class="main-container">
    <h1 class="title">Gestion des Banques</h1>
    <div class="page">       
        <div class="action-bar">
            <button class="btn btn-primary" id="addBanqueBtn">
                <i class='bx bx-plus'></i> Ajouter Banque
            </button>
            
            <div class="search-container">
                <form action="#" class="search-form">
                    <div class="form-group">
                        <input type="text" id="searchInput" placeholder="Rechercher..." autocomplete="off">
                        <i class='bx bx-search icon'></i>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des banques -->
        <div class="table-container">
            <table id="banquesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Désignation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Données dynamiques -->
                </tbody>
            </table>
        </div>

        <!-- Modal for Editing -->
        <div class="modal" id="editBanqueModal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2 id="editModalTitle">Modifier Banque</h2>
                <form id="editBanqueForm" autocomplete="off">
                    <input type="hidden" id="editBanqueId">
                    <div class="form-group">
                        <label>Code<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="editCode">
                        <div class="error-message" id="editCodeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Désignation<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="editDesignation">
                        <div class="error-message" id="editDesignationError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Modal for Adding -->
        <div class="modal" id="banqueModal" style="display: none;">
            <div class="modal-content">
                <span class="addClose-btn">&times;</span>
                <h2 id="modalTitle">Ajouter Banque</h2>
                <form id="banqueForm" autocomplete="off">
                    <input type="hidden" id="banqueId">
                    <div class="form-group">
                        <label>Code<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="code">
                        <div class="error-message" id="codeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Désignation<span class="required" style="color: red;">*</span></label>
                        <input type="text" id="designation">
                        <div class="error-message" id="designationError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="../Banque/css/banque.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.0/dist/sweetalert2.all.min.js"></script>
<script src="../Banque/js/banque.js"></script>

<?php require_once("../Template/footer.php"); ?>