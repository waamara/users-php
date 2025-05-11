<?php
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Récupération et nettoyage des messages de session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
$show_success_alert = $_SESSION['show_success_alert'] ?? false;
unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['show_success_alert']);


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
        <?php if (!empty($success_message) && $show_success_alert): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-check-circle me-2 fs-5'></i>
                    <div><?= htmlspecialchars($success_message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-error-circle me-2 fs-5'></i>
                    <div><?= htmlspecialchars($error_message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h2 class="text-primary">
                    <i class='bx bx-shield me-2'></i>Liste des Garanties
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
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="Valide">Valide</option>
                                <option value="Expiree">Expirée</option>
                                <option value="Liberee">Libérée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="sortSelect" class="form-select">
                                <option value="date_creation_desc">Date création (récent)</option>
                                <option value="date_creation_asc">Date création (ancien)</option>
                                <option value="date_validite_asc">Date validité (croissant)</option>
                                <option value="date_validite_desc">Date validité (décroissant)</option>
                                <option value="montant_desc">Montant (décroissant)</option>
                                <option value="montant_asc">Montant (croissant)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="garantiesTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="num_garantie">Numéro <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="date_creation">Date Création <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="date_validite">Validité <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="montant">Montant <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="direction">Direction <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="fournisseur">Fournisseur <i class='bx bx-sort'></i></th>
                                <th class="sortable" data-sort="statut">Statut <i class='bx bx-sort'></i></th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($garanties)): ?>
                                <?php foreach ($garanties as $row): 
                                    $validite = new DateTime($row['date_validite']);
                                    
                                    // Détermination du statut
                                    if (!empty($row['liberation_id'])) {
                                        $status = 'Liberee';
                                        $statusClass = 'liberee';
                                    } else {
                                        $status = ($validite < $today) ? 'Expiree' : 'Valide';
                                        $statusClass = ($status === 'Expiree') ? 'expiree' : 'valide';
                                    }
                                ?>
                                <tr 
                                    data-num="<?= htmlspecialchars($row['num_garantie']) ?>"
                                    data-date="<?= $row['date_creation'] ?>"
                                    data-validite="<?= $row['date_validite'] ?>"
                                    data-montant="<?= $row['montant'] ?>"
                                    data-direction="<?= htmlspecialchars($row['direction']) ?>"
                                    data-fournisseur="<?= htmlspecialchars($row['nom_fournisseur']) ?>"
                                    data-statut="<?= $status ?>"
                                >
                                    <td><strong><?= htmlspecialchars($row['num_garantie']) ?></strong></td>
                                    <td class="date"><?= date('d/m/Y', strtotime($row['date_creation'])) ?></td>
                                    <td class="date"><?= date('d/m/Y', strtotime($row['date_validite'])) ?></td>
                                    <td class="montant"><?= number_format($row['montant'], 2, ',', ' ') . ' ' . htmlspecialchars($row['monnaie_symbole']) ?></td>
                                    <td><?= htmlspecialchars($row['direction']) ?></td>
                                    <td><?= htmlspecialchars($row['nom_fournisseur']) ?></td>
                                    <td>
                                        <span class="status-pill status-<?= $statusClass ?>">
                                            <?php if ($status === 'Valide'): ?>
                                                <i class='bx bx-check-circle me-1'></i>
                                            <?php elseif ($status === 'Expiree'): ?>
                                                <i class='bx bx-time me-1'></i>
                                            <?php else: ?>
                                                <i class='bx bx-lock-open me-1'></i>
                                            <?php endif; ?>
                                        
                                            <?= $status === 'Liberee' ? 'Libérée' : ($status === 'Expiree' ? 'Expirée' : 'Valide') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="../../Backend/Garantie/view_garantie.php?id=<?= $row['garantie_id'] ?>" 
                                               class="action-btn btn-info" 
                                               data-tooltip="Voir les détails">
                                                <i class='bx bx-show'></i>
                                            </a>
                                            
                                            <a href="../../Backend/Garantie/edit_garantie.php?id=<?= $row['garantie_id'] ?>" 
                                               class="action-btn btn-primary" 
                                               data-tooltip="Modifier la garantie">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            
                                            <form method="GET" action="../Amandements/Amandement.php" class="d-inline">
                                                <input type="hidden" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                                <button type="submit" 
                                                       class="action-btn btn-warning" 
                                                       data-tooltip="Ajouter un amendement">
                                                    <i class='bx bx-edit-alt'></i>
                                                </button>
                                            </form>

                                            <form method="GET" action="../Authentification/Authentification.php" class="d-inline">
                                                <input type="hidden" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                                <button type="submit" 
                                                       class="action-btn btn-info" 
                                                       data-tooltip="Authentifier cette garantie">
                                                    <i class='bx bx-check-shield'></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Modification ici: permettre l'accès à la page de libération même si déjà libérée -->
                                            <form method="POST" action="../Liberation/liberation.php" class="d-inline">
                                                <input type="hidden" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                                <?php if (empty($row['liberation_id'])): ?>
                                                    <button type="submit" 
                                                           class="action-btn btn-success liberation-btn" 
                                                           data-tooltip="Libérer cette garantie">
                                                        <i class='bx bx-lock-open'></i>
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Bouton modifié pour permettre l'accès à la page de libération -->
                                                    <button type="submit" 
                                                           class="action-btn btn-already-liberated" 
                                                           data-tooltip="Voir la libération du <?= date('d/m/Y', strtotime($row['date_liberation'] ?? 'now')) ?>">
                                                        <i class='bx bx-lock-open-alt'></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class='bx bx-info-circle fs-1 text-muted mb-3'></i>
                                            <h5 class="text-muted mb-2">Aucune garantie disponible</h5>
                                            <p class="text-muted mb-4">Aucune garantie n'a été trouvée dans la base de données.</p>
                                            <a href="add_garantie.php" class="btn btn-sm btn-outline-primary">
                                                <i class='bx bx-plus-circle me-1'></i>Ajouter une garantie
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div id="noResults" class="text-center mt-4" style="display: none;">
                    <div class="d-flex flex-column align-items-center p-4">
                        <i class='bx bx-search-alt fs-1 text-muted mb-3'></i>
                        <h5 class="text-muted mb-2">Aucun résultat trouvé</h5>
                        <p class="text-muted">Aucune garantie ne correspond à votre recherche. Essayez d'autres critères.</p>
                        <button id="resetFilters" class="btn btn-sm btn-outline-primary mt-2">
                            <i class='bx bx-reset me-1'></i>Réinitialiser les filtres
                        </button>
                    </div>
                </div>
                
                <!-- Informations de pagination améliorées -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4">
                    <div class="showing-entries mb-3 mb-md-0">
                        Affichage <span id="startEntry">1</span> à <span id="endEntry">10</span> sur <span id="totalEntries"><?= count($garanties) ?></span> entrées
                    </div>
                    <nav id="paginationNav">
                        <ul class="pagination pagination-sm justify-content-center mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>


</body>
</html>

<?php
require_once ('../Template/footer.php');
?>
