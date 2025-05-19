<?php
require_once("../../../db_connection/db_conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $garantieId = isset($_POST['garantie_id']) ? intval($_POST['garantie_id']) : 0;
    $typeAmandement = isset($_POST['type_amd_id']) ? intval($_POST['type_amd_id']) : 0;
    $dateAmandement = isset($_POST['date_sys']) ? trim($_POST['date_sys']) : '';
    $datePronongation = isset($_POST['date_prorogation']) ? trim($_POST['date_prorogation']) : null;
    $montant = isset($_POST['montant_amd']) ? floatval($_POST['montant_amd']) : null;
    $numAmandement = isset($_POST['num_amd']) ? trim($_POST['num_amd']) : '';

    if (!$numAmandement) {
        die(json_encode(['success' => false, 'message' => 'Le numéro d\'amendement est requis.']));
    }

    try {
        // Check for duplicate num_amd
        $sqlCheckDuplicate = "SELECT COUNT(*) as count FROM amandement WHERE num_amd = ?";
        $stmtCheck = $pdo->prepare($sqlCheckDuplicate);
        $stmtCheck->execute([$numAmandement]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            die(json_encode(['success' => false, 'message' => 'Le numéro d\'amendement existe déjà.']));
        }

        // Insert into amandement table
        $sql = "INSERT INTO amandement (
                    num_amd, date_sys, date_prorogation, montant_amd, garantie_id, type_amd_id
                ) VALUES (
                    :num_amd, :date_sys, :date_prorogation, :montant_amd, :garantie_id, :type_amd_id
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':num_amd' => $numAmandement,
            ':date_sys' => $dateAmandement,
            ':date_prorogation' => $datePronongation,
            ':montant_amd' => $montant,
            ':garantie_id' => $garantieId,
            ':type_amd_id' => $typeAmandement,
        ]);

        $amendmentId = $pdo->lastInsertId();

        // Handle file upload
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['document']['tmp_name'];
            $fileName = basename($_FILES['document']['name']);
            $filePath = 'uploads/' . $fileName;

            if (!is_dir('uploads/')) {
                mkdir('uploads/', 0777, true);
            }

            if (!move_uploaded_file($fileTmpPath, $filePath)) {
                die(json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier.']));
            }

            $sqlDocument = "INSERT INTO document_amandement (
                                nom_document, document_path, garantie_id, Amandement_id
                            ) VALUES (
                                :nom_document, :document_path, :garantie_id, :amendment_id
                            )";

            $stmtDocument = $pdo->prepare($sqlDocument);
            $stmtDocument->execute([
                ':nom_document' => $fileName,
                ':document_path' => $filePath,
                ':garantie_id' => $garantieId,
                ':amendment_id' => $amendmentId,
            ]);
        }

        // Fetch updated list of amendments
        $sql = "SELECT 
                    a.*, 
                    t.label AS type_label, 
                    da.nom_document, 
                    da.document_path 
                FROM 
                    amandement a 
                LEFT JOIN 
                    type_amd t ON a.type_amd_id = t.id 
                LEFT JOIN 
                    document_amandement da ON a.id = da.Amandement_id 
                WHERE 
                    a.garantie_id = ? 
                ORDER BY 
                    a.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$garantieId]);
        $amendments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $amendments]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion des données: ' . $e->getMessage()]);
    }
}
?>