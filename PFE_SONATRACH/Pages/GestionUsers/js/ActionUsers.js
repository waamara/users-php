document.addEventListener("DOMContentLoaded", function () {
    const toggleStatusBtn = document.getElementById("toggleStatusBtn");
    const accountStatus = document.getElementById("accountStatus");
    const accountStatusBadge = document.getElementById("accountStatusBadge");
    const toggleStatusModal = document.getElementById("toggleStatusModal");
    const confirmToggleStatus = document.getElementById("confirmToggleStatus");
    const toastContainer = document.getElementById("toastContainer");

    // Assure-toi que toggleStatusModal et confirmToggleStatus existent dans ton HTML
    // Si tu ne veux pas de modal, tu peux déclencher directement la fonction sans modal

    // Récupère l'ID utilisateur passé depuis PHP (à mettre dans ta page PHP)
    const userId = window.userId || null; // on définira userId dans la page PHP via <script>

    // Fonction toast pour message utilisateur
    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        let icon = "bx-check-circle";
        if (type === "error") icon = "bx-error";
        if (type === "warning") icon = "bx-error-circle";

        toast.innerHTML = `<i class='bx ${icon}'></i><span>${message}</span>`;
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = "fadeOut 0.5s forwards";
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // Fonction pour ajouter une entrée dans la timeline
    function addTimelineEntry(title, description) {
        const timeline = document.querySelector(".timeline");
        if (!timeline) return;
        const now = new Date();
        const formattedDate = `${now.getDate().toString().padStart(2, "0")}/${
            (now.getMonth() + 1).toString().padStart(2, "0")
        }/${now.getFullYear()} ${now.getHours().toString().padStart(2, "0")}:${now
            .getMinutes()
            .toString()
            .padStart(2, "0")}`;

        const timelineItem = document.createElement("div");
        timelineItem.className = "timeline-item";
        timelineItem.style.opacity = "0";
        timelineItem.style.transform = "translateY(10px)";
        timelineItem.innerHTML = `
            <div class="timeline-date">${formattedDate}</div>
            <div class="timeline-content">
                <div class="timeline-title">${title}</div>
                <div class="timeline-description"><p>${description}</p></div>
            </div>
        `;
        timeline.insertBefore(timelineItem, timeline.firstChild);
        setTimeout(() => {
            timelineItem.style.transition = "opacity 0.3s, transform 0.3s";
            timelineItem.style.opacity = "1";
            timelineItem.style.transform = "translateY(0)";
        }, 10);
    }

    // Gestion clic sur le bouton Activer/Désactiver
    toggleStatusBtn.addEventListener("click", function () {
        if (!userId) {
            showToast("ID utilisateur manquant.", "error");
            return;
        }

        const isActive = accountStatus.classList.contains("active");
        const newStatus = isActive ? 0 : 1;

        fetch("../../api/updateUserStatus.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: userId, status: newStatus }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    if (newStatus === 0) {
                        accountStatus.classList.remove("active");
                        accountStatus.classList.add("inactive");
                        accountStatus.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';

                        accountStatusBadge.classList.remove("active");
                        accountStatusBadge.classList.add("inactive");
                        accountStatusBadge.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';

                        toggleStatusBtn.classList.remove("btn-danger");
                        toggleStatusBtn.classList.add("btn-success");
                        toggleStatusBtn.innerHTML = '<i class="bx bx-power-off"></i> Activer le compte';

                        showToast("Compte désactivé avec succès", "success");
                        addTimelineEntry("Compte désactivé", "Le compte a été désactivé par un administrateur");
                    } else {
                        accountStatus.classList.remove("inactive");
                        accountStatus.classList.add("active");
                        accountStatus.innerHTML = '<i class="bx bx-check-circle"></i> Actif';

                        accountStatusBadge.classList.remove("inactive");
                        accountStatusBadge.classList.add("active");
                        accountStatusBadge.innerHTML = '<i class="bx bx-check-circle"></i> Actif';

                        toggleStatusBtn.classList.remove("btn-success");
                        toggleStatusBtn.classList.add("btn-danger");
                        toggleStatusBtn.innerHTML = '<i class="bx bx-power-off"></i> Désactiver le compte';

                        showToast("Compte activé avec succès", "success");
                        addTimelineEntry("Compte activé", "Le compte a été activé par un administrateur");
                    }
                } else {
                    showToast("Erreur lors de la mise à jour du statut", "error");
                }
            })
            .catch(() => showToast("Erreur réseau", "error"));
    });
});
