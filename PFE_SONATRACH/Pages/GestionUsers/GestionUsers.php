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
</head>
<body>
    <div>
        <h2>Liste des Users</h2>
        <a href="">Ajouter un User</a>
    </div>

    <div>
        <div>
            <input type="text" id="searchInput" placeholder="Rechercher...">
            <button type="button" id="clearSearch">Effacer</button>
        </div>

        <table id="garantiesTable">
            <thead>
                <tr>
                    <th>Nom Complet</th>
                    <th>User Name</th>
                    <th>Compte</th>
                    <th>Mot de passe</th>
                    <th>Structure</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
require_once('../Template/footer.php');
?>



