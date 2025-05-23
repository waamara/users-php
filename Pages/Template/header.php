<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>PFE</title>
    <style>
        /* Styles pour le profil utilisateur dans l'en-tête */
        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            cursor: pointer;
        }
        
        .profile-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1775f1;
            transition: all 0.3s ease;
        }
        
        .profile-img:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(23, 117, 241, 0.5);
        }
        
        .profile-name {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }
        
        /* Style pour centrer le nom d'utilisateur dans l'en-tête */
        nav {
            position: relative;
        }
        
        .user-welcome {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 600;
            font-size: 16px;
            color: #1775f1;
        }
        
        /* Styles pour la carte de profil */
        .profile-card {
            position: absolute;
            top: 50px;
            right: 0;
            width: 280px;
            background: linear-gradient(135deg, #1775f1, #0c5fcd);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 20px;
            color: white;
            z-index: 1000;
            display: none;
            animation: fadeIn 0.3s ease;
            overflow: hidden;
        }
        
        .profile-card::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 60%);
            z-index: 0;
            animation: pulse 15s infinite linear;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.5;
            }
            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .profile-card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }
        
        .profile-card-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        
        .profile-card-info {
            position: relative;
            z-index: 1;
        }
        
        .profile-card-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .profile-card-role {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-card-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 15px 0;
            position: relative;
            z-index: 1;
        }
        
        .profile-card-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        
        .profile-card-action {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        
        .profile-card-action:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .profile-card-action i {
            font-size: 18px;
        }
        
        /* Styles existants */
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
        
        /* Séparateur dans la navigation */
        #content nav .divider {
            width: 1px;
            background: #e0e0e0;
            height: 12px;
            display: block;
        }
    </style>
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
    
    // Récupérer les informations de l'utilisateur pour l'affichage dans l'en-tête
    $userInfoStmt = $pdo->prepare("SELECT u.nom_user, u.prenom_user, u.username, r.nom_role, i.image_path 
                                  FROM users u 
                                  LEFT JOIN image_users i ON u.id = i.usersid 
                                  LEFT JOIN role r ON u.Role = r.id
                                  WHERE u.id = :user_id");
    $userInfoStmt->bindParam(':user_id', $user_id);
    $userInfoStmt->execute();
    $userInfo = $userInfoStmt->fetch(PDO::FETCH_ASSOC);
    
    // Définir le chemin de l'image par défaut si aucune image n'est trouvée
    $profileImage = "../../assets/images/default-profile.png";
    $fullName = "";
    $userRole = "";
    $username = "";
    
    if ($userInfo) {
        // Si l'utilisateur a une image de profil, utiliser celle-ci
        if (!empty($userInfo['image_path'])) {
            $profileImage = "../../" . $userInfo['image_path'];
        }
        
        // Construire le nom complet et récupérer le rôle
        $fullName = $userInfo['prenom_user'] . ' ' . $userInfo['nom_user'];
        $userRole = $userInfo['nom_role'];
        $username = $userInfo['username'];
    }
}
?>
<!-- MAIN CONTENT -->
<section id="content">
    <nav>
        <i class='bx bx-menu toggle-sidebar'></i>
        <form action="#">
            <div class="form-group">
                <p>helkoooooo world</p>
            </div>
        </form>
        
        <!-- Affichage du nom d'utilisateur au centre -->
        <?php if(isset($_SESSION['user_id']) && !empty($fullName)): ?>
        <div class="user-welcome">
            Bienvenue, <?php echo htmlspecialchars($fullName); ?>
        </div>
        <?php endif; ?>
      
        <span class="divider"></span>
        
        <!-- Affichage de la photo de profil et du nom -->
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="profile" id="profileToggle">
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Photo de profil" class="profile-img">
            <span class="profile-name"><?php echo htmlspecialchars($fullName); ?></span>
            
            <!-- Carte de profil qui s'affiche au clic -->
            <div class="profile-card" id="profileCard">
                <div class="profile-card-header">
                    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Photo de profil" class="profile-card-img">
                    <div class="profile-card-info">
                        <div class="profile-card-name"><?php echo htmlspecialchars($fullName); ?></div>
                        <div class="profile-card-role"><?php echo htmlspecialchars($userRole); ?></div>
                    </div>
                </div>
                
                <div class="profile-card-divider"></div>
                
                <div class="profile-card-actions">
                    <a href="profile.php" class="profile-card-action">
                        <i class='bx bx-user'></i>
                        <span>Mon profil</span>
                    </a>
                    <a href="settings.php" class="profile-card-action">
                        <i class='bx bx-cog'></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="logout.php" class="profile-card-action">
                        <i class='bx bx-log-out'></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </nav>
</section>

<script>
    // Script pour le toggle du sidebar
    document.querySelector('.toggle-sidebar').addEventListener('click', function() {
        document.querySelector('#sidebar').classList.toggle('hide');
    });
    
    // Script pour afficher/masquer la carte de profil
    const profileToggle = document.getElementById('profileToggle');
    const profileCard = document.getElementById('profileCard');
    
    if (profileToggle && profileCard) {
        // Afficher/masquer la carte au clic sur la photo de profil
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Empêche la propagation du clic
            profileCard.style.display = profileCard.style.display === 'block' ? 'none' : 'block';
        });
        
        // Fermer la carte si on clique ailleurs sur la page
        document.addEventListener('click', function(e) {
            if (profileCard.style.display === 'block' && !profileCard.contains(e.target) && e.target !== profileToggle) {
                profileCard.style.display = 'none';
            }
        });
        
        // Empêcher la fermeture si on clique dans la carte
        profileCard.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
</script>
</body>
</html>