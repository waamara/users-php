<?php
// Script à exécuter quotidiennement via cron job
// Exemple: 0 0 * * * php /path/to/check_expiring_garanties.php

// Inclure la configuration de la base de données
require_once '../config/database.php';

// Créer une connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Dates de référence
$today = date('Y-m-d');
$in30days = date('Y-m-d', strtotime('+30 days'));
$in10days = date('Y-m-d', strtotime('+10 days'));
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Récupérer toutes les garanties actives qui expirent dans les 30 prochains jours
$query = "SELECT g.*, 
          DATEDIFF(g.date_validite, :today) AS jours_restants,
          d.responsable_id AS utilisateur_id,
          f.nom AS fournisseur_nom,
          m.symbole AS monnaie_symbole
          FROM garantie g
          LEFT JOIN direction d ON g.direction_id = d.id
          LEFT JOIN fournisseur f ON g.fournisseur_id = f.id
          LEFT JOIN monnaie m ON g.monnaie_id = m.id
          WHERE g.date_validite BETWEEN :today AND :in30days
          ORDER BY g.date_validite ASC";

$stmt = $db->prepare($query);
$stmt->bindParam(':today', $today);
$stmt->bindParam(':in30days', $in30days);
$stmt->execute();

$garanties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les garanties déjà expirées (pour les alertes critiques)
$queryExpired = "SELECT g.*, 
                DATEDIFF(g.date_validite, :today) AS jours_restants,
                d.responsable_id AS utilisateur_id,
                f.nom AS fournisseur_nom,
                m.symbole AS monnaie_symbole
                FROM garantie g
                LEFT JOIN direction d ON g.direction_id = d.id
                LEFT JOIN fournisseur f ON g.fournisseur_id = f.id
                LEFT JOIN monnaie m ON g.monnaie_id = m.id
                WHERE g.date_validite < :today
                ORDER BY g.date_validite DESC";

$stmtExpired = $db->prepare($queryExpired);
$stmtExpired->bindParam(':today', $today);
$stmtExpired->execute();

$garantiesExpirees = $stmtExpired->fetchAll(PDO::FETCH_ASSOC);

// Fusionner les deux listes
$allGaranties = array_merge($garanties, $garantiesExpirees);

// Récupérer les préférences personnalisées
$queryPrefs = "SELECT * FROM garanties_preferences";
$stmtPrefs = $db->prepare($queryPrefs);
$stmtPrefs->execute();
$preferences = $stmtPrefs->fetchAll(PDO::FETCH_ASSOC);

// Organiser les préférences par garantie_id
$prefsMap = [];
foreach ($preferences as $pref) {
    $prefsMap[$pref['garantie_id']] = $pref;
}

// Traiter chaque garantie
foreach ($allGaranties as $garantie) {
    $joursRestants = $garantie['jours_restants'];
    $garantieId = $garantie['id'];
    $utilisateurId = $garantie['utilisateur_id'];
    
    // Déterminer le type d'alerte
    $typeAlerte = '';
    
    if ($joursRestants <= 0) {
        $typeAlerte = 'critical'; // Expirée ou expire aujourd'hui
    } elseif ($joursRestants <= 10) {
        $typeAlerte = 'urgent'; // Expire dans 1-10 jours
    } elseif ($joursRestants <= 30) {
        $typeAlerte = 'preventive'; // Expire dans 11-30 jours
    }
    
    // Vérifier si une alerte a déjà été envoyée aujourd'hui pour cette garantie
    $queryCheckAlert = "SELECT id FROM garanties_alertes 
                        WHERE garantie_id = :garantie_id 
                        AND type_alerte = :type_alerte
                        AND DATE(date_alerte) = :today";
    
    $stmtCheckAlert = $db->prepare($queryCheckAlert);
    $stmtCheckAlert->bindParam(':garantie_id', $garantieId);
    $stmtCheckAlert->bindParam(':type_alerte', $typeAlerte);
    $stmtCheckAlert->bindParam(':today', $today);
    $stmtCheckAlert->execute();
    
    // Si aucune alerte n'a été envoyée aujourd'hui et que le type d'alerte est défini
    if ($stmtCheckAlert->rowCount() == 0 && !empty($typeAlerte)) {
        // Créer l'alerte
        $queryInsert = "INSERT INTO garanties_alertes 
                        (garantie_id, type_alerte, jours_restants, utilisateur_id) 
                        VALUES (:garantie_id, :type_alerte, :jours_restants, :utilisateur_id)";
        
        $stmtInsert = $db->prepare($queryInsert);
        $stmtInsert->bindParam(':garantie_id', $garantieId);
        $stmtInsert->bindParam(':type_alerte', $typeAlerte);
        $stmtInsert->bindParam(':jours_restants', $joursRestants);
        $stmtInsert->bindParam(':utilisateur_id', $utilisateurId);
        $stmtInsert->execute();
    }
    
    // Vérifier les préférences personnalisées
    if (isset($prefsMap[$garantieId])) {
        $pref = $prefsMap[$garantieId];
        $joursNotif = $pref['jours_notification'];
        
        // Si le nombre de jours restants correspond exactement à la préférence
        if ($joursRestants == $joursNotif) {
            // Vérifier si une alerte personnalisée a déjà été envoyée aujourd'hui
            $queryCheckCustom = "SELECT id FROM garanties_alertes 
                                WHERE garantie_id = :garantie_id 
                                AND type_alerte = 'custom'
                                AND jours_restants = :jours_restants
                                AND DATE(date_alerte) = :today";
            
            $stmtCheckCustom = $db->prepare($queryCheckCustom);
            $stmtCheckCustom->bindParam(':garantie_id', $garantieId);
            $stmtCheckCustom->bindParam(':jours_restants', $joursRestants);
            $stmtCheckCustom->bindParam(':today', $today);
            $stmtCheckCustom->execute();
            
            // Si aucune alerte personnalisée n'a été envoyée aujourd'hui
            if ($stmtCheckCustom->rowCount() == 0) {
                // Créer l'alerte personnalisée
                $queryInsertCustom = "INSERT INTO garanties_alertes 
                                    (garantie_id, type_alerte, jours_restants, utilisateur_id) 
                                    VALUES (:garantie_id, 'custom', :jours_restants, :utilisateur_id)";
                
                $stmtInsertCustom = $db->prepare($queryInsertCustom);
                $stmtInsertCustom->bindParam(':garantie_id', $garantieId);
                $stmtInsertCustom->bindParam(':jours_restants', $joursRestants);
                $stmtInsertCustom->bindParam(':utilisateur_id', $pref['utilisateur_id']);
                $stmtInsertCustom->execute();
            }
        }
    }
}

echo "Vérification des garanties terminée. " . count($allGaranties) . " garanties traitées.\n";
?>