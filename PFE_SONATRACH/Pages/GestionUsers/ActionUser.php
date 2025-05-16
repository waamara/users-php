<?php
session_start();
require_once("../../db_connection/db_conn.php"); // NE PAS inclure header.php tout de suite

// Vérifier si un ID est passé en GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);
} else {
    header("Location: ../GestionUsers/GestionUsers.php");
    exit();
}

// Récupérer l'utilisateur (ajusté avec le bon nom de colonne 'id')
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: ../GestionUsers/GestionUsers.php");
    exit();
}

// L'utilisateur est valide, on peut inclure le header HTML
require_once("../Template/header.php");
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
                        <span class="info-value" id="structure"><?= htmlspecialchars($user['structure']) ?></span>
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

<script src="../GestionUsers/js/ActionUsers.js"></script>
</body>
</html>

<?php
require_once('../Template/footer.php');
?>
