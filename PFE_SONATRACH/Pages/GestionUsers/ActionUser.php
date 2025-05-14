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
            <h1><i class='bx bx-user-circle'></i> Détails de l'Utilisateur</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2>
                        <i class='bx bx-detail'></i>
                        Utilisateur
                        <span class="status-badge active" id="accountStatusBadge">
                            <i class='bx bx-check-circle'></i>
                            Actif
                        </span>
                    </h2>
                </div>
                <div class="action-buttons">
                    <a href="#" class="btn btn-secondary" id="ret" onclick="history.back(); return false;">
                        <i class='bx bx-arrow-back'></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div>
                        <div class="info-group">
                            <span class="info-label">Nom complet</span>
                            <span class="info-value highlight" id="fullName">Jean Dupont</span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Nom d'utilisateur</span>
                            <span class="info-value" id="username">jdupont</span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Structure</span>
                            <span class="info-value" id="structure">Département Informatique</span>
                        </div>
                    </div>
                    <div>
                        <div class="info-group">
                            <span class="info-label">État du compte</span>
                            <span class="info-value">
                                <span class="status-badge active" id="accountStatus">
                                    <i class='bx bx-check-circle'></i> Actif
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
                        <div class="info-group">
                            <span class="info-label">Dernière connexion</span>
                            <span class="info-value" id="lastLogin">15/05/2023 14:30</span>
                        </div>
                    </div>
                </div>

                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-shield'></i> Actions disponibles
                </div>
                
                <div class="action-buttons" style="justify-content: center; margin-top: 1.5rem;">
                    <button class="btn btn-danger" id="toggleStatusBtn">
                        <i class='bx bx-power-off'></i> Désactiver le compte
                    </button>
                    <button class="btn btn-warning" id="resetPasswordBtn">
                        <i class='bx bx-reset'></i> Réinitialiser le mot de passe
                    </button>
                </div>

                <div class="section-title" style="margin-top: 2rem;">
                    <i class='bx bx-history'></i> Historique des activités
                </div>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-date">15/05/2023 14:30</div>
                        <div class="timeline-content">
                            <div class="timeline-title">Connexion réussie</div>
                            <div class="timeline-description">
                                <p>Adresse IP: 192.168.1.45</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">10/05/2023 09:15</div>
                        <div class="timeline-content">
                            <div class="timeline-title">Mot de passe modifié</div>
                            <div class="timeline-description">
                                <p>L'utilisateur a changé son mot de passe</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">01/05/2023 11:20</div>
                        <div class="timeline-content">
                            <div class="timeline-title">Compte créé</div>
                            <div class="timeline-description">
                                <p>Création du compte utilisateur</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation pour désactiver/activer le compte -->
    <div class="modal-backdrop" id="toggleStatusModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="toggleStatusTitle">Désactiver le compte</h3>
                <button class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="toggleStatusMessage">Êtes-vous sûr de vouloir désactiver ce compte utilisateur ? L'utilisateur ne pourra plus se connecter au système.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button class="btn btn-danger" id="confirmToggleStatus">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation pour réinitialiser le mot de passe -->
    <div class="modal-backdrop" id="resetPasswordModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Réinitialiser le mot de passe</h3>
                <button class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ? Un email sera envoyé à l'utilisateur avec les instructions pour définir un nouveau mot de passe.</p>
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
