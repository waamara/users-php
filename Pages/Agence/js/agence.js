document.addEventListener('DOMContentLoaded', function() {
    // Sélection des éléments DOM
    const addForm = document.getElementById('agenceForm');
    const editForm = document.getElementById('editAgenceForm');
    const addModal = document.getElementById('agenceModal');
    const editModal = document.getElementById('editAgenceModal');
    const addBtn = document.getElementById('addAgenceBtn');
    const closeBtns = document.querySelectorAll('.close-btn');
    const addCloseBtns = document.querySelectorAll('.addClose-btn');
    const tableBody = document.querySelector('#agencesTable tbody');
    const pageTitle = document.getElementById('pageTitle');

    // Récupère l'ID de la banque sélectionnée depuis sessionStorage
    const selectedBanqueId = sessionStorage.getItem('selectedBanqueId');
    const selectedBanqueName = sessionStorage.getItem('selectedBanqueName');

    // Vérifier si une banque est sélectionnée
    if (!selectedBanqueId) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur !',
            text: "Aucune banque sélectionnée.",
            confirmButtonColor: '#1775f1'
        }).then(() => {
            window.location.href = '../Banque/banque.php';
        });
        return;
    }

    // Mettre à jour le titre avec le nom de la banque
    if (selectedBanqueName) {
        pageTitle.textContent = `Les Agences de la Banque ${selectedBanqueName}`;
    }

    console.log("ID de la banque sélectionnée:", selectedBanqueId);

    // Fonction pour récupérer les agences
    function fetchAgences() {
        // Afficher un indicateur de chargement
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Chargement...</td></tr>';
        
        fetch(`../../Backend/Agence/requetes_ajax/affichageAgence.php?banque_id=${selectedBanqueId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                // Effacer le contenu actuel du tableau
                tableBody.innerHTML = '';
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Aucune agence trouvée</td></tr>';
                    return;
                }
                
                // Remplir le tableau avec les données
                data.forEach(agence => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${agence.id}</td>
                        <td>${agence.code}</td>
                        <td>${agence.label}</td>
                        <td>${agence.adresse}</td>
                        <td>
                            <button class="btn-edit" data-id="${agence.id}" data-code="${agence.code}" 
                                data-label="${agence.label}" data-adresse="${agence.adresse}">
                                Modifier
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                // Attacher les écouteurs d'événements aux boutons d'édition
                attachEditListeners();
            })
            .catch(error => {
                console.error('Erreur:', error);
                tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Une erreur est survenue lors du chargement des agences.</td></tr>';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur !',
                    text: "Une erreur est survenue lors du chargement des agences.",
                    confirmButtonColor: '#1775f1'
                });
            });
    }

    // Charger les agences au démarrage
    fetchAgences();

    // Ouvrir le modal d'ajout
    addBtn.addEventListener('click', function() {
        addForm.reset();
        clearErrors();
        addModal.style.display = 'block';
    });

    // Fermer les modals
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            editModal.style.display = 'none';
        });
    });

    addCloseBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            addModal.style.display = 'none';
        });
    });

    // Fermer les modals en cliquant en dehors
    window.addEventListener('click', function(event) {
        if (event.target === addModal) {
            addModal.style.display = 'none';
        }
        if (event.target === editModal) {
            editModal.style.display = 'none';
        }
    });

    // Vérifier l'unicité d'un champ
    function checkUniqueness(field, value, errorElement, currentId = null) {
        return new Promise((resolve) => {
            if (!value.trim()) {
                resolve(true);
                return;
            }

            const data = { field, value };
            if (currentId) data.currentId = currentId;

            fetch('../../Backend/Agence/requetes_ajax/uniqueAgence.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    errorElement.style.display = 'none';
                    resolve(true);
                } else {
                    errorElement.textContent = data.error;
                    errorElement.style.display = 'block';
                    resolve(false);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorElement.textContent = "Erreur de vérification";
                errorElement.style.display = 'block';
                resolve(false);
            });
        });
    }

    // Ajouter les écouteurs pour la vérification d'unicité
    document.getElementById('code').addEventListener('input', function() {
        checkUniqueness('code', this.value, document.getElementById('codeError'));
    });

    document.getElementById('label').addEventListener('input', function() {
        checkUniqueness('label', this.value, document.getElementById('labelError'));
    });

    // Soumettre le formulaire d'ajout
    addForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const code = document.getElementById('code').value.trim();
        const label = document.getElementById('label').value.trim();
        const adresse = document.getElementById('adresse').value.trim();
        
        clearErrors();
        
        // Validation des champs
        let valid = true;
        
        if (!code) {
            document.getElementById('codeError').textContent = "Ce champ est obligatoire";
            document.getElementById('codeError').style.display = 'block';
            valid = false;
        }
        
        if (!label) {
            document.getElementById('labelError').textContent = "Ce champ est obligatoire";
            document.getElementById('labelError').style.display = 'block';
            valid = false;
        }
        
        if (!adresse) {
            document.getElementById('adresseError').textContent = "Ce champ est obligatoire";
            document.getElementById('adresseError').style.display = 'block';
            valid = false;
        }
        
        // Vérification d'unicité
        const isCodeUnique = await checkUniqueness('code', code, document.getElementById('codeError'));
        const isLabelUnique = await checkUniqueness('label', label, document.getElementById('labelError'));
        
        if (!isCodeUnique || !isLabelUnique) {
            valid = false;
        }
        
        if (valid) {
            // Envoyer les données
            fetch('../../Backend/Agence/requetes_ajax/ajouterAgence.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    code,
                    label,
                    adresse,
                    banque_id: selectedBanqueId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addModal.style.display = 'none';
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès !',
                        text: data.success,
                        confirmButtonColor: '#1775f1'
                    }).then(() => {
                        fetchAgences();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur !',
                        text: data.error || "Une erreur est survenue",
                        confirmButtonColor: '#1775f1'
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur !',
                    text: "Une erreur est survenue lors de l'ajout",
                    confirmButtonColor: '#1775f1'
                });
            });
        }
    });

    // Attacher les écouteurs aux boutons d'édition
    function attachEditListeners() {
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const code = this.getAttribute('data-code');
                const label = this.getAttribute('data-label');
                const adresse = this.getAttribute('data-adresse');
                
                document.getElementById('editAgenceId').value = id;
                document.getElementById('editCode').value = code;
                document.getElementById('editLabel').value = label;
                document.getElementById('editAdresse').value = adresse;
                
                clearEditErrors();
                editModal.style.display = 'block';
            });
        });
    }

    // Ajouter les écouteurs pour la vérification d'unicité dans le formulaire d'édition
    document.getElementById('editCode').addEventListener('input', function() {
        const id = document.getElementById('editAgenceId').value;
        checkUniqueness('code', this.value, document.getElementById('editCodeError'), id);
    });

    document.getElementById('editLabel').addEventListener('input', function() {
        const id = document.getElementById('editAgenceId').value;
        checkUniqueness('label', this.value, document.getElementById('editLabelError'), id);
    });

    // Soumettre le formulaire d'édition
    editForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const id = document.getElementById('editAgenceId').value;
        const code = document.getElementById('editCode').value.trim();
        const label = document.getElementById('editLabel').value.trim();
        const adresse = document.getElementById('editAdresse').value.trim();
        
        clearEditErrors();
        
        // Validation des champs
        let valid = true;
        
        if (!code) {
            document.getElementById('editCodeError').textContent = "Ce champ est obligatoire";
            document.getElementById('editCodeError').style.display = 'block';
            valid = false;
        }
        
        if (!label) {
            document.getElementById('editLabelError').textContent = "Ce champ est obligatoire";
            document.getElementById('editLabelError').style.display = 'block';
            valid = false;
        }
        
        if (!adresse) {
            document.getElementById('editAdresseError').textContent = "Ce champ est obligatoire";
            document.getElementById('editAdresseError').style.display = 'block';
            valid = false;
        }
        
        // Vérification d'unicité
        const isCodeUnique = await checkUniqueness('code', code, document.getElementById('editCodeError'), id);
        const isLabelUnique = await checkUniqueness('label', label, document.getElementById('editLabelError'), id);
        
        if (!isCodeUnique || !isLabelUnique) {
            valid = false;
        }
        
        if (valid) {
            // Envoyer les données
            fetch('../../Backend/Agence/requetes_ajax/modifierAgence.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id,
                    code,
                    label,
                    adresse
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editModal.style.display = 'none';
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès !',
                        text: data.success,
                        confirmButtonColor: '#1775f1'
                    }).then(() => {
                        fetchAgences();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur !',
                        text: data.error || "Une erreur est survenue",
                        confirmButtonColor: '#1775f1'
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur !',
                    text: "Une erreur est survenue lors de la modification",
                    confirmButtonColor: '#1775f1'
                });
            });
        }
    });

    // Effacer les erreurs du formulaire d'ajout
    function clearErrors() {
        const errorElements = document.querySelectorAll('#agenceForm .error-message');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }

    // Effacer les erreurs du formulaire d'édition
    function clearEditErrors() {
        const errorElements = document.querySelectorAll('#editAgenceForm .error-message');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }
});