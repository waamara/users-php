<?php
/**
 * Configuration de la base de données
 * Ce fichier contient les paramètres de connexion à la base de données
 */

// Paramètres de connexion
$db_host = 'localhost';
$db_name = 'db_sonatrach_dp';
$db_user = 'root';
$db_pass = '';

/**
 * Fonction pour se connecter à la base de données
 * @return PDO Instance de connexion PDO
 */
function connectDB() {
    global $db_host, $db_name, $db_user, $db_pass;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
} 
?>