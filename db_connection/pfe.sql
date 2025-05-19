SELECT id, code, label, adresse, banque_id 
  FROM agence;
SELECT id, num_amd, date_sys, date_prorogation, montant_amd, garantie_id, Type_amd_id 
  FROM Amandement;
SELECT id, date_appel_offre, num_appel_offre 
  FROM appel_offre;
SELECT id, num_auth, date_depo, date_auth, garantie_id 
  FROM authentification;
SELECT id, code, label 
  FROM banque;
SELECT id, code, libelle 
  FROM direction;
SELECT id, nom_document, document_path, garantie_id, Amandement_id 
  FROM document_amandement;
SELECT id, nom_document, document_path, authentification_id 
  FROM document_auth;
SELECT id, nom_document, document_path 
  FROM document_garantie;
SELECT id, nom_document, document_path, liberation_id 
  FROM document_liberation;
SELECT id, code_fournisseur, nom_fournisseur, raison_sociale, pays_id 
  FROM fournisseur;
SELECT id, num_garantie, date_creation, date_emission, date_validite, montant, direction_id, fournisseur_id, monnaie_id, agence_id, appel_offre_id 
  FROM garantie;
SELECT id, nom_image, image_path, usersid 
  FROM image_users;
SELECT id, num, date_liberation, Type_liberation_id, garantie_id 
  FROM liberation;
SELECT id, code, label, symbole 
  FROM monnaie;
SELECT id, code, label 
  FROM pays;
SELECT id, nom_role 
  FROM Role;
SELECT id, code, label 
  FROM Type_amd;
SELECT id, code, label 
  FROM Type_liberation;
SELECT id, nom_user, prenom_user, username, password, status, Role, structure 
  FROM users;
