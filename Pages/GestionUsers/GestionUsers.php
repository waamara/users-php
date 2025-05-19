<?php
session_start();
 

require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Charger les directions
$stmt = $pdo->query("SELECT id, libelle FROM direction ORDER BY libelle");
$directions = $stmt->fetchAll();

// Charger les rôles
$stmtRole = $pdo->query("SELECT id, nom_role FROM role ORDER BY nom_role");
$roles = $stmtRole->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <!-- Critical CSS fixes -->
    <style>
        /* Critical modal and SweetAlert fixes */
        .swal2-container {
            z-index: 999999 !important;
        }

        .modal {
            z-index: 99999 !important;
        }
    </style>
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/GestionUsers.css">
</head>

<body>
    <div class="user-management-container">
        <div class="main-container">
            <div class="title">
                <i class='bx bx-user-circle'></i>
                Gestion des Utilisateurs
            </div>

            <div class="page">
                <div class="form-container">
                    <div class="form-title">
                        <i class='bx bx-list-ul'></i>
                        Liste des Utilisateurs
                    </div>

                    <!-- Barre d'action pour rechercher et ajouter -->
                    <div class="action-bar">
                        <!-- Recherche à gauche -->
                        <div class="search-container">
                            <div class="form-group">
                                <div class="input-with-icon">
                                    <i class='bx bx-search'></i>
                                    <input type="text" id="searchInput" placeholder="Rechercher un utilisateur..." class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Bouton d'ajout à droite -->
                        <div class="action-buttons">
                            <button id="btnAjouter" class="btn btn-primary">
                                <i class='bx bx-plus-circle' style="color:azure;"></i> Ajouter un Utilisateur
                            </button>
                        </div>
                    </div>

                    <div class="table-container slideUp">
                        <!-- En-tête du tableau -->
                        <div class="table-header">
                            <div class="header-name">Nom Complet</div>
                            <div class="header-username">Nom d'utilisateur</div>
                            <div class="header-status">Statut</div>
                            <div class="header-structure">Structure</div>
                            <div class="header-role">Rôle</div>
                            <div class="header-actions">Actions</div>
                        </div>

                        <ul class="users-list" id="usersData">
                            <!-- Les utilisateurs seront chargés dynamiquement ici -->
                        </ul>

                        <!-- Message pour aucun résultat -->
                        <div class="no-results" id="noResults">
                            <i class='bx bx-search'></i>
                            <p>Aucun utilisateur ne correspond à votre recherche.</p>
                        </div>
                    </div>

                    <!-- Pagination - sera générée dynamiquement -->
                    <div id="paginationContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un utilisateur -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="form-title">
                <i class='bx bx-user-plus'></i>
                Ajouter un Utilisateur
            </div>
            <form id="userForm">
                <div class="form-group">
                    <label for="nomComplet" class="file">Nom Complet<span>*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-user'></i>
                        <input type="text" id="nomComplet" name="nomComplet" class="form-control">
                    </div>
                    <span class="error" id="nomCompletError"></span>
                </div>

                <div class="form-group">
                    <label for="userName" class="file">Nom d'utilisateur<span>*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-id-card'></i>
                        <input type="text" id="userName" name="userName" class="form-control">
                    </div>
                    <span class="error" id="userNameError"></span>
                </div>

                <div class="form-group">
                    <label for="compte" class="file">Statut<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-toggle-right'></i>
                        <select id="compte" name="compte" class="form-control">
                            <option value="">-- Sélectionnez un statut --</option>
                            <option value="actif" selected>Actif</option>
                            <option value="desactive">Désactivé</option>
                        </select>
                    </div>
                    <span class="error" id="compteError"></span>
                </div>

                <div class="form-group">
                    <label for="structure" class="file">Structure<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-building'></i>
                        <select id="structure" name="structure" class="form-control">
                            <option value="">-- Sélectionnez une direction --</option>
                            <?php foreach ($directions as $direction): ?>
                                <option value="<?= htmlspecialchars($direction['id']) ?>">
                                    <?= htmlspecialchars($direction['libelle']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <span class="error" id="structureError"></span>
                </div>

                <div class="form-group">
                    <label for="role" class="file">Rôle<span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class='bx bx-shield-quarter'></i>
                        <select id="role" name="role" class="form-control">
                            <option value="">-- Sélectionnez un rôle --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role['id']) ?>">
                                    <?= htmlspecialchars(ucfirst($role['nom_role'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <span class="error" id="roleError"></span>
                </div>

                <!-- Champ de mot de passe caché avec valeur par défaut -->
                <input type="hidden" id="motDePasse" name="motDePasse" value="P@ssword123">

                <div class="form-info">
                    <i class='bx bx-info-circle'></i>
                    Le mot de passe par défaut est <strong> &nbsp; P@ssword123</strong>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" id="btnAnnuler">
                        <i class='bx bx-x'></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save'></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/modal-fix.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <!-- Custom JS -->

    <script src="js/Gestionusers.js"></script>

</body>

</html>


<?php require_once('../Template/footer.php'); ?>