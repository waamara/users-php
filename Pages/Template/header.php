<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>PFE</title>
</head>
<body>
<?php
// Vérifier si l'utilisateur est connecté
if(isset($_SESSION['user_id'])) {
    // Vérifier si l'utilisateur existe dans la table first_login
    require_once('../../db_connection/db_conn.php');

    $user_id = $_SESSION['user_id'];
    $checkFirstLoginStmt = $pdo->prepare("SELECT * FROM first_login WHERE user_id = :user_id");
    $checkFirstLoginStmt->bindParam(':user_id', $user_id);
    $checkFirstLoginStmt->execute();
    
    if($checkFirstLoginStmt->rowCount() == 0) {
        // L'utilisateur N'EXISTE PAS dans la table first_login, inclure sidebargris.php
        include('sidebargris.php');
    } else {
        // L'utilisateur EXISTE dans la table first_login
        // Inclure le sidebar selon le rôle
       // Fetch user details including role
$getUserStmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$getUserStmt->bindParam(':user_id', $user_id);
$getUserStmt->execute();
$user = $getUserStmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $roleId = $user['Role'];
    $stmt = $pdo->prepare("SELECT nom_role FROM role WHERE id = :id");
    $stmt->execute(['id' => $roleId]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_role'] = $role['nom_role']; 
    
    if($_SESSION['user_role'] == 'admin') {
        include('sidebar.php');
    } elseif($_SESSION['user_role'] == 'agent') {
        include('sidebaragent.php');
    } elseif($_SESSION['user_role'] == 'responsable') {
        include('sidebarresponsable.php');
    }
}

    }
}
?>
<!-- MAIN CONTENT -->
<section id="content">
    <nav>
        <i class='bx bx-menu toggle-sidebar'></i>
        <form action="#">
            <div class="form-group">
             
            </div>
        </form>
      
        <span class="divider"></span>
        <div class="profile">
        </div>
    </nav> 
	<style>
/* Reset CSS pour le sidebar - Empêche Bootstrap d'affecter le sidebar */
#sidebar {
  /* Reset des propriétés de base */
  all: unset;
  position: fixed !important;
  max-width: 260px !important;
  width: 100% !important;
  background: #fff !important;
  top: 0 !important;
  left: 0 !important;
  height: 100% !important;
  overflow-y: auto !important;
  scrollbar-width: none !important;
  transition: all .3s ease !important;
  z-index: 200 !important;
  box-sizing: border-box !important;
  font-family: "Open Sans", sans-serif !important;
}

/* Ligne de séparation verticale qui commence un peu en dessous du haut */
#sidebar::after {
  content: "" !important;
  position: absolute !important;
  top: 80px !important; /* Commence 80px en dessous du haut */
  right: 0 !important;
  width: 1px !important;
  height: calc(100% - 100px) !important; /* Hauteur ajustée pour ne pas aller jusqu'en bas */
  background-color: #e0e0e0 !important;
  box-shadow: 1px 0 3px rgba(0, 0, 0, 0.05) !important;
  z-index: 201 !important;
}

/* Ajustement pour le sidebar réduit */
#sidebar.hide::after {
  content: "" !important;
  right: 0 !important;
}

#sidebar.hide {
  max-width: 60px !important;
  border-right: 1px solid #e0e0e0 !important;
}

#sidebar.hide:hover {
  max-width: 260px !important;
}

#sidebar::-webkit-scrollbar {
  display: none !important;
}

/* Reset pour la marque */
#sidebar .brand {
  font-size: 24px !important;
  display: flex !important;
  align-items: center !important;
  height: 64px !important;
  font-weight: 700 !important;
  color: #1775f1 !important;
  position: sticky !important;
  top: 0 !important;
  left: 0 !important;
  z-index: 100 !important;
  background: #fff !important;
  transition: all .3s ease !important;
  padding: 0 6px !important;
  margin: 0 !important;
  text-decoration: none !important;
  border: none !important;
}

/* Reset pour les icônes */
#sidebar .icon {
  min-width: 48px !important;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  margin-right: 6px !important;
  font-size: inherit !important;
}

#sidebar .icon-right {
  margin-left: auto !important;
  transition: all .3s ease !important;
}

/* Reset pour le menu latéral */
#sidebar .side-menu {
  margin: 36px 0 !important;
  padding: 0 20px !important;
  transition: all .3s ease !important;
  list-style: none !important;
}

#sidebar.hide .side-menu {
  padding: 0 6px !important;
}

#sidebar.hide:hover .side-menu {
  padding: 0 20px !important;
}

/* Reset pour les liens du menu */
#sidebar .side-menu a {
  display: flex !important;
  align-items: center !important;
  font-size: 14px !important;
  color: #000 !important;
  padding: 12px 16px 12px 0 !important;
  transition: all .3s ease !important;
  border-radius: 10px !important;
  margin: 4px 0 !important;
  white-space: nowrap !important;
  text-decoration: none !important;
  background: transparent !important;
  border: none !important;
}

#sidebar .side-menu > li > a:hover {
  background: #f1f0f6 !important;
}

/* Reset pour les liens actifs */
#sidebar .side-menu > li > a[data-page="dashboard"] {
  background: #1775f1 !important;
  color: #fff !important;
}

#sidebar .side-menu > li > a[data-page="dashboard"] .icon {
  color: #fff !important;
}

#sidebar .side-menu > li > a:not([data-page="dashboard"]).active {
  background: #1775f1 !important;
  color: #fff !important;
}

#sidebar .side-menu > li > a:not([data-page="dashboard"]).active .icon {
  color: #fff !important;
}

/* Reset pour le séparateur */
#sidebar .divider {
  margin-top: 24px !important;
  font-size: 12px !important;
  text-transform: uppercase !important;
  font-weight: 700 !important;
  color: #8d8d8d !important;
  transition: all .3s ease !important;
  white-space: nowrap !important;
  padding: 0 !important;
  border: none !important;
  background: transparent !important;
  height: auto !important;
  opacity: 1 !important;
}

#sidebar.hide:hover .divider {
  text-align: left !important;
}

#sidebar.hide .divider {
  text-align: center !important;
}

/* Reset pour les menus déroulants */
#sidebar .side-dropdown {
  max-height: 0 !important;
  overflow-y: hidden !important;
  transition: all .15s ease !important;
  padding: 0 !important;
  margin: 0 !important;
  list-style: none !important;
}

#sidebar .side-dropdown.show {
  max-height: 1000px !important;
}

#sidebar .side-dropdown a:hover {
  color: #1775f1 !important;
}

/* Reset pour les publicités */
#sidebar .ads {
  width: 100% !important;
  padding: 20px !important;
}

#sidebar.hide .ads {
  display: none !important;
}

#sidebar.hide:hover .ads {
  display: block !important;
}

#sidebar .ads .wrapper {
  background: #f1f0f6 !important;
  padding: 20px !important;
  border-radius: 10px !important;
}

#sidebar .btn-upgrade {
  font-size: 14px !important;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  padding: 12px 0 !important;
  color: #fff !important;
  background: #1775f1 !important;
  transition: all .3s ease !important;
  border-radius: 5px !important;
  font-weight: 600 !important;
  margin-bottom: 12px !important;
  text-decoration: none !important;
  border: none !important;
}

#sidebar .btn-upgrade:hover {
  background: #0c5fcd !important;
}

#sidebar .ads .wrapper p {
  font-size: 12px !important;
  color: #8d8d8d !important;
  text-align: center !important;
  margin: 0 !important;
}

#sidebar .ads .wrapper p span {
  font-weight: 700 !important;
}

/* Reset pour le contenu principal */
#content {
  position: relative !important;
  width: calc(100% - 260px) !important;
  left: 260px !important;
  transition: all .3s ease !important;
}

#sidebar.hide + #content {
  width: calc(100% - 60px) !important;
  left: 60px !important;
}

/* Reset pour la navigation */
#content nav {
  background: #fff !important;
  height: 64px !important;
  padding: 0 20px !important;
  display: flex !important;
  align-items: center !important;
  grid-gap: 28px !important;
  position: sticky !important;
  top: 0 !important;
  left: 0 !important;
  z-index: 100 !important;
}

/* Réinitialisation des éléments de liste */
#sidebar li {
  list-style: none !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* Réinitialisation des éléments spécifiques qui pourraient être affectés par Bootstrap */
#sidebar .side-menu > li > a:not([data-page]).active {
  background: transparent !important;
  color: #000 !important;
}

#sidebar .side-menu > li > a:not([data-page]) .icon {
  color: #000 !important;
}

#sidebar .side-menu > li > a:not([data-page]).active .icon {
  color: #1775f1 !important;
}

#sidebar .side-menu > li > a:hover .icon {
  color: #1775f1 !important;
}

/* Force white icons for last 3 items when active/hovered */
#sidebar .side-menu > li:nth-last-child(-n + 3) > a.active .icon,
#sidebar .side-menu > li:nth-last-child(-n + 3) > a.active:hover .icon {
  color: #fff !important;
}

/* Ensure blue hover only for non-active items */
#sidebar .side-menu > li:nth-last-child(-n + 3) > a:not(.active):hover .icon {
  color: #1775f1 !important;
}

/* Réinitialisation des marges et paddings pour tous les éléments du sidebar */
#sidebar * {
  box-sizing: border-box !important;
}

	</style>
