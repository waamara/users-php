<?php
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Vérifier si un ID est passé en GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);
} else {
    header("Location: ../GestionUsers/GestionUsers.php");
    exit();
}

// Récupérer l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: ../GestionUsers/GestionUsers.php");
    exit();
}

// Récupérer l'historique s’il existe une table `historique`
$stmtLogs = $pdo->prepare("SELECT * FROM historique WHERE id_user = ? ORDER BY date_action DESC");
$stmtLogs->execute([$userId]);
$logs = $stmtLogs->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Utilisateur</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../GestionUsers/css/ActionUsers.css">
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
                        <span class="status-badge <?= $user['etat_user'] === 'actif' ? 'active' : 'inactive' ?>" id="accountStatusBadge">
                            <i class='bx <?= $user['etat_user'] === 'actif' ? 'bx-check-circle' : 'bx-block' ?>'></i>
                            <?= ucfirst($user['etat_user']) ?>
                        </span>
                    </h2>
                </div>
                <div class="action-buttons">
                    <a href="../GestionUsers/GestionUsers.php" class="btn btn-secondary" id="ret">
                        <i class='bx bx-arrow-back'>Retour</i>
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
                                <span class="status-badge <?= $user['etat_user'] === 'actif' ? 'active' : 'inactive' ?>" id="accountStatus">
                                    <i class='bx <?= $user['etat_user'] === 'actif' ? 'bx-check-circle' : 'bx-block' ?>'></i> 
                                    <?= ucfirst($user['etat_user']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">État du mot de passe</span>
                            <span class="info-value">
                                <span class="status-badge <?= $user['etat_mdp'] === 'initialisé' ? 'initialized' : 'updated' ?>" id="passwordStatus">
                                    <i class='bx bx-check-circle'></i> <?= ucfirst($user['etat_mdp']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Dernière connexion</span>
                            <span class="info-value" id="lastLogin">
                                <?= !empty($user['last_login']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['last_login']))) : '-' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-shield'></i> Actions disponibles
                </div>

                <div class="action-buttons" style="justify-content: center; margin-top: 1.5rem;">
                    <?php $isActive = ($user['etat_user'] === 'actif'); ?>
                    <button class="btn <?= $isActive ? 'btn-danger' : 'btn-success' ?>" id="toggleStatusBtn">
                        <i class='bx bx-power-off'></i> <?= $isActive ? 'Désactiver le compte' : 'Activer le compte' ?>
                    </button>
                    <button class="btn btn-warning" id="resetPasswordBtn">
                        <i class='bx bx-reset'></i> Réinitialiser le mot de passe
                    </button>
                </div>

                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-history'></i> Historique des activités
                </div>
                <div class="timeline">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <div class="timeline-item">
                                <div class="timeline-date"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($log['date_action']))) ?></div>
                                <div class="timeline-content">
                                    <div class="timeline-title"><?= htmlspecialchars($log['titre_action']) ?></div>
                                    <div class="timeline-description">
                                        <p><?= htmlspecialchars($log['description']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: gray;">Aucune activité enregistrée.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <div class="modal-backdrop" id="toggleStatusModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="toggleStatusTitle"><?= $isActive ? 'Désactiver le compte' : 'Activer le compte' ?></h3>
                <button class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="toggleStatusMessage">
                    <?= $isActive 
                        ? "Êtes-vous sûr de vouloir désactiver ce compte utilisateur ? L'utilisateur ne pourra plus se connecter." 
                        : "Êtes-vous sûr de vouloir activer ce compte utilisateur ?" ?>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button class="btn <?= $isActive ? 'btn-danger' : 'btn-success' ?>" id="confirmToggleStatus">Confirmer</button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="resetPasswordModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Réinitialiser le mot de passe</h3>
                <button class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ? Un email sera envoyé à l'utilisateur avec les instructions.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button class="btn btn-warning" id="confirmResetPassword">Confirmer</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>
    <script src="../GestionUsers/js/ActionUsers.js"></script>
</body>
</html>

<?php
require_once('../Template/footer.php');
?>
