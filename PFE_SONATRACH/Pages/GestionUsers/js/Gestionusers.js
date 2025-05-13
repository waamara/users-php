document.addEventListener("DOMContentLoaded", function () {
    const addUserLink = document.getElementById("addUserLink");
    const modal = document.getElementById("userFormModal");
    const closeBtn = document.getElementById("closeFormBtn");
    const form = document.getElementById("userForm");

    addUserLink.addEventListener("click", function (e) {
        e.preventDefault();
        modal.style.display = "block";
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
        form.reset();
        hideValidationMessages();
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
            form.reset();
            hideValidationMessages();
        }
    });

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        hideValidationMessages();

        const nomCompletInput = document.getElementById("nomComplet");
        const userNameInput = document.getElementById("userName");
        const compteInput = document.getElementById("compte");
        const motDePasseInput = document.getElementById("motDePasse");
        const structureInput = document.getElementById("structure");

        const nomComplet = nomCompletInput.value.trim();
        const userName = userNameInput.value.trim();
        const compte = compteInput.value.trim();
        const motDePasse = motDePasseInput.value.trim();
        const structure = structureInput.value.trim();

        let isValid = true;

        if (!nomComplet) {
            showValidationMessage(nomCompletInput);
            isValid = false;
        }
        if (!userName) {
            showValidationMessage(userNameInput);
            isValid = false;
        }
        if (!compte) {
            showValidationMessage(compteInput);
            isValid = false;
        }
        if (!motDePasse) {
            showValidationMessage(motDePasseInput);
            isValid = false;
        }
        if (!structure) {
            showValidationMessage(structureInput);
            isValid = false;
        }

        if (!isValid) return;

        fetch('../../Backend/GestionUsers/requetes_ajax/add_user.php', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                nomComplet,
                userName,
                compte,
                motDePasse,
                structure,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                form.reset();
                modal.style.display = "none";
                loadUsers();
            })
            .catch((error) => {
                console.error("Erreur:", error);
            });
    });

    function loadUsers() {
        fetch("../../Backend/GestionUsers/requetes_ajax/get_users.php")
            .then(response => response.json())
            .then(users => {
                const tbody = document.querySelector("#garantiesTable tbody");
                tbody.innerHTML = "";

                users.forEach(user => {
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                        <td>${user.nom_user} ${user.prenom_user}</td>
                        <td>${user.username}</td>
                        <td>${user.status || 'Actif'}</td>
                        <td>••••••••</td>
                        <td>${user.structure}</td>
                        <td class="actions">
                            <button class="edit-btn">Actions</button>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                });
            })
            .catch(error => console.error("Erreur de chargement des utilisateurs:", error));
    }

    function showValidationMessage(inputElement) {
        const message = inputElement.parentElement.querySelector(".validation-message");
        if (message) {
            message.style.display = "block";
        }
    }

    function hideValidationMessages() {
        document.querySelectorAll(".validation-message").forEach((message) => {
            message.style.display = "none";
        });
    }

    // Initial load
    loadUsers();
});
