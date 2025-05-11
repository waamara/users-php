<?php
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");
?>

<link rel="stylesheet" href="../Fournisseur/css/fournisseur.css">
<script defer src="../Fournisseur/js/fournisseur.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main>
<body>
    <div class="main-container">
        <h1 class="title">Gestion des Fournisseurs</h1>

        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn btn-primary" id="addFournisseurBtn">
                <i class='bx bx-plus'></i> Ajouter Fournisseur
            </button>

            <!-- Search Bar -->
            <div class="search-container">
                <form class="search-form">
                    <div class="form-group">
                        <input type="text" id="searchInput" placeholder="Rechercher...">
                        <i class='bx bx-search icon'></i>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <table id="fournisseursTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code Fournisseur</th>
                        <th>Nom Fournisseur</th>
                        <th>Raison Sociale</th>
                        <th>Pays </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic Data Will Be Rendered Here -->
                </tbody>
            </table>
        </div>

        <!-- Modal for Adding/Editing Suppliers -->
        <div class="modal" id="fournisseurModal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2 id="modalTitle">Ajouter Fournisseur</h2>
                <form id="fournisseurForm">
                    <input type="hidden" id="fournisseurId">
                    <div class="form-group">
                        <label>Code Fournisseur<span class="required">*</span></label>
                        <input type="text" id="codeFournisseur"> <!-- Removed required -->
                        <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                    </div>
                    <div class="form-group">
                        <label>Nom Fournisseur<span class="required">*</span></label>
                        <input type="text" id="nomFournisseur"> <!-- Removed required -->
                        <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                    </div>
                    <div class="form-group">
                        <label>Raison Sociale</label>
                        <input type="text" id="raisonSociale">
                        <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                    </div>
                    <div class="form-group">
                        <label>Pays<span class="required">*</span></label>
                        <select id="paysId" required>
                            <option value="" disabled selected>Sélectionnez un pays</option>
                            <!-- Options will be dynamically populated here -->
                        </select>
                        <span class="validation-message">Vous devez sélectionner un pays.</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <div class="error-message" id="formError"></div>
                </form>
            </div>
        </div>
    </div>


</body>

</main>


<?php
require_once('../Template/footer.php');
?>