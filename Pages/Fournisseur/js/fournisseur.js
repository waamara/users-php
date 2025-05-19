document.addEventListener('DOMContentLoaded', async () => {
    const tableBody = document.querySelector('#fournisseursTable tbody');
    const searchInput = document.getElementById('searchInput');
    const addBtn = document.getElementById('addFournisseurBtn');
    const modal = document.getElementById('fournisseurModal');
    const form = document.getElementById('fournisseurForm');
    const closeModal = document.querySelector('.close-btn');
    const formError = document.getElementById('formError');
    const paysDropdown = document.getElementById('paysId');

    let fournisseurs = []; // Stores fetched data

    // Fetch fournisseurs from the database
    async function fetchFournisseurs() {
        try {
            const response = await fetch('../../Backend/Fournisseur/requetes_ajax/get_fournisseurs.php');
            if (!response.ok) throw new Error('Server error');
            const data = await response.json();
            if (data.error) {
                console.error('Error fetching fournisseurs:', data.error);
                return;
            }
            fournisseurs = data.data; // Store fetched data
            renderTable(fournisseurs);
        } catch (error) {
            console.error('Error fetching fournisseurs:', error);
        }
    }

    // Fetch countries from the backend
    async function fetchCountries() {
        try {
            const response = await fetch('../../Backend/Fournisseur/requetes_ajax/get_pays.php');
            if (!response.ok) throw new Error('Server error');
            const data = await response.json();
            if (data.status === 'success') {
                // Clear existing options
                paysDropdown.innerHTML = '';
                // Add default placeholder option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Sélectionnez un pays';
                defaultOption.disabled = true;
                defaultOption.selected = true;
                paysDropdown.appendChild(defaultOption);
                // Populate the dropdown with countries
                data.data.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.id; // Use the ID as the value
                    option.textContent = country.label; // Display the label
                    paysDropdown.appendChild(option);
                });
            } else {
                console.error('Error fetching countries:', data.message);
            }
        } catch (error) {
            console.error('Error fetching countries:', error);
        }
    }

    // Call the functions to populate data
    fetchFournisseurs();
    fetchCountries();

    // Form submission (Add/Update fournisseur)
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        formError.style.display = 'none'; // Reset general error message

        // Clear previous validation messages
        document.querySelectorAll('.validation-message').forEach(message => {
            message.style.display = 'none'; // Hide all validation messages initially
        });

        // Get input values
        const id = document.getElementById('fournisseurId').value;
        const codeFournisseur = document.getElementById('codeFournisseur').value.trim();
        const nomFournisseur = document.getElementById('nomFournisseur').value.trim();
        const raisonSociale = document.getElementById('raisonSociale').value.trim();
        const paysId = document.getElementById('paysId').value;

        // Validation flags
        let isValid = true;

        // Validate Code Fournisseur
        if (!codeFournisseur) {
            document.querySelector('#codeFournisseur + .validation-message').style.display = 'block';
            isValid = false;
        }

        // Validate Nom Fournisseur
        if (!nomFournisseur) {
            document.querySelector('#nomFournisseur + .validation-message').style.display = 'block';
            isValid = false;
        }

        // Validate raisonSociale
        if (!raisonSociale) {
            document.querySelector('#raisonSociale + .validation-message').style.display = 'block';
            isValid = false;
        }

        // Validate paysId
        if (!paysId) {
            document.querySelector('#paysId + .validation-message').style.display = 'block';
            isValid = false;
        }

        if (!isValid) return; // Stop if validation fails

        try {
            let response;
            let requestData = {
                id: id || null, // Include ID for update, or null for new entry
                code_fournisseur: codeFournisseur,
                nom_fournisseur: nomFournisseur,
                raison_sociale: raisonSociale || '', // Send empty string if not filled
                pays_id: paysId || '' // Send empty string if not filled
            };

            if (id) {
                response = await fetch('../../Backend/Fournisseur/requetes_ajax/update_fournisseur.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestData)
                });
            } else {
                response = await fetch('../../Backend/Fournisseur/requetes_ajax/add_fournisseur.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestData)
                });
            }

            const result = await response.json();
            console.log("Server Response:", result); // Debugging log

            if (result.status === 'success') {
                showNotification('success', 'Succès!', 'Le fournisseur a été enregistré avec succès.');
                fetchFournisseurs(); // Refresh the table
                modal.style.display = 'none'; // Close modal after success
                form.reset(); // Reset form fields
            } else {
                // Handle duplicate entry error
                if (result.message && result.message.includes('Duplicate entry')) {
                    showNotification('error', 'Erreur!', 'Le code fournisseur existe déjà. Veuillez choisir un autre code.');
                } else {
                    showNotification('error', 'Erreur!', result.message || 'Une erreur est survenue. Veuillez réessayer.');
                }
            }
        } catch (error) {
            showNotification('error', 'Erreur!', 'Une erreur est survenue. Veuillez réessayer.');
        }
    });

    // Search fournisseurs
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filtered = fournisseurs.filter(f =>
            f.code_fournisseur.toLowerCase().includes(searchTerm) ||
            f.nom_fournisseur.toLowerCase().includes(searchTerm) ||
            f.raison_sociale?.toLowerCase().includes(searchTerm) ||
            f.pays_label?.toLowerCase().includes(searchTerm)
        );
        renderTable(filtered);
    });

    // Open Add Modal
    addBtn.addEventListener('click', () => {
        form.reset();
        formError.style.display = 'none';
        document.getElementById('modalTitle').textContent = 'Ajouter Fournisseur';
        document.getElementById('fournisseurId').value = '';
        modal.style.display = 'flex';
    });

    // Close Modal
    closeModal.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // Render Table
    function renderTable(data) {
        tableBody.innerHTML = data.map(f => `
            <tr>
                <td>${f.id}</td>
                <td>${f.code_fournisseur}</td>
                <td>${f.nom_fournisseur}</td>
                <td>${f.raison_sociale || '-'}</td>
                <td>${f.pays_label || '-'}</td> <!-- Display the country name -->
                <td>
                    <button class="btn btn-edit" data-id="${f.id}">
                        <i class='bx bx-edit'></i> Modifier
                    </button>
                </td>
            </tr>
        `).join('');

        // Attach Edit Handlers
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const id = e.target.closest('button').dataset.id;
                const f = fournisseurs.find(f => f.id == id);

                console.log("Fournisseur Data:", f); // Log the fournisseur object

                document.getElementById('modalTitle').textContent = 'Modifier Fournisseur';
                document.getElementById('fournisseurId').value = f.id;
                document.getElementById('codeFournisseur').value = f.code_fournisseur;
                document.getElementById('nomFournisseur').value = f.nom_fournisseur;
                document.getElementById('raisonSociale').value = f.raison_sociale || '';

                // Ensure the dropdown is populated
                const paysDropdown = document.getElementById('paysId');
                if (paysDropdown.options.length <= 1) { // Check if only the placeholder option exists
                    await fetchCountries(); // Fetch countries if not already loaded
                }

                // Set the selected country in the dropdown
                if (f.pays_id && paysDropdown.querySelector(`option[value="${f.pays_id}"]`)) {
                    paysDropdown.value = f.pays_id; // Set the value if the option exists
                } else {
                    paysDropdown.value = ''; // Default to the placeholder option
                }

                modal.style.display = 'flex';
            });
        });
    }

    // Helper function to display notifications
    function showNotification(icon, title, text) {
        Swal.fire({
            icon,
            title,
            text,
            confirmButtonText: 'OK'
        });
    }
});