document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("ajouterAman");
    const modal = document.getElementById("AmandementModal");
    const closeModal = document.querySelector(".close-btn");
    const closeBtn = document.getElementById("close");
    const form = document.getElementById("AmandementForm");
    const tableBody = document.getElementById("amendementsTableBody");

    const datePronongationField = document
        .getElementById("DatePronongation")
        .closest(".form-group");
    const montantField = document
        .getElementById("Montant")
        .closest(".form-group");

    function updateTable(amendments) {
        tableBody.innerHTML = "";
        amendments.forEach((amendment, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
            <td>${index + 1}</td>
            <td>${amendment.num_amd}</td>
            <td>${amendment.date_sys}</td>
            <td>${amendment.date_prorogation || "N/A"}</td>
            <td>${amendment.montant_amd || "N/A"}</td>
            <td>${amendment.type_label || "Inconnu"}</td>
            <td>
                ${amendment.document_path
                    ? `<a href="../../../PFE_SONATRACH/Backend/Amandements/requetes_ajax/${amendment.document_path}" target="_blank">
                <img src="css/image.png" alt="Document" style="width: 44px; height: 24px;">
                </a>`
                    : "Aucun document"
                }
            </td>


            <td>
                <button class="modify-btn" data-id="${amendment.id
                }">Modifier</button>
            </td>
        `;
            tableBody.appendChild(row);
        });
    }

    if (initialAmendments && Array.isArray(initialAmendments)) {
        updateTable(initialAmendments);
    }

    addBtn.addEventListener("click", () => {
        modal.style.display = "flex";
        document.querySelector("#modalTitle").textContent = "Ajouter Amendement";
        form.reset();
        form.querySelector('input[name="amendment_id"]')?.remove();
        clearValidationMessages();
        datePronongationField.style.display = "block";
        montantField.style.display = "block";
    });

    [closeModal, closeBtn].forEach((btn) =>
        btn.addEventListener("click", () => (modal.style.display = "none"))
    );

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    const typeAmandement = document.getElementById("TypeAmandement");
    typeAmandement.addEventListener("change", () => {
        const selectedType = typeAmandement.value;
        datePronongationField.style.display = selectedType == 2 ? "none" : "block";
        montantField.style.display = selectedType == 3 ? "none" : "block";
    });

    function clearValidationMessages() {
        document.querySelectorAll(".validation-message").forEach((msg) => {
            msg.style.display = "none";
        });
    }

    function isValidDate(dateString) {
        const today = new Date().toISOString().split("T")[0];
        return dateString <= today;
    }

    function isDateAfter(start, end) {
        return new Date(end) > new Date(start);
    }

    tableBody.addEventListener("click", (event) => {
        if (event.target.classList.contains("modify-btn")) {
            const id = event.target.dataset.id;
            fetch(
                `../../Backend/Amandements/requetes_ajax/get_amendment.php?id=${id}`
            )
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        const amendment = data.data;
                        form.reset();
                        document.getElementById("NumAmandement").value = amendment.num_amd;
                        document.getElementById("DateAmandement").value =
                            amendment.date_sys;
                        document.getElementById("TypeAmandement").value =
                            amendment.type_amd_id || "";
                        document.getElementById("DatePronongation").value =
                            amendment.date_prorogation || "";
                        document.getElementById("Montant").value =
                            amendment.montant_amd || "";

                        const type = amendment.type_amd_id;
                        datePronongationField.style.display = type == 2 ? "none" : "block";
                        montantField.style.display = type == 3 ? "none" : "block";

                        let hiddenInput = document.querySelector(
                            'input[name="amendment_id"]'
                        );
                        if (!hiddenInput) {
                            hiddenInput = document.createElement("input");
                            hiddenInput.type = "hidden";
                            hiddenInput.name = "amendment_id";
                            form.appendChild(hiddenInput);
                        }
                        hiddenInput.value = id;

                        document.querySelector("#modalTitle").textContent =
                            "Modifier Amendement";
                        modal.style.display = "flex";
                        clearValidationMessages();
                    } else {
                        Swal.fire("Erreur", data.message, "error");
                    }
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire("Erreur", "Impossible de charger les données.", "error");
                });
        }
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        clearValidationMessages();
        let isValid = true;

        const validateField = (id) => {
            const field = document.getElementById(id);
            if (!field.value.trim()) {
                const msg = document.querySelector(`#${id} + .validation-message`);
                if (msg) msg.style.display = "block";
                isValid = false;
            }
        };

        validateField("NumAmandement");
        validateField("DateAmandement");
        validateField("TypeAmandement");

        const dateAmd = document.getElementById("DateAmandement").value;
        if (!isValidDate(dateAmd)) {
            const msg = document.querySelector(
                "#DateAmandement + .validation-message"
            );
            msg.textContent = "La date ne peut pas être dans le futur.";
            msg.style.display = "block";
            isValid = false;
        }

        if (datePronongationField.style.display !== "none") {
            validateField("DatePronongation");
            const datePro = document.getElementById("DatePronongation").value;
            if (dateAmd && datePro && !isDateAfter(dateAmd, datePro)) {
                const msg = document.querySelector(
                    "#DatePronongation + .validation-message"
                );
                msg.textContent =
                    "La date de prolongation doit être après la date de l'amendement.";
                msg.style.display = "block";
                isValid = false;
            }
        }

        if (montantField.style.display !== "none") {
            validateField("Montant");
        }

        const isModify = !!form.querySelector('input[name="amendment_id"]');
        const documentInput = document.getElementById("Document");
        if (!isModify && !documentInput.files.length) {
            const msg = document.querySelector("#Document + .validation-message");
            if (msg) msg.style.display = "block";
            isValid = false;
        }

        if (!isValid) return;

        const formData = new FormData(form);
        const url = isModify
            ? "../../Backend/Amandements/requetes_ajax/update_amendment.php"
            : "../../Backend/Amandements/requetes_ajax/add_amendment.php";

        fetch(url, {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    form.reset();
                    modal.style.display = "none";
                    updateTable(data.data);
                    Swal.fire("Succès", data.message, "success");
                } else {
                    Swal.fire("Erreur", data.message, "error");
                }
            })
            .catch((err) => {
                console.error(err);
                Swal.fire(
                    "Erreur",
                    "Erreur de communication avec le serveur.",
                    "error"
                );
            });
    });
});
