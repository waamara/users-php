// Gestionusers.js

document.addEventListener("DOMContentLoaded", function () {
    const addUserLink = document.getElementById('addUserLink');
    const modal = document.getElementById('userFormModal');
    const closeBtn = document.getElementById('closeFormBtn');
    const form = document.getElementById('userForm');
    const tbody = document.querySelector('#garantiesTable tbody');

    // Show modal when clicking "Ajouter un User"
    addUserLink.addEventListener('click', function (e) {
        e.preventDefault();
        modal.style.display = 'block';
    });

    // Hide modal when clicking close button
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        form.reset();
    });

    // Hide modal if clicked outside content box
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            form.reset();
        }
    });

    // Handle form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const nomComplet = document.getElementById('nomComplet').value.trim();
        const userName = document.getElementById('userName').value.trim();
        const compte = document.getElementById('compte').value.trim();
        const motDePasse = document.getElementById('motDePasse').value.trim();
        const structure = document.getElementById('structure').value.trim();

        if (!nomComplet || !userName || !compte || !motDePasse || !structure) {
            alert("Veuillez remplir tous les champs.");
            return;
        }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${nomComplet}</td>
            <td>${userName}</td>
            <td>${compte}</td>
            <td>••••••••</td>
            <td>${structure}</td>
            <td class="actions">
                <button class="edit-btn">Modifier</button>
                <button class="delete-btn">Supprimer</button>
            </td>
        `;

        tbody.appendChild(newRow);

        // Reset and hide form
        form.reset();
        modal.style.display = 'none';
    });
});