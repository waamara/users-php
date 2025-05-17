<?php
session_start();
require_once("../../db_connection/db_conn.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);
} else {
    header("Location: ../GestionUsers/GestionUsers.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT users.*, direction.libelle AS structure_libelle
    FROM users
    LEFT JOIN direction ON users.structure = direction.id
    WHERE users.id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: ../GestionUsers/GestionUsers.php");
    exit();
}

require_once("../Template/header.php");

// Charger les structures pour le formulaire
$structures = $pdo->query("SELECT id, libelle FROM direction ORDER BY libelle")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Détails de l'Utilisateur</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../GestionUsers/css/ActionUsers.css" />
</head>
<body>
<div class="container fade-in">
    <div class="page-header">
        <h1><i class='bx bx-user-circle'></i> Détails de l'Utilisateur : <?= htmlspecialchars($user['nom_user'] . ' ' . $user['prenom_user']) ?></h1>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>
                    <i class='bx bx-detail'></i>
                    Utilisateur
                    <span class="status-badge <?= $user['status'] == 1 ? 'active' : 'inactive' ?>" id="accountStatusBadge">
                        <i class='bx <?= $user['status'] == 1 ? 'bx-check-circle' : 'bx-block' ?>'></i>
                        <?= $user['status'] == 1 ? 'Actif' : 'Désactivé' ?>
                    </span>
                </h2>
            </div>
            <div class="action-buttons">
                <a href="../GestionUsers/GestionUsers.php" class="btn btn-secondary" id="ret">
                    <i class='bx bx-arrow-back'></i> Retour
                </a>
                <button class="btn btn-primary" id="editUserBtn">
                    <i class='bx bx-edit'></i> Modifier
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="info-grid">
                <div>
                    <div class="info-group">
                        <span class="info-label">Nom complet</span>
                        <span class="info-value highlight" id="fullName"><?= htmlspecialchars($user['nom_user'] . ' ' . $user['prenom_user']) ?></span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Nom d'utilisateur</span>
                        <span class="info-value" id="username"><?= htmlspecialchars($user['username']) ?></span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Structure</span>
                        <span class="info-value" id="structureDisplay"><?= htmlspecialchars($user['structure_libelle'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div>
                    <div class="info-group">
                        <span class="info-label">État du compte</span>
                        <span class="info-value">
                            <span class="status-badge <?= $user['status'] == 1 ? 'active' : 'inactive' ?>" id="accountStatus">
                                <i class='bx <?= $user['status'] == 1 ? 'bx-check-circle' : 'bx-block' ?>'></i> 
                                <?= $user['status'] == 1 ? 'Actif' : 'Désactivé' ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">État du mot de passe</span>
                        <span class="info-value">
                            <span class="status-badge initialized" id="passwordStatus">
                                <i class='bx bx-check-circle'></i> Initialisé
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="section-title" style="margin-top: 2rem;">
                <i class='bx bx-shield'></i> Actions disponibles
            </div>

            <div class="action-buttons" style="justify-content: center; margin-top: 1.5rem;">
                <?php $isActive = ($user['status'] == 1); ?>
                <button class="btn <?= $isActive ? 'btn-danger' : 'btn-success' ?>" id="toggleStatusBtn">
                    <i class='bx bx-power-off'></i> <?= $isActive ? 'Désactiver le compte' : 'Activer le compte' ?>
                </button>
                <button class="btn btn-warning" id="resetPasswordBtn">
                    <i class='bx bx-reset'></i> Réinitialiser le mot de passe
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Formulaire de modification -->
<div id="userFormModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modifier l'Utilisateur</h3>
                <button id="closeFormBtn" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <div class="form-group">
                        <label for="nomComplet">Nom Complet:</label>
                        <input type="text" id="nomComplet" name="nomComplet" class="form-control" />
                        <div class="validation-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="userName">Nom d'utilisateur:</label>
                        <input type="text" id="userName" name="userName" class="form-control" />
                        <div class="validation-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="compte">Compte:</label>
                        <select id="compte" name="compte" class="form-control">
                            <option value="actif">Actif</option>
                            <option value="desactive">Désactivé</option>
                        </select>
                        <div class="validation-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="structure">Structure:</label>
                        <select id="structure" name="structure" class="form-control">
                            <option value="">-- Sélectionnez une direction --</option>
                            <?php foreach ($structures as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="validation-message"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toastContainer" class="toast-container"></div>

<script>
    window.userId = <?= json_encode($user['id']) ?>;
    window.userData = <?= json_encode($user) ?>;
</script>

<script src="../GestionUsers/js/ActionUsers.js"></script>

</body>
</html>

<?php
require_once('../Template/footer.php');
?>