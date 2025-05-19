<?php require_once("../Template/header.php"); ?>

<div class="main-container">
    <h1 class="title">Gestion des Monnaies</h1>
    <div class="page">
        <div class="action-bar">
            <button class="btn btn-primary" id="addMonnaieBtn">
                <i class='bx bx-plus'></i> Ajouter Monnaie
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

        <!-- Tableau des monnaies -->
        <div class="table-container">
            <table id="monnaieTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Label</th>
                        <th>Symbole</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal" id="monnaieModal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2 id="modalTitle">Ajouter Monnaie</h2>
                <form id="monnaieForm" autocomplete="off">
                    <input type="hidden" id="monnaieId">
                    <div class="form-group">
                        <label>Code<span class="required">*</span></label>
                        <input type="text" id="code">
                        <div class="error-message" id="codeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Label<span class="required">*</span></label>
                        <input type="text" id="label" >
                        <div class="error-message" id="labelError"></div>
                    </div>
                    <div class="form-group">
                        <label>Symbole<span class="required">*</span></label>
                        <input type="text" id="symbole">
                        <div class="error-message" id="symboleError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="../Monnaie/css//monnaie.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.0/dist/sweetalert2.all.min.js"></script>
<script src="../Monnaie/js/monnaie.js"></script>

<?php require_once("../Template/footer.php"); ?>

