document.addEventListener('DOMContentLoaded', () => {
    // ✅ Sélection des éléments DOM
    const addForm = document.getElementById('agenceForm');
    const addModal = document.getElementById('agenceModal');
    const editModal = document.getElementById('editAgenceModal'); // Modal de modification
    const closeModal = document.querySelectorAll('.close-btn');
    const addBtn = document.getElementById('addAgenceBtn');
    const codeField = document.getElementById('code');
    const labelField = document.getElementById('label');
    const adresseField = document.getElementById('adresse');
    const codeError = document.getElementById('codeError');
    const labelError = document.getElementById('labelError');
    const adresseError = document.getElementById('adresseError');
    const searchInput = document.getElementById('searchInput'); // Champ de recherche
    const tableBody = document.querySelector('#agencesTable tbody'); // Tableau des agences

    // ✅ Récupère l'ID de la banque sélectionnée depuis sessionStorage
    const selectedBanqueId = sessionStorage.getItem('selectedBanqueId');

    if (!selectedBanqueId) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur !',
            text: "Aucune banque sélectionnée.",
        });
        return;
    }

    console.log("ID de la banque sélectionnée :", selectedBanqueId);

    // ✅ Fonction pour récupérer les agences associées à une banque
    function fetchAgences(query = '') {
        fetch(`../../Backend/Agence/requetes_ajax/affichageAgence.php?query=${encodeURIComponent(query)}&banque_id=${selectedBanqueId}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur !',
                    text: data.error,
                });
                return;
            }

            // Efface le contenu actuel du tableau
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5">Aucune donnée disponible</td></tr>`;
                return;
            }

            // Remplit le tableau avec les données des agences
            data.forEach(agence => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${agence.id}</td>
                    <td>${agence.code}</td>
                    <td>${agence.label}</td>
                    <td>${agence.adresse}</td>
                    <td>
                        <button class="btn-edit" 
                            data-id="${agence.id}" 
                            data-code="${agence.code}" 
                            data-label="${agence.label}" 
                            data-adresse="${agence.adresse}">
                            Modifier
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Attache les écouteurs aux boutons "Modifier"
            attachEditListeners();
        })
        .catch(error => {
            console.error("Erreur lors du chargement des agences :", error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur !',
                text: "Une erreur est survenue lors du chargement des agences.",
            });
        });
    }

    // ✅ Récupère les agences au chargement de la page
    fetchAgences();

    // ✅ Vérifie l'unicité du code en temps réel
    codeField.addEventListener('input', () => {
        checkUniqueness('code', codeField.value, codeError);
    });

    // ✅ Vérifie l'unicité du label en temps réel
    labelField.addEventListener('input', () => {
        checkUniqueness('label', labelField.value, labelError);
    });

    // ✅ Ouvre le modal d'ajout
    addBtn.addEventListener('click', () => {
        addForm.reset();
        clearErrors();
        addModal.style.display = 'flex';
    });

    // ✅ Ferme les modals lorsque l'utilisateur clique sur le bouton "X" ou en dehors du modal
    closeModal.forEach(button => {
        button.addEventListener('click', () => {
            addModal.style.display = 'none';
            editModal.style.display = 'none';
            addForm.reset();
            clearErrors();
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === addModal || e.target === editModal) {
            addModal.style.display = 'none';
            editModal.style.display = 'none';
            addForm.reset();
            clearErrors();
        }
    });

    // ✅ Gère la soumission du formulaire d'ajout
    addForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        // Récupération des valeurs des champs
        const code = codeField.value.trim();
        const label = labelField.value.trim();
        const adresse = adresseField.value.trim();

        // Validation des champs requis
        let valid = true;

        if (code === '') {
            codeError.textContent = "Ce champ est obligatoire.";
            codeError.style.display = 'block';
            valid = false;
        }
        if (label === '') {
            labelError.textContent = "Ce champ est obligatoire.";
            labelError.style.display = 'block';
            valid = false;
        }
        if (adresse === '') {
            adresseError.textContent = "Ce champ est obligatoire.";
            adresseError.style.display = 'block';
            valid = false;
        }

        // Vérification de l'unicité du code et du label
        const isCodeUnique = await checkUniqueness('code', code, codeError);
        const isLabelUnique = await checkUniqueness('label', label, labelError);

        if (!isCodeUnique || !isLabelUnique) {
            valid = false; // Empêche la soumission si les champs ne sont pas uniques
        }

        // Soumet le formulaire si toutes les validations sont passées
        if (valid) {
            addModal.style.display = 'none'; // Ferme le modal avant d'afficher SweetAlert2

            fetch('../../Backend/Agence/requetes_ajax/ajouterAgence.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    code,
                    label,
                    adresse,
                    banque_id: selectedBanqueId // Include the bank ID in the payload
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès !',
                        text: data.success,
                    }).then(() => {
                        location.reload(); // Actualise la page après ajout
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur !',
                        text: data?.error || 'Une erreur inattendue est survenue.',
                    });
                }
            })
            .catch(error => {
                console.error("Erreur AJAX :", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur !',
                    text: "Une erreur est survenue lors de l'envoi des données.",
                });
            });
        }
    });

    // ✅ Fonction pour vérifier l'unicité d'un champ
    function checkUniqueness(field, value, errorElement, currentId = null) {
        return new Promise((resolve, reject) => {
            if (!value.trim()) {
                resolve(); // Ne vérifie pas si le champ est vide
                return;
            }

            fetch('../../Backend/Agence/requetes_ajax/uniqueAgence.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ field, value, currentId }) // Inclut l'ID actuel pour exclure l'enregistrement en cours de modification
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    errorElement.textContent = "";
                    errorElement.style.display = 'none';
                    resolve(true); // Le champ est unique
                } else {
                    errorElement.textContent = data.error;
                    errorElement.style.display = 'block';
                    resolve(false); // Le champ n'est pas unique
                }
            })
            .catch(error => {
                console.error("Erreur lors de la vérification :", error);
                errorElement.textContent = "Une erreur est survenue lors de la vérification.";
                errorElement.style.display = 'block';
                resolve(false); // Supposons que ce n'est pas unique en cas d'erreur
            });
        });
    }

    // ✅ Attache des écouteurs aux boutons "Modifier"
    function attachEditListeners() {
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', (e) => {
                const agenceId = e.currentTarget.dataset.id;
                const originalCode = e.currentTarget.dataset.code;
                const originalLabel = e.currentTarget.dataset.label;
                const originalAdresse = e.currentTarget.dataset.adresse;

                // Pré-remplissage des champs du modal de modification
                const editModal = document.getElementById('editAgenceModal');
                const editCodeField = document.getElementById('editCode');
                const editLabelField = document.getElementById('editLabel');
                const editAdresseField = document.getElementById('editAdresse');
                const editCodeError = document.getElementById('editCodeError');
                const editLabelError = document.getElementById('editLabelError');
                const editAdresseError = document.getElementById('editAdresseError');

                // Remplit les champs avec les données actuelles
                document.getElementById('editAgenceId').value = agenceId;
                editCodeField.value = originalCode;
                editLabelField.value = originalLabel;
                editAdresseField.value = originalAdresse;

                // Affiche le modal de modification
                editModal.style.display = 'flex';

                // Validation en temps réel pour les champs "code" et "label"
                editCodeField.addEventListener('input', () => {
                    checkUniqueness('code', editCodeField.value, editCodeError, agenceId);
                });

                editLabelField.addEventListener('input', () => {
                    checkUniqueness('label', editLabelField.value, editLabelError, agenceId);
                });

                // Gestion de la soumission du formulaire de modification
                const editForm = document.getElementById('editAgenceForm');
                editForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    clearErrors();

                    // Récupération des nouvelles valeurs
                    const newCode = editCodeField.value.trim();
                    const newLabel = editLabelField.value.trim();
                    const newAdresse = editAdresseField.value.trim();

                    // Vérifie si aucune modification n'a été effectuée
                    if (newCode === originalCode && newLabel === originalLabel && newAdresse === originalAdresse) {
                        editModal.style.display = 'none'; // Ferme le modal IMMÉDIATEMENT
                        Swal.fire({
                            icon: 'info',
                            title: 'Aucun changement !',
                            text: "Aucune modification n'a été apportée.",
                        });
                        return;
                    }

                    // Validation des champs requis
                    let valid = true;

                    if (newCode === '') {
                        editCodeError.textContent = "Ce champ est obligatoire.";
                        editCodeError.style.display = 'block';
                        valid = false;
                    }
                    if (newLabel === '') {
                        editLabelError.textContent = "Ce champ est obligatoire.";
                        editLabelError.style.display = 'block';
                        valid = false;
                    }
                    if (newAdresse === '') {
                        editAdresseError.textContent = "Ce champ est obligatoire.";
                        editAdresseError.style.display = 'block';
                        valid = false;
                    }

                    // Vérification de l'unicité (en excluant l'enregistrement actuel)
                    if (newCode !== originalCode) {
                        const isCodeUnique = await checkUniqueness('code', newCode, editCodeError, agenceId);
                        if (!isCodeUnique) valid = false;
                    }
                    if (newLabel !== originalLabel) {
                        const isLabelUnique = await checkUniqueness('label', newLabel, editLabelError, agenceId);
                        if (!isLabelUnique) valid = false;
                    }

                    // Soumet le formulaire si toutes les validations sont passées
                    if (valid) {
                        editModal.style.display = 'none'; // Ferme le modal AVANT d'afficher SweetAlert2

                        fetch('../../Backend/Agence/requetes_ajax/modifierAgence.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id: agenceId,
                                code: newCode,
                                label: newLabel,
                                adresse: newAdresse
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès !',
                                    text: data.success,
                                }).then(() => {
                                    location.reload(); // Actualise la page après modification
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur !',
                                    text: data?.error || 'Une erreur inattendue est survenue.',
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Erreur AJAX :", error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur !',
                                text: "Une erreur est survenue lors de la modification.",
                            });
                        });
                    }
                });
            });
        });
    }

    // ✅ Efface les messages d'erreur
    function clearErrors() {
        codeError.textContent = "";
        labelError.textContent = "";
        adresseError.textContent = "";
        codeError.style.display = 'none';
        labelError.style.display = 'none';
        adresseError.style.display = 'none';
    }
});