<?php require_once("../Template/header.php"); ?>

<div class="main-container">
    <h1 class="title">Gestion des Appels d'Offres</h1>
    <div class="page">
        <div class="action-bar">
            <button class="btn btn-primary" id="addAppelOffreBtn">
                <i class='bx bx-plus'></i> Ajouter Un Appel d'Offre
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

        <!-- Tableau des Appels d'Offres -->
        <div class="table-container">
            <table id="appelOffreTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>

        <!-- Modal for Editing -->
        <div class="modal" id="appelOffreModal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2 id="modalTitle">Modifier Un Appel d'Offre</h2>
                <form id="appelOffreForm" autocomplete="off">
                    <input type="hidden" id="appelOffreId">
                    <div class="form-group">
                        <label>Code de l'Appel d'Offre</label>
                        <input type="text" id="code">
                        <div class="error-message" id="codeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Date de l'Appel d'Offre<span class="required">*</span></label>
                        <input type="date" id="dateAO">
                        <div class="error-message" id="dateError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Modal for Adding -->
        <div class="modal" id="addAppelOffreModal" style="display: none;">
            <div class="modal-content">
                <span class="addClose-btn">&times;</span>
                <h2 id="addmodalTitle">Ajouter Un Appel d'Offre</h2>
                <form id="addAppelOffreForm" autocomplete="off">
                    <input type="hidden" id="aadAppelOffreId">
                    <div class="form-group">
                        <label>Code de l'Appel d'Offre</label>
                        <input type="text" id="addCode">
                        <div class="error-message" id="addCodeError"></div>
                    </div>
                    <div class="form-group">
                        <label>Date de l'Appel d'Offre<span class="required">*</span></label>
                        <input type="date" id="addDateAO">
                        <div class="error-message" id="addDateError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="../Appel_Offre/css/appel_offre.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.0/dist/sweetalert2.all.min.js"></script>
<script src="../Appel_Offre/js/appel_offre.js"></script>

<?php require_once("../Template/footer.php"); ?>
