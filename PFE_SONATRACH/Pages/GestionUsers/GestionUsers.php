<?php
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Charger les directions
$stmt = $pdo->query("SELECT id, libelle FROM direction ORDER BY libelle");
$directions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Liste des Users</title>
    <link rel="stylesheet" href="../GestionUsers/css/GestionUsers.css" />
</head>
<body>
    <div>
        <h2>Liste des Users</h2>
        <a href="#" id="addUserLink">Ajouter un User</a>
    </div>

    <div>
        <input type="text" id="searchInput" placeholder="Rechercher..." />
        <table id="garantiesTable">
            <thead>
                <tr>
                    <th>Nom Complet</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Mot de passe</th>
                    <th>Structure</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM users");
                $users = $stmt->fetchAll();
                foreach ($users as $user) {
                    echo "<tr>
                        <td>{$user['nom_user']} {$user['prenom_user']}</td>
                        <td>{$user['username']}</td>
                        <td>none</td>
                        <td>••••••••</td>
                        <td>{$user['structure']}</td>
                        <td class='actions'>
                            <a href='ActionUser.php?id=" . $user['id'] . "' title='Modifier l\\'utilisateur'>Actions</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="userFormModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span id="closeFormBtn" class="close-btn">&times;</span>
            <h3>Ajouter un Nouveau User</h3>
            <form id="userForm">
                <div class="form-group">
                    <label for="nomComplet">Nom Complet:</label>
                    <input type="text" id="nomComplet" name="nomComplet" />
                    <div class="validation-message" style="display:none;color:red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="userName">User Name:</label>
                    <input type="text" id="userName" name="userName" />
                    <div class="validation-message" style="display:none;color:red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="compte">Compte:</label>
                    <select id="compte" name="compte">
                        <option value="">-- Sélectionnez un état --</option>
                        <option value="actif">Actif</option>
                        <option value="desactive">Désactivé</option>
                    </select>
                    <div class="validation-message" style="display:none;color:red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="motDePasse">Mot de Passe:</label>
                    <input type="password" id="motDePasse" name="motDePasse" />
                    <div class="validation-message" style="display:none;color:red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="structure">Structure:</label>
                    <select id="structure" name="structure">
                        <option value="">-- Sélectionnez une direction --</option>
                        <?php foreach ($directions as $direction): ?>
                            <option value="<?= htmlspecialchars($direction['libelle']) ?>">
                                <?= htmlspecialchars($direction['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="validation-message" style="display:none;color:red;">Ce champ est requis</div>
                </div>

                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <script>
        const addUserLink = document.getElementById('addUserLink');
        const modal = document.getElementById('userFormModal');
        const closeBtn = document.getElementById('closeFormBtn');

        addUserLink.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'block';
        });

        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        // Envoi AJAX du formulaire
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = {
                nomComplet: document.getElementById('nomComplet').value,
                userName: document.getElementById('userName').value,
                compte: document.getElementById('compte').value,
                motDePasse: document.getElementById('motDePasse').value,
                structure: document.getElementById('structure').value
            };

            fetch('ajout_user_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.message.includes('successfully')) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('Erreur : ' + error.message);
            });
        });
    </script>
</body>
</html>

<?php require_once('../Template/footer.php'); ?>
