
   <!-- SIDEBAR -->
<section id="sidebar">
    <a href="#" class="brand"> </a>
    <ul class="side-menu">
        <!-- Dashboard -->
        <li><a href="../Role/admindash.php" data-page="dashboard" class="active">
            <i class='bx bxs-dashboard icon'></i> Dashboard
        </a></li>

        <!-- Main Folders -->
        <li>
            <a href="#"><i class='bx bx-folder icon'></i> Données de base 
                <i class='bx bx-chevron-right icon-right'></i>
            </a>
            <ul class="side-dropdown">
                <li><a href="../Direction/index.php" data-page="divisions">
                    <i class='bx bx-grid-alt icon'></i> Directions
                </a></li>
                <li><a href="../Fournisseur/Fournisseur.php" data-page="fournisseurs">
                    <i class='bx bx-user-pin icon'></i> Fournisseurs
                </a></li>
                <li><a href="../Banque/Banque.php" data-page="banque">
                    <i class='bx bx-building icon'></i> Banque
                </a></li>
                <li><a href="../Monnaie/monnaie.php" data-page="monnaie">
                    <i class='bx bx-dollar-circle icon'></i> Monnaie
                </a></li>
                <li><a href="../Appel_offre/appel_offre.php" data-page="divisions">
                    <i class='bx bx-grid-alt icon'></i> Appel Offre
                </a></li>
            </ul>
        </li>

        <!-- Garantie bancaire -->
        <li>
    <a href="#"><i class='bx bx-shield icon'></i> Garantie bancaire 
        <i class='bx bx-chevron-right icon-right'></i>
    </a>
    <ul class="side-dropdown">
        <li><a href="../garantie/ListeGaranties.php" data-page="soumission">
        <i class='bx bx-list-ul icon'></i>
        Liste des Garanties
        </a></li>
        <li><a href="../Recherche/recherche_details.php" data-page="soumission">
        <i class='bx bx-search-alt icon'></i>        Rechercher Garanties
        </a></li>
        <li><a href="../Recherche/recherche_details.php" data-page="soumission">
        <i class='bx bx-folder-open icon'></i>
        Rechercher Libérations
        </a></li>
    </ul>
</li>

        <li class="element"><a href="../GestionUsers/GestionUsers.php" data-page="notifications">
        <i class='bx bx-user icon'></i>Gestion des Utilisateurs
        </a></li>
         <li class="element"><a href="../garantie/alertes_garanties.php" data-page="notifications">
            <i class='bx bx-bell icon'></i> Alertes & Notifications
        </a></li>
        <!-- Other Pages -->
        <li class="element"><a href="#" data-page="historique">
        <i class='bx bx-bot icon'></i>Bot Assistant 
        </a></li>
        <li class="element"><a href="#" data-page="historique">
            <i class='bx bx-history icon'></i> Historique
        </a></li>
        
       
        <li class="element">
  <a href="../Login/logout.php" data-page="logout">
    <i class='bx bx-log-out icon'></i> Logout
  </a>
</li>

    </ul>
</section>
