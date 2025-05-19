document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("ajouterAman");
    const modal = document.getElementById("AmandementModal");
    const closeModal = document.querySelector(".close-btn");
    const closeBtn = document.getElementById("close");
    const form = document.getElementById("AmandementForm");
    const tableBody = document.getElementById("amendementsTableBody");

    const datePronongationField = document.getElementById("DatePronongation").closest(".form-group");
    const montantField = document.getElementById("Montant").closest(".form-group");

    /**
     * Function to dynamically update the amendments table
     * @param {Array} amendments - List of amendments to display in the table
     */
    function updateTable(amendments) {
        tableBody.innerHTML = ""; // Clear existing rows

        amendments.forEach((amendment) => {
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${amendment.id}</td>
                <td>${amendment.num_amd}</td>
                <td>${amendment.date_sys}</td>
                <td>${amendment.date_prorogation || 'N/A'}</td>
                <td>${amendment.montant_amd || 'N/A'}</td>
                <td>${amendment.type_label || 'Inconnu'}</td>
                <td>
                    ${amendment.document_path 
                        ? `<a href="../../../Backend/Amandements/requetes_ajax/${amendment.document_path}" target="_blank">${amendment.nom_document || 'Voir le document'}</a>` 
                        : 'Aucun document'}
                </td>
                <td>   
                    <div>
                        <button class="modify-btn" data-id="${amendment.id}">Modifier</button>
                    </div>
                </td>
            `;

            tableBody.appendChild(row);
        });
    }

    // Populate the table with existing amendments on page load
    if (initialAmendments && Array.isArray(initialAmendments)) {
        updateTable(initialAmendments);
    }

    /**
     * Open Modal for Adding a New Amendment
     */
    addBtn.addEventListener("click", () => {
        modal.style.display = "flex";
        document.querySelector("#modalTitle").textContent = "Ajouter Amendement";
        form.reset();
        form.querySelector('input[name="amendment_id"]')?.remove(); // Remove hidden amendment ID field
        clearValidationMessages();
    });

    /**
     * Close Modal
     */
    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    /**
     * Toggle fields based on the selected amendment type
     */
    const typeAmandement = document.getElementById("TypeAmandement");
    typeAmandement.addEventListener("change", () => {
        const selectedType = typeAmandement.value;

        // Reset visibility
        datePronongationField.style.display = "block";
        montantField.style.display = "block";

        // Hide fields based on the selected type
        if (selectedType == 2) { // Augmentation Montant
            datePronongationField.style.display = "none";
        } else if (selectedType == 3) { // Prolongation
            montantField.style.display = "none";
        }
    });

    /**
     * Clear all validation error messages
     */
    function clearValidationMessages() {
        document.querySelectorAll(".validation-message").forEach((message) => {
            message.style.display = "none";
        });
    }

    /**
     * Validate date input (must not be in the future)
     * @param {string} dateString - The date string to validate
     * @returns {boolean} - True if valid, false otherwise
     */
    function isValidDate(dateString) {
        const today = new Date().toISOString().split("T")[0];
        return dateString <= today;
    }

    /**
     * Validate that a date is after another date
     * @param {string} start - The start date string
     * @param {string} end - The end date string
     * @returns {boolean} - True if valid, false otherwise
     */
    function isDateAfter(start, end) {
        return new Date(end) > new Date(start);
    }

    /**
     * Handle Modify Button Clicks
     */
    tableBody.addEventListener("click", (event) => {
        if (event.target.classList.contains("modify-btn")) {
            const amendmentId = event.target.dataset.id;

            // Fetch amendment details from the backend
            fetch(`../../Backend/Amandements/requetes_ajax/get_amendment.php?id=${amendmentId}`)
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const amendment = data.data;

                        // Populate the form fields
                        document.getElementById("NumAmandement").value = amendment.num_amd;
                        document.getElementById("DateAmandement").value = amendment.date_sys;
                        document.getElementById("TypeAmandement").value = amendment.type_amd_id || "";

                        // Handle optional fields
                        document.getElementById("DatePronongation").value = amendment.date_prorogation || "";
                        document.getElementById("Montant").value = amendment.montant_amd || "";

                        // Show/hide fields based on the selected type
                        const selectedType = document.getElementById("TypeAmandement").value;
                        datePronongationField.style.display = selectedType == 2 ? "none" : "block";
                        montantField.style.display = selectedType == 3 ? "none" : "block";

                        // Set the hidden amendment ID field
                        let hiddenInput = document.querySelector('input[name="amendment_id"]');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement("input");
                            hiddenInput.type = "hidden";
                            hiddenInput.name = "amendment_id";
                            form.appendChild(hiddenInput);
                        }
                        hiddenInput.value = amendmentId;

                        // Update modal title and show the modal
                        document.querySelector("#modalTitle").textContent = "Modifier Amendement";
                        modal.style.display = "flex";
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Erreur!",
                            text: data.message,
                        });
                    }
                })
                .catch((error) => {
                    console.error("Error fetching amendment details:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Erreur!",
                        text: "Une erreur est survenue lors de la récupération des détails de l'amendement.",
                    });
                });
        }
    });

    /**
     * Form Submission Handling
     */
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        // Clear previous validation messages
        clearValidationMessages();

        let isValid = true;

        /**
         * Helper function to validate required fields
         * @param {string} fieldId - The ID of the field to validate
         */
        function validateField(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                const message = document.querySelector(`#${fieldId} + .validation-message`);
                if (message) message.style.display = "block";
                isValid = false;
            }
        }

        // Validate required fields
        validateField("NumAmandement");
        validateField("DateAmandement");
        validateField("TypeAmandement");

        // Validate Date Amendement (must not be in the future)
        const dateAmandement = document.getElementById("DateAmandement").value;
        if (!isValidDate(dateAmandement)) {
            document.querySelector("#DateAmandement + .validation-message").textContent =
                "La date ne peut pas être dans le futur.";
            document.querySelector("#DateAmandement + .validation-message").style.display = "block";
            isValid = false;
        }

        // Validate Date Pronongation (only if visible)
        if (datePronongationField.style.display !== "none") {
            validateField("DatePronongation");
            const datePronongation = document.getElementById("DatePronongation").value;
            if (dateAmandement && datePronongation && !isDateAfter(dateAmandement, datePronongation)) {
                document.querySelector("#DatePronongation + .validation-message").textContent =
                    "La date de prolongation doit être après la date de l'amendement.";
                document.querySelector("#DatePronongation + .validation-message").style.display = "block";
                isValid = false;
            }
        }

        // Validate Montant (only if visible)
        if (montantField.style.display !== "none") {
            validateField("Montant");
        }

        // Validate Document (only for new amendments)
        const documentInput = document.getElementById("Document");
        if (!documentInput.files.length && !form.querySelector('input[name="amendment_id"]')) {
            const message = document.querySelector("#Document + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        }

        if (!isValid) return;

        // Prepare form data for AJAX submission
        const formData = new FormData(form);

        // Determine whether to add or modify an amendment
        const isModify = !!form.querySelector('input[name="amendment_id"]');
        const url = isModify ? "../../Backend/Amandements/requetes_ajax/update_amendment.php" : "../../Backend/Amandements/requetes_ajax/add_amendment.php";

        fetch(url, {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Clear the form
                    form.reset();
                    modal.style.display = "none";

                    // Update the table with the new data
                    updateTable(data.data);

                    // Show success message with SweetAlert2
                    Swal.fire({
                        icon: "success",
                        title: "Succès!",
                        text: isModify ? "Amendement modifié avec succès!" : "Amendement ajouté avec succès!",
                    });
                } else {
                    // Show error message with SweetAlert2
                    Swal.fire({
                        icon: "error",
                        title: "Erreur!",
                        text: data.message,
                    });
                }
            })
            .catch((error) => {
                console.error("Error:", error);

                // Show error message with SweetAlert2
                Swal.fire({
                    icon: "error",
                    title: "Erreur!",
                    text: "Une erreur est survenue lors de l'ajout/modification de l'amendement.",
                });
            });
    });
});