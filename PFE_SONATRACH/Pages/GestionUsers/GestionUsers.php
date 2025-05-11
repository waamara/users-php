<?php
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Garanties Bancaires</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="GestionUsers.css">
</head>

<body>
    <div class="listegarantie-container">

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h2 class="text-primary">
                    <i class='bx bx-shield me-2'></i>Liste des Users
                </h2>
                <a href="add_garantie.php" class="btn btn-add d-flex align-items-center">
                    <i class='bx bx-plus-circle me-2'></i>Ajouter une garantie
                </a>
            </div>
            <div class="card-body">
                <!-- Filtres améliorés -->
                <div class="filter-container mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group position-relative">
                                <span class="input-group-text bg-white"><i class='bx bx-search'></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par numéro, fournisseur, direction...">
                                <button type="button" id="clearSearch" class="search-clear" aria-label="Effacer la recherche">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="garantiesTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="num_garantie">Nom Complet <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="date_creation">User Name <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="date_validite">Compte <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="montant">Mot de passe <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="direction">Structure <i class='bx bx-sort'></i></th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require_once('../Template/footer.php');
?>