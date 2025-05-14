document.addEventListener("DOMContentLoaded", function () {
    // Éléments DOM
    const toggleStatusBtn = document.getElementById("toggleStatusBtn");
    const resetPasswordBtn = document.getElementById("resetPasswordBtn");
    const accountStatus = document.getElementById("accountStatus");
    const accountStatusBadge = document.getElementById("accountStatusBadge");
    const passwordStatus = document.getElementById("passwordStatus");
    const toggleStatusModal = document.getElementById("toggleStatusModal");
    const resetPasswordModal = document.getElementById("resetPasswordModal");
    const confirmToggleStatus = document.getElementById("confirmToggleStatus");
    const confirmResetPassword = document.getElementById("confirmResetPassword");
    const toggleStatusTitle = document.getElementById("toggleStatusTitle");
    const toggleStatusMessage = document.getElementById("toggleStatusMessage");
    const toastContainer = document.getElementById("toastContainer");

    // Fermer les modals
    document.querySelectorAll('[data-dismiss="modal"]').forEach((button) => {
        button.addEventListener("click", function () {
            document.querySelectorAll(".modal-backdrop").forEach((modal) => {
                modal.classList.remove("show");
            });
        });
    });

    // Ouvrir le modal de désactivation/activation
    toggleStatusBtn.addEventListener("click", function () {
        const isActive = accountStatus.classList.contains("active");

        if (isActive) {
            toggleStatusTitle.textContent = "Désactiver le compte";
            toggleStatusMessage.textContent =
                "Êtes-vous sûr de vouloir désactiver ce compte utilisateur ? L'utilisateur ne pourra plus se connecter au système.";
            confirmToggleStatus.className = "btn btn-danger";
            confirmToggleStatus.textContent = "Désactiver";
        } else {
            toggleStatusTitle.textContent = "Activer le compte";
            toggleStatusMessage.textContent =
                "Êtes-vous sûr de vouloir activer ce compte utilisateur ? L'utilisateur pourra se connecter au système.";
            confirmToggleStatus.className = "btn btn-success";
            confirmToggleStatus.textContent = "Activer";
        }

        toggleStatusModal.classList.add("show");
    });

    // Ouvrir le modal de réinitialisation du mot de passe
    resetPasswordBtn.addEventListener("click", function () {
        resetPasswordModal.classList.add("show");
    });

    // Confirmer la désactivation/activation du compte
    confirmToggleStatus.addEventListener("click", function () {
        const isActive = accountStatus.classList.contains("active");

        if (isActive) {
            // Désactiver le compte
            accountStatus.classList.remove("active");
            accountStatus.classList.add("inactive");
            accountStatus.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';

            accountStatusBadge.classList.remove("active");
            accountStatusBadge.classList.add("inactive");
            accountStatusBadge.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';

            toggleStatusBtn.classList.remove("btn-danger");
            toggleStatusBtn.classList.add("btn-success");
            toggleStatusBtn.innerHTML =
                '<i class="bx bx-power-off"></i> Activer le compte';

            showToast("Compte désactivé avec succès", "success");

            // Ajouter une entrée à l'historique
            addTimelineEntry(
                "Compte désactivé",
                "Le compte a été désactivé par un administrateur"
            );
        } else {
            // Activer le compte
            accountStatus.classList.remove("inactive");
            accountStatus.classList.add("active");
            accountStatus.innerHTML = '<i class="bx bx-check-circle"></i> Actif';

            accountStatusBadge.classList.remove("inactive");
            accountStatusBadge.classList.add("active");
            accountStatusBadge.innerHTML = '<i class="bx bx-check-circle"></i> Actif';

            toggleStatusBtn.classList.remove("btn-success");
            toggleStatusBtn.classList.add("btn-danger");
            toggleStatusBtn.innerHTML =
                '<i class="bx bx-power-off"></i> Désactiver le compte';

            showToast("Compte activé avec succès", "success");

            // Ajouter une entrée à l'historique
            addTimelineEntry(
                "Compte activé",
                "Le compte a été activé par un administrateur"
            );
        }

        toggleStatusModal.classList.remove("show");
    });

    // Confirmer la réinitialisation du mot de passe
    confirmResetPassword.addEventListener("click", function () {
        // Réinitialiser le mot de passe
        passwordStatus.classList.remove("initialized");
        passwordStatus.classList.add("not-initialized");
        passwordStatus.innerHTML = '<i class="bx bx-time"></i> Non initialisé';

        showToast("Mot de passe réinitialisé avec succès", "success");

        // Ajouter une entrée à l'historique
        addTimelineEntry(
            "Mot de passe réinitialisé",
            "Le mot de passe a été réinitialisé par un administrateur"
        );

        resetPasswordModal.classList.remove("show");
    });

    // Fonction pour afficher un toast
    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;

        let icon = "bx-check-circle";
        if (type === "error") icon = "bx-error";
        if (type === "warning") icon = "bx-error-circle";

        toast.innerHTML = `
                    <i class='bx ${icon}'></i>
                    <span>${message}</span>
                `;

        toastContainer.appendChild(toast);

        // Supprimer le toast après 3 secondes
        setTimeout(() => {
            toast.style.animation = "fadeOut 0.5s forwards";
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 3000);
    }

    // Fonction pour ajouter une entrée à la timeline
    function addTimelineEntry(title, description) {
        const timeline = document.querySelector(".timeline");
        const now = new Date();
        const formattedDate = `${now.getDate().toString().padStart(2, "0")}/${(
            now.getMonth() + 1
        )
            .toString()
            .padStart(2, "0")}/${now.getFullYear()} ${now
                .getHours()
                .toString()
                .padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;

        const timelineItem = document.createElement("div");
        timelineItem.className = "timeline-item";
        timelineItem.style.opacity = "0";
        timelineItem.style.transform = "translateY(10px)";

        timelineItem.innerHTML = `
                    <div class="timeline-date">${formattedDate}</div>
                    <div class="timeline-content">
                        <div class="timeline-title">${title}</div>
                        <div class="timeline-description">
                            <p>${description}</p>
                        </div>
                    </div>
                `;

        // Insérer au début de la timeline
        timeline.insertBefore(timelineItem, timeline.firstChild);

        // Animation d'apparition
        setTimeout(() => {
            timelineItem.style.transition = "opacity 0.3s, transform 0.3s";
            timelineItem.style.opacity = "1";
            timelineItem.style.transform = "translateY(0)";
        }, 10);
    }

    // Simuler le chargement des données utilisateur
    function loadUserData() {
        // Dans un environnement réel, vous feriez un appel AJAX ici
        // Pour l'exemple, nous utilisons des données statiques
        const userData = {
            fullName: "Jean Dupont",
            username: "jdupont",
            structure: "Département Informatique",
            isActive: true,
            passwordInitialized: true,
            lastLogin: "15/05/2023 14:30",
        };

        // Mettre à jour l'interface avec les données
        document.getElementById("fullName").textContent = userData.fullName;
        document.getElementById("username").textContent = userData.username;
        document.getElementById("structure").textContent = userData.structure;
        document.getElementById("lastLogin").textContent = userData.lastLogin;

        // Mettre à jour le statut du compte
        updateAccountStatus(userData.isActive);

        // Mettre à jour le statut du mot de passe
        updatePasswordStatus(userData.passwordInitialized);
    }

    // Fonction pour mettre à jour le statut du compte
    function updateAccountStatus(isActive) {
        if (isActive) {
            accountStatus.classList.add("active");
            accountStatus.classList.remove("inactive");
            accountStatus.innerHTML = '<i class="bx bx-check-circle"></i> Actif';

            accountStatusBadge.classList.add("active");
            accountStatusBadge.classList.remove("inactive");
            accountStatusBadge.innerHTML = '<i class="bx bx-check-circle"></i> Actif';

            toggleStatusBtn.classList.add("btn-danger");
            toggleStatusBtn.classList.remove("btn-success");
            toggleStatusBtn.innerHTML =
                '<i class="bx bx-power-off"></i> Désactiver le compte';
        } else {
            accountStatus.classList.add("inactive");
            accountStatus.classList.remove("active");
            accountStatus.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';

            accountStatusBadge.classList.add("inactive");
            accountStatusBadge.classList.remove("active");
            accountStatusBadge.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';

            toggleStatusBtn.classList.add("btn-success");
            toggleStatusBtn.classList.remove("btn-danger");
            toggleStatusBtn.innerHTML =
                '<i class="bx bx-power-off"></i> Activer le compte';
        }
    }

    // Fonction pour mettre à jour le statut du mot de passe
    function updatePasswordStatus(isInitialized) {
        if (isInitialized) {
            passwordStatus.classList.add("initialized");
            passwordStatus.classList.remove("not-initialized");
            passwordStatus.innerHTML =
                '<i class="bx bx-check-circle"></i> Initialisé';
        } else {
            passwordStatus.classList.add("not-initialized");
            passwordStatus.classList.remove("initialized");
            passwordStatus.innerHTML = '<i class="bx bx-time"></i> Non initialisé';
        }
    }

    // Charger les données utilisateur
    loadUserData();
});
