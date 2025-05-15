<?php
session_start();
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Users</title>
    <link rel="stylesheet" href="../GestionUsers/css/GestionUsers.css">
</head>

<body>
    <div>
        <h2>Liste des Users</h2>
        <a href="#" id="addUserLink">Ajouter un User</a>
    </div>

    <div>
        <div>
            <input type="text" id="searchInput" placeholder="Rechercher...">
        </div>

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
                            <a href='ActionUsers.php?id=" . $user['id'] . "' class='edit-btn'>Actions</a>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Form -->
    <div id="userFormModal" class="modal">
        <div class="modal-content">
            <span id="closeFormBtn" class="close-btn">&times;</span>
            <h3>Ajouter un Nouveau User</h3>
            <form id="userForm">
                <div class="form-group">
                    <label for="nomComplet">Nom Complet:</label>
                    <input type="text" id="nomComplet">
                    <div class="validation-message" style="display: none; color: red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="userName">User Name:</label>
                    <input type="text" id="userName">
                    <div class="validation-message" style="display: none; color: red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="compte">Compte:</label>
                    <select id="compte">
                        <option value="">-- Sélectionnez un état --</option>
                        <option value="actif">Actif</option>
                        <option value="desactive">Désactivé</option>
                    </select>
                    <div class="validation-message" style="display: none; color: red;">Ce champ est requis</div>
                </div>


                <div class="form-group">
                    <label for="motDePasse">Mot de Passe:</label>
                    <input type="password" id="motDePasse">
                    <div class="validation-message" style="display: none; color: red;">Ce champ est requis</div>
                </div>

                <div class="form-group">
                    <label for="structure">Structure:</label>
                    <input type="text" id="structure">
                    <div class="validation-message" style="display: none; color: red;">Ce champ est requis</div>
                </div>

                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <script src="../GestionUsers/js/Gestionusers.js"></script>
</body>

</html>

<?php
require_once('../Template/footer.php');
?>