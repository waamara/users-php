<?php
session_start();

// Simple script to clear specific session data
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'clear_liberation_search') {
        // Clear the liberation search data from session
        unset($_SESSION['liberation_search']);
    }
}
?>
