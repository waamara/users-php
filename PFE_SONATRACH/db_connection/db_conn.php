<?php
$host = "localhost"; // cuz WAMP server runs MySQL on localhost
$dbname = "db_sonatrach_dp";
$username = "root"; // hada Default WAMP MySQL username
$password = "";
try {
    // Create a new PDO instance (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error reporting
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
