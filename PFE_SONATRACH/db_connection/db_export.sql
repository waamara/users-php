-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 12 mai 2025 à 12:25
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14
SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;

/*!40101 SET NAMES utf8mb4 */
;

--
-- Base de données : `db_sonatrach_dp`
--
-- --------------------------------------------------------
--
-- Structure de la table `agence`
--
DROP TABLE IF EXISTS `agence`;

CREATE TABLE IF NOT EXISTS `agence` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL,
    `label` varchar(50) NOT NULL,
    `adresse` varchar(50) NOT NULL,
    `banque_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `label` (`label`),
    KEY `fk_banque_agence` (`banque_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `agence`
--
INSERT INTO
    `agence` (`id`, `code`, `label`, `adresse`, `banque_id`)
VALUES
    (1, '', '', '', 0),
    (2, '1', '1', '1', 1);

-- --------------------------------------------------------
--
-- Structure de la table `amandement`
--
DROP TABLE IF EXISTS `amandement`;

CREATE TABLE IF NOT EXISTS `amandement` (
    `id` int NOT NULL AUTO_INCREMENT,
    `num_amd` varchar(50) NOT NULL,
    `date_sys` date NOT NULL,
    `date_prorogation` date DEFAULT NULL,
    `montant_amd` decimal(15, 5) DEFAULT NULL,
    `garantie_id` int NOT NULL,
    `type_amd_id` int DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `num_amd` (`num_amd`),
    KEY `fk_gar_amd` (`garantie_id`),
    KEY `fk_type_amd` (`type_amd_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 45 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `amandement`
--
INSERT INTO
    `amandement` (
        `id`,
        `num_amd`,
        `date_sys`,
        `date_prorogation`,
        `montant_amd`,
        `garantie_id`,
        `type_amd_id`
    )
VALUES
    (
        40,
        '5687841',
        '2025-04-02',
        '0000-00-00',
        133.00000,
        1,
        2
    ),
    (
        39,
        '5687841111',
        '2025-04-05',
        '0000-00-00',
        288.00000,
        1,
        2
    ),
    (
        38,
        '56878422',
        '2025-04-01',
        '2025-04-28',
        2134.00000,
        1,
        1
    ),
    (
        36,
        '1',
        '2025-03-06',
        '2025-03-25',
        9.00000,
        1,
        1
    ),
    (
        37,
        '568784',
        '2024-01-02',
        '2025-03-06',
        566.00000,
        1,
        1
    ),
    (
        35,
        '5669889',
        '2025-03-03',
        '0000-00-00',
        0.00000,
        1,
        1
    ),
    (
        34,
        '5668',
        '2025-03-03',
        '0000-00-00',
        0.00000,
        1,
        2
    ),
    (
        41,
        '56878412',
        '2025-04-01',
        '2025-04-02',
        0.00000,
        1,
        3
    ),
    (
        42,
        '7',
        '2025-04-01',
        '2025-04-21',
        0.00000,
        1,
        3
    ),
    (
        43,
        '123ttt',
        '2025-04-01',
        '0000-00-00',
        123.00000,
        1,
        2
    ),
    (
        44,
        '1892U842318642',
        '2025-04-01',
        '0000-00-00',
        12314.00000,
        1,
        2
    );

-- --------------------------------------------------------
--
-- Structure de la table `appel_offre`
--
DROP TABLE IF EXISTS `appel_offre`;

CREATE TABLE IF NOT EXISTS `appel_offre` (
    `id` int NOT NULL AUTO_INCREMENT,
    `date_appel_offre` date NOT NULL,
    `num_appel_offre` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `num_appel_offre` (`num_appel_offre`)
) ENGINE = MyISAM AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `appel_offre`
--
INSERT INTO
    `appel_offre` (`id`, `date_appel_offre`, `num_appel_offre`)
VALUES
    (1, '2025-04-15', 'AO12'),
    (2, '2025-04-30', 'AO13');

-- --------------------------------------------------------
--
-- Structure de la table `authentification`
--
DROP TABLE IF EXISTS `authentification`;

CREATE TABLE IF NOT EXISTS `authentification` (
    `id` int NOT NULL AUTO_INCREMENT,
    `num_auth` varchar(50) NOT NULL,
    `date_depo` date NOT NULL,
    `date_auth` date NOT NULL,
    `garantie_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `num_auth` (`num_auth`),
    KEY `fk_gar_auth` (`garantie_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `banque`
--
DROP TABLE IF EXISTS `banque`;

CREATE TABLE IF NOT EXISTS `banque` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5) NOT NULL,
    `label` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `label` (`label`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `direction`
--
DROP TABLE IF EXISTS `direction`;

CREATE TABLE IF NOT EXISTS `direction` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5) NOT NULL,
    `libelle` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `libelle` (`libelle`)
) ENGINE = MyISAM AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `direction`
--
INSERT INTO
    `direction` (`id`, `code`, `libelle`)
VALUES
    (1, '213', 'aymen');

-- --------------------------------------------------------
--
-- Structure de la table `document_amandement`
--
DROP TABLE IF EXISTS `document_amandement`;

CREATE TABLE IF NOT EXISTS `document_amandement` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_document` varchar(50) NOT NULL,
    `document_path` varchar(100) NOT NULL,
    `garantie_id` int NOT NULL,
    `Amandement_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nom_document` (`nom_document`),
    KEY `fk_amd_doc` (`Amandement_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 13 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `document_amandement`
--
INSERT INTO
    `document_amandement` (
        `id`,
        `nom_document`,
        `document_path`,
        `garantie_id`,
        `Amandement_id`
    )
VALUES
    (
        1,
        'RECU DE PAIEMENT (3).pdf',
        'uploads/RECU DE PAIEMENT (3).pdf',
        1,
        35
    ),
    (
        2,
        'DM ADMIN .pdf',
        'uploads/DM ADMIN .pdf',
        1,
        36
    ),
    (
        3,
        'Capture d\'écran 2025-03-26 183521.pdf',
        'uploads/Capture d\'écran 2025-03-26 183521.pdf',
        1,
        37
    ),
    (4, 'pda3.pdf', 'uploads/pda3.pdf', 1, 37),
    (
        5,
        'dzexams-2as-sciences-naturelles-583691.pdf',
        'uploads/dzexams-2as-sciences-naturelles-583691.pdf',
        1,
        38
    ),
    (
        6,
        'Solution_TP1 (2).pdf',
        'uploads/Solution_TP1 (2).pdf',
        1,
        39
    ),
    (7, 'pda2.pdf', 'uploads/pda2.pdf', 1, 39),
    (
        8,
        'Document sans titre (10).pdf',
        'uploads/Document sans titre (10).pdf',
        1,
        40
    ),
    (9, 'pda1.pdf', 'uploads/pda1.pdf', 1, 41),
    (
        10,
        'TP3-ADM_CS.pdf',
        'uploads/TP3-ADM_CS.pdf',
        1,
        42
    ),
    (
        11,
        'Sujet (28).pdf',
        'uploads/Sujet (28).pdf',
        1,
        43
    ),
    (
        12,
        'dzexams-bac-mathematiques-3372206.pdf',
        'uploads/dzexams-bac-mathematiques-3372206.pdf',
        1,
        44
    );

-- --------------------------------------------------------
--
-- Structure de la table `document_auth`
--
DROP TABLE IF EXISTS `document_auth`;

CREATE TABLE IF NOT EXISTS `document_auth` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_document` varchar(50) NOT NULL,
    `document_path` varchar(100) NOT NULL,
    `authentification_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nom_document` (`nom_document`),
    KEY `fk_auth_doc` (`authentification_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `document_garantie`
--
DROP TABLE IF EXISTS `document_garantie`;

CREATE TABLE IF NOT EXISTS `document_garantie` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_document` varchar(50) NOT NULL,
    `document_path` varchar(100) NOT NULL,
    `garantie_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nom_document` (`nom_document`),
    KEY `fk_gar_doc` (`garantie_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `document_liberation`
--
DROP TABLE IF EXISTS `document_liberation`;

CREATE TABLE IF NOT EXISTS `document_liberation` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_document` varchar(50) NOT NULL,
    `document_path` varchar(100) NOT NULL,
    `liberation_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nom_document` (`nom_document`),
    KEY `fk_lib_doc` (`liberation_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `fournisseur`
--
DROP TABLE IF EXISTS `fournisseur`;

CREATE TABLE IF NOT EXISTS `fournisseur` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code_fournisseur` varchar(50) NOT NULL,
    `nom_fournisseur` varchar(50) NOT NULL,
    `raison_sociale` varchar(255) NOT NULL,
    `pays_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code_fournisseur` (`code_fournisseur`),
    UNIQUE KEY `nom_fournisseur` (`nom_fournisseur`),
    KEY `fk_pays_fournisseur` (`pays_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 37 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `fournisseur`
--
INSERT INTO
    `fournisseur` (
        `id`,
        `code_fournisseur`,
        `nom_fournisseur`,
        `raison_sociale`,
        `pays_id`
    )
VALUES
    (35, '234', 'BERBICHE', 'nothing special', 10),
    (1, 'rf', 'd', 'az', 4),
    (13, 'g', 'f', 'd', 14),
    (14, ',', 'b', 'n', 10),
    (16, 'a', 'e', 'r', 13),
    (
        34,
        'ga',
        'gggggggggggggggg',
        'gggggggggggggg',
        14
    ),
    (19, 'ezer', 'hngj', ',', 16),
    (33, 'aqsfez', 'aeerg', 'rrf', 15),
    (32, 'aaaaaa', 'aaaaa', 'DFR', 69),
    (
        31,
        'HS_002',
        'BERBICHE_àà2',
        'nothing special',
        15
    ),
    (30, 'nontesst', 'test', 'test', 239),
    (29, 'amine', 'rigel', 'a', 13),
    (36, 'oh it works', 'now', 'nh', 16);

-- --------------------------------------------------------
--
-- Structure de la table `garantie`
--
DROP TABLE IF EXISTS `garantie`;

CREATE TABLE IF NOT EXISTS `garantie` (
    `id` int NOT NULL AUTO_INCREMENT,
    `num_garantie` varchar(50) NOT NULL,
    `date_creation` date NOT NULL,
    `date_emission` date NOT NULL,
    `date_validite` date NOT NULL,
    `montant` decimal(15, 3) NOT NULL,
    `direction_id` int NOT NULL,
    `fournisseur_id` int NOT NULL,
    `monnaie_id` int NOT NULL,
    `agence_id` int NOT NULL,
    `appel_offre_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `num_garantie` (`num_garantie`),
    KEY `fk_agence_gar` (`agence_id`),
    KEY `fk_appel_gar` (`appel_offre_id`),
    KEY `fk_dir_gar` (`direction_id`),
    KEY `fk_fournisseur_gar` (`fournisseur_id`),
    KEY `fk_monnaie_gar` (`monnaie_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `garantie`
--
INSERT INTO
    `garantie` (
        `id`,
        `num_garantie`,
        `date_creation`,
        `date_emission`,
        `date_validite`,
        `montant`,
        `direction_id`,
        `fournisseur_id`,
        `monnaie_id`,
        `agence_id`,
        `appel_offre_id`
    )
VALUES
    (
        1,
        'firstgaranite',
        '0000-00-00',
        '0000-00-00',
        '0000-00-00',
        300000.000,
        1,
        1,
        1,
        1,
        1
    );

-- --------------------------------------------------------
--
-- Structure de la table `image_users`
--
DROP TABLE IF EXISTS `image_users`;

CREATE TABLE IF NOT EXISTS `image_users` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_image` varchar(50) NOT NULL,
    `image_path` varchar(100) NOT NULL,
    `usersid` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_users_image` (`usersid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `liberation`
--
DROP TABLE IF EXISTS `liberation`;

CREATE TABLE IF NOT EXISTS `liberation` (
    `id` int NOT NULL AUTO_INCREMENT,
    `num` int NOT NULL,
    `date_liberation` date NOT NULL,
    `Type_liberation_id` int NOT NULL,
    `garantie_id` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_gar_lib` (`garantie_id`),
    KEY `fk_lib_type` (`Type_liberation_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `monnaie`
--
DROP TABLE IF EXISTS `monnaie`;

CREATE TABLE IF NOT EXISTS `monnaie` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5) NOT NULL,
    `label` varchar(50) NOT NULL,
    `symbole` varchar(3) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `label` (`label`)
) ENGINE = MyISAM AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `monnaie`
--
INSERT INTO
    `monnaie` (`id`, `code`, `label`, `symbole`)
VALUES
    (1, 'USA', 'Dollar', 'AD');

-- --------------------------------------------------------
--
-- Structure de la table `pays`
--
DROP TABLE IF EXISTS `pays`;

CREATE TABLE IF NOT EXISTS `pays` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5) NOT NULL,
    `label` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `label` (`label`)
) ENGINE = MyISAM AUTO_INCREMENT = 250 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `pays`
--
INSERT INTO
    `pays` (`id`, `code`, `label`)
VALUES
    (1, 'AF', 'Afghanistan'),
    (2, 'AX', 'Åland Islands'),
    (3, 'AL', 'Albania'),
    (4, 'DZ', 'Algeria'),
    (5, 'AS', 'American Samoa'),
    (6, 'AD', 'Andorra'),
    (7, 'AO', 'Angola'),
    (8, 'AI', 'Anguilla'),
    (9, 'AQ', 'Antarctica'),
    (10, 'AG', 'Antigua and Barbuda'),
    (11, 'AR', 'Argentina'),
    (12, 'AM', 'Armenia'),
    (13, 'AW', 'Aruba'),
    (14, 'AU', 'Australia'),
    (15, 'AT', 'Austria'),
    (16, 'AZ', 'Azerbaijan'),
    (17, 'BS', 'Bahamas'),
    (18, 'BH', 'Bahrain'),
    (19, 'BD', 'Bangladesh'),
    (20, 'BB', 'Barbados'),
    (21, 'BY', 'Belarus'),
    (22, 'BE', 'Belgium'),
    (23, 'BZ', 'Belize'),
    (24, 'BJ', 'Benin'),
    (25, 'BM', 'Bermuda'),
    (26, 'BT', 'Bhutan'),
    (27, 'BO', 'Bolivia'),
    (28, 'BQ', 'Bonaire, Sint Eustatius and Saba'),
    (29, 'BA', 'Bosnia and Herzegovina'),
    (30, 'BW', 'Botswana'),
    (31, 'BV', 'Bouvet Island'),
    (32, 'BR', 'Brazil'),
    (33, 'IO', 'British Indian Ocean Territory'),
    (34, 'BN', 'Brunei Darussalam'),
    (35, 'BG', 'Bulgaria'),
    (36, 'BF', 'Burkina Faso'),
    (37, 'BI', 'Burundi'),
    (38, 'CV', 'Cabo Verde'),
    (39, 'KH', 'Cambodia'),
    (40, 'CM', 'Cameroon'),
    (41, 'CA', 'Canada'),
    (42, 'KY', 'Cayman Islands'),
    (43, 'CF', 'Central African Republic'),
    (44, 'TD', 'Chad'),
    (45, 'CL', 'Chile'),
    (46, 'CN', 'China'),
    (47, 'CX', 'Christmas Island'),
    (48, 'CC', 'Cocos (Keeling) Islands'),
    (49, 'CO', 'Colombia'),
    (50, 'KM', 'Comoros'),
    (51, 'CG', 'Congo'),
    (52, 'CD', 'Congo, Democratic Republic of the'),
    (53, 'CK', 'Cook Islands'),
    (54, 'CR', 'Costa Rica'),
    (55, 'CI', 'Côte d\'Ivoire'),
    (56, 'HR', 'Croatia'),
    (57, 'CU', 'Cuba'),
    (58, 'CW', 'Curaçao'),
    (59, 'CY', 'Cyprus'),
    (60, 'CZ', 'Czechia'),
    (61, 'DK', 'Denmark'),
    (62, 'DJ', 'Djibouti'),
    (63, 'DM', 'Dominica'),
    (64, 'DO', 'Dominican Republic'),
    (65, 'EC', 'Ecuador'),
    (66, 'EG', 'Egypt'),
    (67, 'SV', 'El Salvador'),
    (68, 'GQ', 'Equatorial Guinea'),
    (69, 'ER', 'Eritrea'),
    (70, 'EE', 'Estonia'),
    (71, 'SZ', 'Eswatini'),
    (72, 'ET', 'Ethiopia'),
    (73, 'FK', 'Falkland Islands (Malvinas)'),
    (74, 'FO', 'Faroe Islands'),
    (75, 'FJ', 'Fiji'),
    (76, 'FI', 'Finland'),
    (77, 'FR', 'France'),
    (78, 'GF', 'French Guiana'),
    (79, 'PF', 'French Polynesia'),
    (80, 'TF', 'French Southern Territories'),
    (81, 'GA', 'Gabon'),
    (82, 'GM', 'Gambia'),
    (83, 'GE', 'Georgia'),
    (84, 'DE', 'Germany'),
    (85, 'GH', 'Ghana'),
    (86, 'GI', 'Gibraltar'),
    (87, 'GR', 'Greece'),
    (88, 'GL', 'Greenland'),
    (89, 'GD', 'Grenada'),
    (90, 'GP', 'Guadeloupe'),
    (91, 'GU', 'Guam'),
    (92, 'GT', 'Guatemala'),
    (93, 'GG', 'Guernsey'),
    (94, 'GN', 'Guinea'),
    (95, 'GW', 'Guinea-Bissau'),
    (96, 'GY', 'Guyana'),
    (97, 'HT', 'Haiti'),
    (98, 'HM', 'Heard Island and McDonald Islands'),
    (99, 'VA', 'Holy See'),
    (100, 'HN', 'Honduras'),
    (101, 'HK', 'Hong Kong'),
    (102, 'HU', 'Hungary'),
    (103, 'IS', 'Iceland'),
    (104, 'IN', 'India'),
    (105, 'ID', 'Indonesia'),
    (106, 'IR', 'Iran'),
    (107, 'IQ', 'Iraq'),
    (108, 'IE', 'Ireland'),
    (109, 'IM', 'Isle of Man'),
    (110, 'IL', 'Israel'),
    (111, 'IT', 'Italy'),
    (112, 'JM', 'Jamaica'),
    (113, 'JP', 'Japan'),
    (114, 'JE', 'Jersey'),
    (115, 'JO', 'Jordan'),
    (116, 'KZ', 'Kazakhstan'),
    (117, 'KE', 'Kenya'),
    (118, 'KI', 'Kiribati'),
    (119, 'KP', 'North Korea'),
    (120, 'KR', 'South Korea'),
    (121, 'KW', 'Kuwait'),
    (122, 'KG', 'Kyrgyzstan'),
    (123, 'LA', 'Laos'),
    (124, 'LV', 'Latvia'),
    (125, 'LB', 'Lebanon'),
    (126, 'LS', 'Lesotho'),
    (127, 'LR', 'Liberia'),
    (128, 'LY', 'Libya'),
    (129, 'LI', 'Liechtenstein'),
    (130, 'LT', 'Lithuania'),
    (131, 'LU', 'Luxembourg'),
    (132, 'MO', 'Macao'),
    (133, 'MG', 'Madagascar'),
    (134, 'MW', 'Malawi'),
    (135, 'MY', 'Malaysia'),
    (136, 'MV', 'Maldives'),
    (137, 'ML', 'Mali'),
    (138, 'MT', 'Malta'),
    (139, 'MH', 'Marshall Islands'),
    (140, 'MQ', 'Martinique'),
    (141, 'MR', 'Mauritania'),
    (142, 'MU', 'Mauritius'),
    (143, 'YT', 'Mayotte'),
    (144, 'MX', 'Mexico'),
    (145, 'FM', 'Micronesia'),
    (146, 'MD', 'Moldova'),
    (147, 'MC', 'Monaco'),
    (148, 'MN', 'Mongolia'),
    (149, 'ME', 'Montenegro'),
    (150, 'MS', 'Montserrat'),
    (151, 'MA', 'Morocco'),
    (152, 'MZ', 'Mozambique'),
    (153, 'MM', 'Myanmar'),
    (154, 'NA', 'Namibia'),
    (155, 'NR', 'Nauru'),
    (156, 'NP', 'Nepal'),
    (157, 'NL', 'Netherlands'),
    (158, 'NC', 'New Caledonia'),
    (159, 'NZ', 'New Zealand'),
    (160, 'NI', 'Nicaragua'),
    (161, 'NE', 'Niger'),
    (162, 'NG', 'Nigeria'),
    (163, 'NU', 'Niue'),
    (164, 'NF', 'Norfolk Island'),
    (165, 'MK', 'North Macedonia'),
    (166, 'MP', 'Northern Mariana Islands'),
    (167, 'NO', 'Norway'),
    (168, 'OM', 'Oman'),
    (169, 'PK', 'Pakistan'),
    (170, 'PW', 'Palau'),
    (171, 'PS', 'Palestine'),
    (172, 'PA', 'Panama'),
    (173, 'PG', 'Papua New Guinea'),
    (174, 'PY', 'Paraguay'),
    (175, 'PE', 'Peru'),
    (176, 'PH', 'Philippines'),
    (177, 'PN', 'Pitcairn'),
    (178, 'PL', 'Poland'),
    (179, 'PT', 'Portugal'),
    (180, 'PR', 'Puerto Rico'),
    (181, 'QA', 'Qatar'),
    (182, 'RE', 'Réunion'),
    (183, 'RO', 'Romania'),
    (184, 'RU', 'Russia'),
    (185, 'RW', 'Rwanda'),
    (186, 'BL', 'Saint Barthélemy'),
    (
        187,
        'SH',
        'Saint Helena, Ascension and Tristan da Cunha'
    ),
    (188, 'KN', 'Saint Kitts and Nevis'),
    (189, 'LC', 'Saint Lucia'),
    (190, 'MF', 'Saint Martin (French part)'),
    (191, 'PM', 'Saint Pierre and Miquelon'),
    (192, 'VC', 'Saint Vincent and the Grenadines'),
    (193, 'WS', 'Samoa'),
    (194, 'SM', 'San Marino'),
    (195, 'ST', 'São Tomé and Príncipe'),
    (196, 'SA', 'Saudi Arabia'),
    (197, 'SN', 'Senegal'),
    (198, 'RS', 'Serbia'),
    (199, 'SC', 'Seychelles'),
    (200, 'SL', 'Sierra Leone'),
    (201, 'SG', 'Singapore'),
    (202, 'SX', 'Sint Maarten (Dutch part)'),
    (203, 'SK', 'Slovakia'),
    (204, 'SI', 'Slovenia'),
    (205, 'SB', 'Solomon Islands'),
    (206, 'SO', 'Somalia'),
    (207, 'ZA', 'South Africa'),
    (
        208,
        'GS',
        'South Georgia and the South Sandwich Islands'
    ),
    (209, 'SS', 'South Sudan'),
    (210, 'ES', 'Spain'),
    (211, 'LK', 'Sri Lanka'),
    (212, 'SD', 'Sudan'),
    (213, 'SR', 'Suriname'),
    (214, 'SJ', 'Svalbard and Jan Mayen'),
    (215, 'SE', 'Sweden'),
    (216, 'CH', 'Switzerland'),
    (217, 'SY', 'Syria'),
    (218, 'TW', 'Taiwan'),
    (219, 'TJ', 'Tajikistan'),
    (220, 'TZ', 'Tanzania'),
    (221, 'TH', 'Thailand'),
    (222, 'TL', 'Timor-Leste'),
    (223, 'TG', 'Togo'),
    (224, 'TK', 'Tokelau'),
    (225, 'TO', 'Tonga'),
    (226, 'TT', 'Trinidad and Tobago'),
    (227, 'TN', 'Tunisia'),
    (228, 'TR', 'Turkey'),
    (229, 'TM', 'Turkmenistan'),
    (230, 'TC', 'Turks and Caicos Islands'),
    (231, 'TV', 'Tuvalu'),
    (232, 'UG', 'Uganda'),
    (233, 'UA', 'Ukraine'),
    (234, 'AE', 'United Arab Emirates'),
    (235, 'GB', 'United Kingdom'),
    (236, 'US', 'United States'),
    (
        237,
        'UM',
        'United States Minor Outlying Islands'
    ),
    (238, 'UY', 'Uruguay'),
    (239, 'UZ', 'Uzbekistan'),
    (240, 'VU', 'Vanuatu'),
    (241, 'VE', 'Venezuela'),
    (242, 'VN', 'Vietnam'),
    (243, 'VG', 'Virgin Islands (British)'),
    (244, 'VI', 'Virgin Islands (U.S.)'),
    (245, 'WF', 'Wallis and Futuna'),
    (246, 'EH', 'Western Sahara'),
    (247, 'YE', 'Yemen'),
    (248, 'ZM', 'Zambia'),
    (249, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------
--
-- Structure de la table `role`
--
DROP TABLE IF EXISTS `role`;

CREATE TABLE IF NOT EXISTS `role` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_role` char(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nom_role` (`nom_role`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `type_amd`
--
DROP TABLE IF EXISTS `type_amd`;

CREATE TABLE IF NOT EXISTS `type_amd` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `label` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE = MyISAM AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `type_amd`
--
INSERT INTO
    `type_amd` (`id`, `code`, `label`)
VALUES
    (1, 'TOTA', 'toutes les rubriques'),
    (2, 'AUG_MONTANT', 'Augmentration Montant'),
    (3, 'PROROGATION', 'Prolongation');

-- --------------------------------------------------------
--
-- Structure de la table `type_liberation`
--
DROP TABLE IF EXISTS `type_liberation`;

CREATE TABLE IF NOT EXISTS `type_liberation` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5) NOT NULL,
    `label` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Structure de la table `users`
--
DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom_user` varchar(50) NOT NULL,
    `prenom_user` varchar(50) NOT NULL,
    `username` varchar(100) NOT NULL,
    `password` varchar(100) NOT NULL,
    `status` tinyint(1) NOT NULL,
    `Role` int NOT NULL,
    `structure` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    KEY `fk_role_users` (`Role`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;