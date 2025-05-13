document.addEventListener("DOMContentLoaded", function () {
    const addUserLink = document.getElementById("addUserLink");
    const modal = document.getElementById("userFormModal");
    const closeBtn = document.getElementById("closeFormBtn");
    const form = document.getElementById("userForm");
    const tbody = document.querySelector("#garantiesTable tbody");

    // Show modal when clicking "Ajouter un User"
    addUserLink.addEventListener("click", function (e) {
        e.preventDefault();
        modal.style.display = "block";
    });

    // Hide modal when clicking close button
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
        form.reset();
        hideValidationMessages();
    });

    // Hide modal if clicked outside content box
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
            form.reset();
            hideValidationMessages();
        }
    });

    // Handle form submission
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Hide all validation messages first
        hideValidationMessages();

        // Get input values
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

        // Send data to server
        fetch("../../Backend/GestionUsers/add_user.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                nomComplet: nomComplet,
                userName: userName,
                compte: compte,
                motDePasse: motDePasse,
                structure: structure,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message); // optional success message
                form.reset();
                modal.style.display = "none";
                loadUsers(); // refresh the user list
            })
            .catch((error) => {
                console.error("Erreur:", error);
            });
    });

    // Function to load all users from the database
    function loadUsers() {
        fetch("get_users.php")
            .then(response => response.json())
            .then(users => {
                const tbody = document.querySelector("#garantiesTable tbody");
                tbody.innerHTML = ""; // Clear the existing table rows

                users.forEach(user => {
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                        <td>${user.nom_user} ${user.prenom_user}</td>
                        <td>${user.username}</td>
                        <td>${user.status}</td>
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
});
