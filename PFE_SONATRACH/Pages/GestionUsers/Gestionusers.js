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

        // Add user to table
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>${nomComplet}</td>
            <td>${userName}</td>
            <td>${compte}</td>
            <td>••••••••</td>
            <td>${structure}</td>
            <td class="actions">
                <button class="edit-btn">Actions</button>
            </td>
        `;

        tbody.appendChild(newRow);

        // Reset and hide form
        form.reset();
        modal.style.display = "none";
    });

    function showValidationMessage(inputElement) {
        const message = inputElement.parentElement.querySelector(".validation-message");
        if (message) {
            message.style.display = "block";
        }
    }

    function hideValidationMessages() {
        document.querySelectorAll('.validation-message').forEach(message => {
            message.style.display = 'none';
        });
    }
});
