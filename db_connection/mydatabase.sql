-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 19 mai 2025 à 11:43
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
) ENGINE = MyISAM AUTO_INCREMENT = 202 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `agence`
--
INSERT INTO
    `agence` (`id`, `code`, `label`, `adresse`, `banque_id`)
VALUES
    (
        21,
        'A003-001',
        'Banque Centrale - Agence 1',
        'Alger',
        3
    ),
    (
        19,
        'A002-009',
        'Banque Populaire - Agence 9',
        'Batna',
        2
    ),
    (
        20,
        'A002-010',
        'Banque Populaire - Agence 10',
        'Ouargla',
        2
    ),
    (
        17,
        'A002-007',
        'Banque Populaire - Agence 7',
        'Tizi Ouzou',
        2
    ),
    (
        18,
        'A002-008',
        'Banque Populaire - Agence 8',
        'Sétif',
        2
    ),
    (
        16,
        'A002-006',
        'Banque Populaire - Agence 6',
        'Béjaïa',
        2
    ),
    (
        15,
        'A002-005',
        'Banque Populaire - Agence 5',
        'Annaba',
        2
    ),
    (
        14,
        'A002-004',
        'Banque Populaire - Agence 4',
        'Blida',
        2
    ),
    (
        13,
        'A002-003',
        'Banque Populaire - Agence 3',
        'Constantine',
        2
    ),
    (
        12,
        'A002-002',
        'Banque Populaire - Agence 2',
        'Oran',
        2
    ),
    (
        11,
        'A002-001',
        'Banque Populaire - Agence 1',
        'Alger',
        2
    ),
    (
        10,
        'A001-010',
        'Banque Nationale - Agence 10',
        'Ouargla',
        1
    ),
    (
        9,
        'A001-009',
        'Banque Nationale - Agence 9',
        'Batna',
        1
    ),
    (
        8,
        'A001-008',
        'Banque Nationale - Agence 8',
        'Sétif',
        1
    ),
    (
        7,
        'A001-007',
        'Banque Nationale - Agence 7',
        'Tizi Ouzou',
        1
    ),
    (
        6,
        'A001-006',
        'Banque Nationale - Agence 6',
        'Béjaïa',
        1
    ),
    (
        5,
        'A001-005',
        'Banque Nationale - Agence 5',
        'Annaba',
        1
    ),
    (
        4,
        'A001-004',
        'Banque Nationale - Agence 4',
        'Blida',
        1
    ),
    (
        3,
        'A001-003',
        'Banque Nationale - Agence 3',
        'Constantine',
        1
    ),
    (
        2,
        'A001-002',
        'Banque Nationale - Agence 2',
        'Oran',
        1
    ),
    (
        1,
        'A001-001',
        'Banque Nationale - Agence 1',
        'Alger',
        1
    ),
    (
        22,
        'A003-002',
        'Banque Centrale - Agence 2',
        'Oran',
        3
    ),
    (
        23,
        'A003-003',
        'Banque Centrale - Agence 3',
        'Constantine',
        3
    ),
    (
        24,
        'A003-004',
        'Banque Centrale - Agence 4',
        'Blida',
        3
    ),
    (
        25,
        'A003-005',
        'Banque Centrale - Agence 5',
        'Annaba',
        3
    ),
    (
        26,
        'A003-006',
        'Banque Centrale - Agence 6',
        'Béjaïa',
        3
    ),
    (
        27,
        'A003-007',
        'Banque Centrale - Agence 7',
        'Tizi Ouzou',
        3
    ),
    (
        28,
        'A003-008',
        'Banque Centrale - Agence 8',
        'Sétif',
        3
    ),
    (
        29,
        'A003-009',
        'Banque Centrale - Agence 9',
        'Batna',
        3
    ),
    (
        30,
        'A003-010',
        'Banque Centrale - Agence 10',
        'Ouargla',
        3
    ),
    (
        31,
        'A004-001',
        'Banque Algérienne - Agence 1',
        'Alger',
        4
    ),
    (
        32,
        'A004-002',
        'Banque Algérienne - Agence 2',
        'Oran',
        4
    ),
    (
        33,
        'A004-003',
        'Banque Algérienne - Agence 3',
        'Constantine',
        4
    ),
    (
        34,
        'A004-004',
        'Banque Algérienne - Agence 4',
        'Blida',
        4
    ),
    (
        35,
        'A004-005',
        'Banque Algérienne - Agence 5',
        'Annaba',
        4
    ),
    (
        36,
        'A004-006',
        'Banque Algérienne - Agence 6',
        'Béjaïa',
        4
    ),
    (
        37,
        'A004-007',
        'Banque Algérienne - Agence 7',
        'Tizi Ouzou',
        4
    ),
    (
        38,
        'A004-008',
        'Banque Algérienne - Agence 8',
        'Sétif',
        4
    ),
    (
        39,
        'A004-009',
        'Banque Algérienne - Agence 9',
        'Batna',
        4
    ),
    (
        40,
        'A004-010',
        'Banque Algérienne - Agence 10',
        'Ouargla',
        4
    ),
    (
        41,
        'A005-001',
        'Banque du Sahara - Agence 1',
        'Alger',
        5
    ),
    (
        42,
        'A005-002',
        'Banque du Sahara - Agence 2',
        'Oran',
        5
    ),
    (
        43,
        'A005-003',
        'Banque du Sahara - Agence 3',
        'Constantine',
        5
    ),
    (
        44,
        'A005-004',
        'Banque du Sahara - Agence 4',
        'Blida',
        5
    ),
    (
        45,
        'A005-005',
        'Banque du Sahara - Agence 5',
        'Annaba',
        5
    ),
    (
        46,
        'A005-006',
        'Banque du Sahara - Agence 6',
        'Béjaïa',
        5
    ),
    (
        47,
        'A005-007',
        'Banque du Sahara - Agence 7',
        'Tizi Ouzou',
        5
    ),
    (
        48,
        'A005-008',
        'Banque du Sahara - Agence 8',
        'Sétif',
        5
    ),
    (
        49,
        'A005-009',
        'Banque du Sahara - Agence 9',
        'Batna',
        5
    ),
    (
        50,
        'A005-010',
        'Banque du Sahara - Agence 10',
        'Ouargla',
        5
    ),
    (
        51,
        'A006-001',
        'Banque du Nord - Agence 1',
        'Alger',
        6
    ),
    (
        52,
        'A006-002',
        'Banque du Nord - Agence 2',
        'Oran',
        6
    ),
    (
        53,
        'A006-003',
        'Banque du Nord - Agence 3',
        'Constantine',
        6
    ),
    (
        54,
        'A006-004',
        'Banque du Nord - Agence 4',
        'Blida',
        6
    ),
    (
        55,
        'A006-005',
        'Banque du Nord - Agence 5',
        'Annaba',
        6
    ),
    (
        56,
        'A006-006',
        'Banque du Nord - Agence 6',
        'Béjaïa',
        6
    ),
    (
        57,
        'A006-007',
        'Banque du Nord - Agence 7',
        'Tizi Ouzou',
        6
    ),
    (
        58,
        'A006-008',
        'Banque du Nord - Agence 8',
        'Sétif',
        6
    ),
    (
        59,
        'A006-009',
        'Banque du Nord - Agence 9',
        'Batna',
        6
    ),
    (
        60,
        'A006-010',
        'Banque du Nord - Agence 10',
        'Ouargla',
        6
    ),
    (
        61,
        'A007-001',
        'Banque du Sud - Agence 1',
        'Alger',
        7
    ),
    (
        62,
        'A007-002',
        'Banque du Sud - Agence 2',
        'Oran',
        7
    ),
    (
        63,
        'A007-003',
        'Banque du Sud - Agence 3',
        'Constantine',
        7
    ),
    (
        64,
        'A007-004',
        'Banque du Sud - Agence 4',
        'Blida',
        7
    ),
    (
        65,
        'A007-005',
        'Banque du Sud - Agence 5',
        'Annaba',
        7
    ),
    (
        66,
        'A007-006',
        'Banque du Sud - Agence 6',
        'Béjaïa',
        7
    ),
    (
        67,
        'A007-007',
        'Banque du Sud - Agence 7',
        'Tizi Ouzou',
        7
    ),
    (
        68,
        'A007-008',
        'Banque du Sud - Agence 8',
        'Sétif',
        7
    ),
    (
        69,
        'A007-009',
        'Banque du Sud - Agence 9',
        'Batna',
        7
    ),
    (
        70,
        'A007-010',
        'Banque du Sud - Agence 10',
        'Ouargla',
        7
    ),
    (
        71,
        'A008-001',
        'Banque de l’Est - Agence 1',
        'Alger',
        8
    ),
    (
        72,
        'A008-002',
        'Banque de l’Est - Agence 2',
        'Oran',
        8
    ),
    (
        73,
        'A008-003',
        'Banque de l’Est - Agence 3',
        'Constantine',
        8
    ),
    (
        74,
        'A008-004',
        'Banque de l’Est - Agence 4',
        'Blida',
        8
    ),
    (
        75,
        'A008-005',
        'Banque de l’Est - Agence 5',
        'Annaba',
        8
    ),
    (
        76,
        'A008-006',
        'Banque de l’Est - Agence 6',
        'Béjaïa',
        8
    ),
    (
        77,
        'A008-007',
        'Banque de l’Est - Agence 7',
        'Tizi Ouzou',
        8
    ),
    (
        78,
        'A008-008',
        'Banque de l’Est - Agence 8',
        'Sétif',
        8
    ),
    (
        79,
        'A008-009',
        'Banque de l’Est - Agence 9',
        'Batna',
        8
    ),
    (
        80,
        'A008-010',
        'Banque de l’Est - Agence 10',
        'Ouargla',
        8
    ),
    (
        81,
        'A009-001',
        'Banque de l’Ouest - Agence 1',
        'Alger',
        9
    ),
    (
        82,
        'A009-002',
        'Banque de l’Ouest - Agence 2',
        'Oran',
        9
    ),
    (
        83,
        'A009-003',
        'Banque de l’Ouest - Agence 3',
        'Constantine',
        9
    ),
    (
        84,
        'A009-004',
        'Banque de l’Ouest - Agence 4',
        'Blida',
        9
    ),
    (
        85,
        'A009-005',
        'Banque de l’Ouest - Agence 5',
        'Annaba',
        9
    ),
    (
        86,
        'A009-006',
        'Banque de l’Ouest - Agence 6',
        'Béjaïa',
        9
    ),
    (
        87,
        'A009-007',
        'Banque de l’Ouest - Agence 7',
        'Tizi Ouzou',
        9
    ),
    (
        88,
        'A009-008',
        'Banque de l’Ouest - Agence 8',
        'Sétif',
        9
    ),
    (
        89,
        'A009-009',
        'Banque de l’Ouest - Agence 9',
        'Batna',
        9
    ),
    (
        90,
        'A009-010',
        'Banque de l’Ouest - Agence 10',
        'Ouargla',
        9
    ),
    (
        91,
        'A010-001',
        'Banque Commerciale - Agence 1',
        'Alger',
        10
    ),
    (
        92,
        'A010-002',
        'Banque Commerciale - Agence 2',
        'Oran',
        10
    ),
    (
        93,
        'A010-003',
        'Banque Commerciale - Agence 3',
        'Constantine',
        10
    ),
    (
        94,
        'A010-004',
        'Banque Commerciale - Agence 4',
        'Blida',
        10
    ),
    (
        95,
        'A010-005',
        'Banque Commerciale - Agence 5',
        'Annaba',
        10
    ),
    (
        96,
        'A010-006',
        'Banque Commerciale - Agence 6',
        'Béjaïa',
        10
    ),
    (
        97,
        'A010-007',
        'Banque Commerciale - Agence 7',
        'Tizi Ouzou',
        10
    ),
    (
        98,
        'A010-008',
        'Banque Commerciale - Agence 8',
        'Sétif',
        10
    ),
    (
        99,
        'A010-009',
        'Banque Commerciale - Agence 9',
        'Batna',
        10
    ),
    (
        100,
        'A010-010',
        'Banque Commerciale - Agence 10',
        'Ouargla',
        10
    ),
    (
        101,
        'A011-001',
        'Banque Industrielle - Agence 1',
        'Alger',
        11
    ),
    (
        102,
        'A011-002',
        'Banque Industrielle - Agence 2',
        'Oran',
        11
    ),
    (
        103,
        'A011-003',
        'Banque Industrielle - Agence 3',
        'Constantine',
        11
    ),
    (
        104,
        'A011-004',
        'Banque Industrielle - Agence 4',
        'Blida',
        11
    ),
    (
        105,
        'A011-005',
        'Banque Industrielle - Agence 5',
        'Annaba',
        11
    ),
    (
        106,
        'A011-006',
        'Banque Industrielle - Agence 6',
        'Béjaïa',
        11
    ),
    (
        107,
        'A011-007',
        'Banque Industrielle - Agence 7',
        'Tizi Ouzou',
        11
    ),
    (
        108,
        'A011-008',
        'Banque Industrielle - Agence 8',
        'Sétif',
        11
    ),
    (
        109,
        'A011-009',
        'Banque Industrielle - Agence 9',
        'Batna',
        11
    ),
    (
        110,
        'A011-010',
        'Banque Industrielle - Agence 10',
        'Ouargla',
        11
    ),
    (
        111,
        'A012-001',
        'Banque Agricole - Agence 1',
        'Alger',
        12
    ),
    (
        112,
        'A012-002',
        'Banque Agricole - Agence 2',
        'Oran',
        12
    ),
    (
        113,
        'A012-003',
        'Banque Agricole - Agence 3',
        'Constantine',
        12
    ),
    (
        114,
        'A012-004',
        'Banque Agricole - Agence 4',
        'Blida',
        12
    ),
    (
        115,
        'A012-005',
        'Banque Agricole - Agence 5',
        'Annaba',
        12
    ),
    (
        116,
        'A012-006',
        'Banque Agricole - Agence 6',
        'Béjaïa',
        12
    ),
    (
        117,
        'A012-007',
        'Banque Agricole - Agence 7',
        'Tizi Ouzou',
        12
    ),
    (
        118,
        'A012-008',
        'Banque Agricole - Agence 8',
        'Sétif',
        12
    ),
    (
        119,
        'A012-009',
        'Banque Agricole - Agence 9',
        'Batna',
        12
    ),
    (
        120,
        'A012-010',
        'Banque Agricole - Agence 10',
        'Ouargla',
        12
    ),
    (
        121,
        'A013-001',
        'Banque Maritime - Agence 1',
        'Alger',
        13
    ),
    (
        122,
        'A013-002',
        'Banque Maritime - Agence 2',
        'Oran',
        13
    ),
    (
        123,
        'A013-003',
        'Banque Maritime - Agence 3',
        'Constantine',
        13
    ),
    (
        124,
        'A013-004',
        'Banque Maritime - Agence 4',
        'Blida',
        13
    ),
    (
        125,
        'A013-005',
        'Banque Maritime - Agence 5',
        'Annaba',
        13
    ),
    (
        126,
        'A013-006',
        'Banque Maritime - Agence 6',
        'Béjaïa',
        13
    ),
    (
        127,
        'A013-007',
        'Banque Maritime - Agence 7',
        'Tizi Ouzou',
        13
    ),
    (
        128,
        'A013-008',
        'Banque Maritime - Agence 8',
        'Sétif',
        13
    ),
    (
        129,
        'A013-009',
        'Banque Maritime - Agence 9',
        'Batna',
        13
    ),
    (
        130,
        'A013-010',
        'Banque Maritime - Agence 10',
        'Ouargla',
        13
    ),
    (
        131,
        'A014-001',
        'Banque Minérale - Agence 1',
        'Alger',
        14
    ),
    (
        132,
        'A014-002',
        'Banque Minérale - Agence 2',
        'Oran',
        14
    ),
    (
        133,
        'A014-003',
        'Banque Minérale - Agence 3',
        'Constantine',
        14
    ),
    (
        134,
        'A014-004',
        'Banque Minérale - Agence 4',
        'Blida',
        14
    ),
    (
        135,
        'A014-005',
        'Banque Minérale - Agence 5',
        'Annaba',
        14
    ),
    (
        136,
        'A014-006',
        'Banque Minérale - Agence 6',
        'Béjaïa',
        14
    ),
    (
        137,
        'A014-007',
        'Banque Minérale - Agence 7',
        'Tizi Ouzou',
        14
    ),
    (
        138,
        'A014-008',
        'Banque Minérale - Agence 8',
        'Sétif',
        14
    ),
    (
        139,
        'A014-009',
        'Banque Minérale - Agence 9',
        'Batna',
        14
    ),
    (
        140,
        'A014-010',
        'Banque Minérale - Agence 10',
        'Ouargla',
        14
    ),
    (
        141,
        'A015-001',
        'Banque Technologique - Agence 1',
        'Alger',
        15
    ),
    (
        142,
        'A015-002',
        'Banque Technologique - Agence 2',
        'Oran',
        15
    ),
    (
        143,
        'A015-003',
        'Banque Technologique - Agence 3',
        'Constantine',
        15
    ),
    (
        144,
        'A015-004',
        'Banque Technologique - Agence 4',
        'Blida',
        15
    ),
    (
        145,
        'A015-005',
        'Banque Technologique - Agence 5',
        'Annaba',
        15
    ),
    (
        146,
        'A015-006',
        'Banque Technologique - Agence 6',
        'Béjaïa',
        15
    ),
    (
        147,
        'A015-007',
        'Banque Technologique - Agence 7',
        'Tizi Ouzou',
        15
    ),
    (
        148,
        'A015-008',
        'Banque Technologique - Agence 8',
        'Sétif',
        15
    ),
    (
        149,
        'A015-009',
        'Banque Technologique - Agence 9',
        'Batna',
        15
    ),
    (
        150,
        'A015-010',
        'Banque Technologique - Agence 10',
        'Ouargla',
        15
    ),
    (
        151,
        'A016-001',
        'Banque Islamique - Agence 1',
        'Alger',
        16
    ),
    (
        152,
        'A016-002',
        'Banque Islamique - Agence 2',
        'Oran',
        16
    ),
    (
        153,
        'A016-003',
        'Banque Islamique - Agence 3',
        'Constantine',
        16
    ),
    (
        154,
        'A016-004',
        'Banque Islamique - Agence 4',
        'Blida',
        16
    ),
    (
        155,
        'A016-005',
        'Banque Islamique - Agence 5',
        'Annaba',
        16
    ),
    (
        156,
        'A016-006',
        'Banque Islamique - Agence 6',
        'Béjaïa',
        16
    ),
    (
        157,
        'A016-007',
        'Banque Islamique - Agence 7',
        'Tizi Ouzou',
        16
    ),
    (
        158,
        'A016-008',
        'Banque Islamique - Agence 8',
        'Sétif',
        16
    ),
    (
        159,
        'A016-009',
        'Banque Islamique - Agence 9',
        'Batna',
        16
    ),
    (
        160,
        'A016-010',
        'Banque Islamique - Agence 10',
        'Ouargla',
        16
    ),
    (
        161,
        'A017-001',
        'Banque Internationale - Agence 1',
        'Alger',
        17
    ),
    (
        162,
        'A017-002',
        'Banque Internationale - Agence 2',
        'Oran',
        17
    ),
    (
        163,
        'A017-003',
        'Banque Internationale - Agence 3',
        'Constantine',
        17
    ),
    (
        164,
        'A017-004',
        'Banque Internationale - Agence 4',
        'Blida',
        17
    ),
    (
        165,
        'A017-005',
        'Banque Internationale - Agence 5',
        'Annaba',
        17
    ),
    (
        166,
        'A017-006',
        'Banque Internationale - Agence 6',
        'Béjaïa',
        17
    ),
    (
        167,
        'A017-007',
        'Banque Internationale - Agence 7',
        'Tizi Ouzou',
        17
    ),
    (
        168,
        'A017-008',
        'Banque Internationale - Agence 8',
        'Sétif',
        17
    ),
    (
        169,
        'A017-009',
        'Banque Internationale - Agence 9',
        'Batna',
        17
    ),
    (
        170,
        'A017-010',
        'Banque Internationale - Agence 10',
        'Ouargla',
        17
    ),
    (
        171,
        'A018-001',
        'Banque Urbaine - Agence 1',
        'Alger',
        18
    ),
    (
        172,
        'A018-002',
        'Banque Urbaine - Agence 2',
        'Oran',
        18
    ),
    (
        173,
        'A018-003',
        'Banque Urbaine - Agence 3',
        'Constantine',
        18
    ),
    (
        174,
        'A018-004',
        'Banque Urbaine - Agence 4',
        'Blida',
        18
    ),
    (
        175,
        'A018-005',
        'Banque Urbaine - Agence 5',
        'Annaba',
        18
    ),
    (
        176,
        'A018-006',
        'Banque Urbaine - Agence 6',
        'Béjaïa',
        18
    ),
    (
        177,
        'A018-007',
        'Banque Urbaine - Agence 7',
        'Tizi Ouzou',
        18
    ),
    (
        178,
        'A018-008',
        'Banque Urbaine - Agence 8',
        'Sétif',
        18
    ),
    (
        179,
        'A018-009',
        'Banque Urbaine - Agence 9',
        'Batna',
        18
    ),
    (
        180,
        'A018-010',
        'Banque Urbaine - Agence 10',
        'Ouargla',
        18
    ),
    (
        181,
        'A019-001',
        'Banque Rurale - Agence 1',
        'Alger',
        19
    ),
    (
        182,
        'A019-002',
        'Banque Rurale - Agence 2',
        'Oran',
        19
    ),
    (
        183,
        'A019-003',
        'Banque Rurale - Agence 3',
        'Constantine',
        19
    ),
    (
        184,
        'A019-004',
        'Banque Rurale - Agence 4',
        'Blida',
        19
    ),
    (
        185,
        'A019-005',
        'Banque Rurale - Agence 5',
        'Annaba',
        19
    ),
    (
        186,
        'A019-006',
        'Banque Rurale - Agence 6',
        'Béjaïa',
        19
    ),
    (
        187,
        'A019-007',
        'Banque Rurale - Agence 7',
        'Tizi Ouzou',
        19
    ),
    (
        188,
        'A019-008',
        'Banque Rurale - Agence 8',
        'Sétif',
        19
    ),
    (
        189,
        'A019-009',
        'Banque Rurale - Agence 9',
        'Batna',
        19
    ),
    (
        190,
        'A019-010',
        'Banque Rurale - Agence 10',
        'Ouargla',
        19
    ),
    (
        191,
        'A020-001',
        'Banque Digitale - Agence 1',
        'Alger',
        20
    ),
    (
        192,
        'A020-002',
        'Banque Digitale - Agence 2',
        'Oran',
        20
    ),
    (
        193,
        'A020-003',
        'Banque Digitale - Agence 3',
        'Constantine',
        20
    ),
    (
        194,
        'A020-004',
        'Banque Digitale - Agence 4',
        'Blida',
        20
    ),
    (
        195,
        'A020-005',
        'Banque Digitale - Agence 5',
        'Annaba',
        20
    ),
    (
        196,
        'A020-006',
        'Banque Digitale - Agence 6',
        'Béjaïa',
        20
    ),
    (
        197,
        'A020-007',
        'Banque Digitale - Agence 7',
        'Tizi Ouzou',
        20
    ),
    (
        198,
        'A020-008',
        'Banque Digitale - Agence 8',
        'Sétif',
        20
    ),
    (
        199,
        'A020-009',
        'Banque Digitale - Agence 9',
        'Batna',
        20
    ),
    (
        200,
        'A020-010',
        'Banque Digitale - Agence 10',
        'Ouargla',
        20
    ),
    (
        201,
        'A018-021',
        'Banque Urbaine - Agence 12',
        'ss',
        18
    );

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
    `Type_amd_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `num_amd` (`num_amd`),
    KEY `fk_amd_type` (`Type_amd_id`),
    KEY `fk_gar_amd` (`garantie_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 14 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

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
        `Type_amd_id`
    )
VALUES
    (
        1,
        '215',
        '2025-04-17',
        '2025-04-18',
        2515615.00000,
        8,
        1
    ),
    (
        2,
        '2157',
        '2025-04-17',
        '2025-04-19',
        1234567899.00000,
        9,
        1
    ),
    (
        3,
        '123456',
        '2025-04-16',
        '2025-04-19',
        4567899.00000,
        10,
        1
    ),
    (
        4,
        '12346',
        '2025-04-17',
        '2025-05-03',
        12345687.00000,
        10,
        1
    ),
    (
        5,
        'KJKDSFKDS',
        '2025-04-09',
        '0000-00-00',
        12122.00000,
        9,
        2
    ),
    (
        6,
        'KJKDSFKDSBGGF',
        '2025-04-09',
        '0000-00-00',
        12122.00000,
        9,
        2
    ),
    (
        7,
        '4444',
        '2025-04-10',
        '0000-00-00',
        5345.00000,
        12,
        2
    ),
    (
        8,
        '44447',
        '2025-04-10',
        '0000-00-00',
        5345.00000,
        12,
        2
    ),
    (
        9,
        'aymen9999',
        '2025-05-10',
        '2025-05-23',
        0.00000,
        12,
        3
    ),
    (
        10,
        '12346777777',
        '2025-05-15',
        '0000-00-00',
        840999499.00000,
        61,
        2
    ),
    (
        11,
        '12354',
        '2025-05-07',
        '0000-00-00',
        111.00000,
        61,
        2
    ),
    (
        12,
        '12345633',
        '2025-05-16',
        '0000-00-00',
        234567890.00000,
        61,
        2
    ),
    (
        13,
        '2153333',
        '2025-05-09',
        '2025-07-03',
        123.00000,
        61,
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
) ENGINE = MyISAM AUTO_INCREMENT = 17 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `appel_offre`
--
INSERT INTO
    `appel_offre` (`id`, `date_appel_offre`, `num_appel_offre`)
VALUES
    (1, '2025-03-10', 'AO-2025-001'),
    (2, '2025-03-12', 'AO-2025-002'),
    (3, '2025-03-15', 'AO-2025-003'),
    (8, '2025-03-14', 'AO-2025-00407'),
    (7, '2025-08-08', 'AO-2025-00699'),
    (9, '2025-05-02', 'vvvv'),
    (10, '2025-04-09', '1345'),
    (11, '2025-05-29', '12355'),
    (12, '2025-05-16', '7777777777'),
    (13, '2025-05-23', '777777777'),
    (14, '2025-05-30', '4447'),
    (15, '2025-05-17', '7777777'),
    (16, '2025-05-23', '13457');

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
) ENGINE = MyISAM AUTO_INCREMENT = 78 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `authentification`
--
INSERT INTO
    `authentification` (
        `id`,
        `num_auth`,
        `date_depo`,
        `date_auth`,
        `garantie_id`
    )
VALUES
    (68, '1234', '2025-04-17', '2025-04-17', 8),
    (67, '12322', '2025-04-17', '2025-04-17', 0),
    (66, '123', '2025-04-16', '2025-04-17', 6),
    (69, '255', '2025-04-17', '2025-04-17', 9),
    (70, '1234567899', '2025-04-17', '2025-04-17', 10),
    (71, '12347', '2025-04-17', '2025-04-17', 11),
    (72, '12346', '2025-04-18', '2025-04-18', 18),
    (73, '1345', '2025-04-18', '2025-04-18', 12),
    (74, '45623', '2025-04-20', '2025-04-20', 19),
    (75, '123777', '2025-05-16', '2025-05-16', 25),
    (76, '123477777', '2025-05-16', '2025-05-16', 26),
    (77, '23456789', '2025-05-18', '2025-05-18', 61);

-- --------------------------------------------------------
--
-- Structure de la table `banque`
--
DROP TABLE IF EXISTS `banque`;

CREATE TABLE IF NOT EXISTS `banque` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL,
    `label` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 85 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `banque`
--
INSERT INTO
    `banque` (`id`, `code`, `label`)
VALUES
    (18, 'BANK-018', 'Fransabank'),
    (17, 'BANK-017', 'HSBC Algérie'),
    (16, 'BANK-016', 'Citibank'),
    (15, 'BANK-015', 'Banque El Khalifa'),
    (14, 'BANK-014', 'Banque de Développement'),
    (13, 'BANK-013', 'Banque Saharienne'),
    (12, 'BANK-012', 'CNEP Banque'),
    (11, 'BANK-011', 'Banque ABC'),
    (10, 'BANK-010', 'Trust Bank'),
    (9, 'BANK-009', 'Banque El Amana'),
    (8, 'BANK-008', 'Banque Al Baraka'),
    (7, 'BANK-007', 'Banque Extérieure d’Algérie'),
    (6, 'BANK-006', 'Banque d’Algérie'),
    (5, 'BANK-005', 'BNP Paribas'),
    (4, 'BANK-004', 'Crédit Agricole'),
    (3, 'BANK-003', 'Société Générale'),
    (2, 'BANK-002', 'Banque Populaire'),
    (1, 'BANK-001', 'Banque Nationale'),
    (19, 'BANK-019', 'Natixis'),
    (20, 'BANK-020', 'Banque du Maghreb'),
    (84, 'BANK-021', 'Djouhara');

-- --------------------------------------------------------
--
-- Structure de la table `direction`
--
DROP TABLE IF EXISTS `direction`;

CREATE TABLE IF NOT EXISTS `direction` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL,
    `libelle` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `libelle` (`libelle`)
) ENGINE = MyISAM AUTO_INCREMENT = 22 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `direction`
--
INSERT INTO
    `direction` (`id`, `code`, `libelle`)
VALUES
    (1, 'DIR-01', 'SOCIETE GENERALE'),
    (2, 'DIR-02', 'DIRECTION COMMERCIALE'),
    (3, 'DIR-03', 'DIRECTION FINANCIERE'),
    (4, 'DIR-04', 'DIRECTION RESSOURCES HUMAINES'),
    (5, 'DIR-05', 'DIRECTION JURIDIQUE'),
    (6, 'DIR-06', 'DIRECTION INFORMATIQUE'),
    (7, 'DIR-07', 'DIRECTION MARKETING'),
    (8, 'DIR-08', 'DIRECTION LOGISTIQUE'),
    (9, 'DIR-09', 'DIRECTION DE LA COMMUNICATION'),
    (10, 'DIR-10', 'DIRECTION DE L’INNOVATION'),
    (11, 'DIR-11', 'DIRECTION TECHNIQUE'),
    (12, 'DIR-12', 'DIRECTION QUALITE'),
    (13, 'DIR-13', 'DIRECTION AUDIT INTERNE'),
    (14, 'DIR-14', 'DIRECTION DES OPERATIONS'),
    (15, 'DIR-15', 'DIRECTION STRATEGIQUE'),
    (16, 'DIR-16', 'DIRECTION DE LA FORMATION'),
    (17, 'DIR-17', 'DIRECTION ACHATS'),
    (18, 'DIR-18', 'DIRECTION ENVIRONNEMENTALE'),
    (19, 'DIR-19', 'DIRECTION DE LA SECURITE'),
    (20, 'DIR-20', 'DIRECTION DES RELATIONS PUBLIQUE'),
    (21, 'DIR-011', 'SOCIETE GENERALE17');

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
) ENGINE = MyISAM AUTO_INCREMENT = 11 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

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
        'TP4-ADM_CS (1).pdf',
        'uploads/TP4-ADM_CS (1).pdf',
        8,
        1
    ),
    (
        2,
        'TP5-ADM_CS.pdf',
        'uploads/TP5-ADM_CS.pdf',
        9,
        2
    ),
    (
        3,
        'LISTE DES PRIX poly[1].pdf',
        'uploads/LISTE DES PRIX poly[1].pdf',
        10,
        3
    ),
    (
        4,
        'LISTE DES PRIX poly.pdf',
        'uploads/LISTE DES PRIX poly.pdf',
        10,
        4
    ),
    (
        5,
        'CV-DEGHBAR-DJOUHARA - Copy.pdf',
        'uploads/CV-DEGHBAR-DJOUHARA - Copy.pdf',
        9,
        6
    ),
    (
        6,
        'cours-7-documents-structures.pdf',
        'uploads/cours-7-documents-structures.pdf',
        12,
        9
    ),
    (
        7,
        'notes L3 Acad 2024 2025 étudiants_250205_203721 (1',
        'uploads/notes L3 Acad 2024 2025 étudiants_250205_203721 (1).pdf',
        61,
        10
    ),
    (
        8,
        'Notes_L3_ACADA_2425.pdf',
        '../../Pages/Amandements/uploads/Notes_L3_ACADA_2425.pdf',
        61,
        11
    ),
    (
        9,
        'Rapport_PFE_G43_VF.pdf',
        '../Backend/Amandements/requetes_ajax/uploads/Rapport_PFE_G43_VF.pdf',
        61,
        12
    ),
    (
        10,
        'EDT L2L3 ACAD MAJ.pdf',
        'uploads/EDT L2L3 ACAD MAJ.pdf',
        61,
        13
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
) ENGINE = MyISAM AUTO_INCREMENT = 37 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `document_auth`
--
INSERT INTO
    `document_auth` (
        `id`,
        `nom_document`,
        `document_path`,
        `authentification_id`
    )
VALUES
    (
        17,
        'ca6e629a-45e0-4713-811a-b128832cd831_1st_Meeting_R',
        '../../uploads/documents/ca6e629a-45e0-4713-811a-b128832cd831_1st_Meeting_Report.pdf',
        57
    ),
    (
        16,
        '5b0f591c-6786-4349-a98b-4641b78b33c1.pdf',
        '../../uploads/documents/5b0f591c-6786-4349-a98b-4641b78b33c1.pdf',
        56
    ),
    (
        15,
        'Second_meeting_report_01-04-2025.pdf',
        '../../uploads/documents/Second_meeting_report_01-04-2025.pdf',
        55
    ),
    (
        14,
        'cours-4-documents-structures.pdf',
        '../../uploads/documents/cours-4-documents-structures.pdf',
        54
    ),
    (
        12,
        'TP1-ADM (3).pdf',
        '../../uploads/documents/TP1-ADM (3).pdf',
        52
    ),
    (
        13,
        'TP2-ADM_CS.pdf',
        '../../uploads/documents/TP2-ADM_CS.pdf',
        53
    ),
    (
        11,
        'Devoir_Maison_DEGHBAR_Djouhra.pdf',
        '../../uploads/documents/Devoir_Maison_DEGHBAR_Djouhra.pdf',
        49
    ),
    (
        18,
        'IMG_7996-removebg-preview.png',
        '../../uploads/documents/IMG_7996-removebg-preview.png',
        58
    ),
    (
        19,
        'cours-5-documents-structures.pdf',
        '../../uploads/documents/cours-5-documents-structures.pdf',
        59
    ),
    (
        20,
        'cours-3-documents-structures.pdf',
        '../../uploads/documents/cours-3-documents-structures.pdf',
        60
    ),
    (
        21,
        'cours-2-documents-structures.pdf',
        '../../uploads/documents/cours-2-documents-structures.pdf',
        61
    ),
    (
        22,
        'cours-1-documents-structures.pdf',
        '../../uploads/documents/cours-1-documents-structures.pdf',
        62
    ),
    (
        23,
        'Project Structure & Responsibilities - Copy.pdf',
        '../../uploads/documents/Project Structure & Responsibilities - Copy.pdf',
        63
    ),
    (
        24,
        'TP1-ADM (4).pdf',
        '../../uploads/documents/TP1-ADM (4).pdf',
        64
    ),
    (
        25,
        'TP4-ADM_CS.pdf',
        '../../uploads/documents/TP4-ADM_CS.pdf',
        65
    ),
    (
        26,
        'LISTE DES PRIX poly.pdf',
        '../../uploads/documents/LISTE DES PRIX poly.pdf',
        66
    ),
    (
        27,
        'Document sans titre-7.pdf',
        '../../uploads/documents/Document sans titre-7.pdf',
        67
    ),
    (
        28,
        'LISTE DES PRIX poly[1].pdf',
        '../../uploads/documents/LISTE DES PRIX poly[1].pdf',
        68
    ),
    (
        29,
        'TP3-ADM_CS (3).pdf',
        '../../uploads/documents/TP3-ADM_CS (3).pdf',
        69
    ),
    (
        30,
        'Chapitre 3 - Diagramme cas d\'utilisation - Slides.',
        '../../uploads/documents/Chapitre 3 - Diagramme cas d\'utilisation - Slides.pdf',
        70
    ),
    (
        31,
        'TP4-ADM_CS (1).pdf',
        '../../uploads/documents/TP4-ADM_CS (1).pdf',
        71
    ),
    (
        32,
        'PFE_ISIL_E_007 SAYOUD Lynda BELARBI Maria.pdf',
        '../../uploads/documents/PFE_ISIL_E_007 SAYOUD Lynda BELARBI Maria.pdf',
        72
    ),
    (
        33,
        'Justificatif-ROP (2).pdf',
        '../../uploads/documents/Justificatif-ROP (2).pdf',
        73
    ),
    (
        34,
        '23-24-EMD-INSI.pdf',
        '../../uploads/documents/23-24-EMD-INSI.pdf',
        75
    ),
    (
        35,
        'Rapport_PFE_G43 (1).pdf',
        '../../uploads/documents/Rapport_PFE_G43 (1).pdf',
        76
    ),
    (
        36,
        'Chill Minds Presentation.pdf',
        '../../uploads/documents/Chill Minds Presentation.pdf',
        77
    );

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
) ENGINE = MyISAM AUTO_INCREMENT = 23 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `document_garantie`
--
INSERT INTO
    `document_garantie` (
        `id`,
        `nom_document`,
        `document_path`,
        `garantie_id`
    )
VALUES
    (
        1,
        'TP5-ADM_CS.pdf',
        '../uploads/garanties/GB-2025-7474_20250417_102121.pdf',
        8
    ),
    (
        2,
        'LISTE DES PRIX poly[1].pdf',
        '../uploads/garanties/GB-2025-4256777777_20250417_102608.pdf',
        9
    ),
    (
        3,
        'LISTE DES PRIX poly.pdf',
        '../uploads/garanties/GB-2025-916577_20250417_131002.pdf',
        10
    ),
    (
        4,
        'cours-4-documents-structures.pdf',
        '../uploads/garanties/GB-2025-6985_20250417_130935.pdf',
        11
    ),
    (
        5,
        'TP4-ADM_CS.pdf',
        '../uploads/garanties/GB-2025-8321_20250418_150125.pdf',
        12
    ),
    (
        6,
        'cours-6-documents-structures.pdf',
        '../uploads/garanties/GB-2025-4541_20250418_150215.pdf',
        13
    ),
    (
        7,
        'Corrigé (1).pdf',
        '../uploads/garanties/GB-2025-9838_20250418_150248.pdf',
        14
    ),
    (
        8,
        'TP3-ADM_CS (3).pdf',
        '../uploads/garanties/GB-2025-7150_20250418_150324.pdf',
        15
    ),
    (
        9,
        'Devoir_maison_DEGHBAR_Djouhra .pdf',
        '../uploads/garanties/GB-2025-5427_20250418_150410.pdf',
        16
    ),
    (
        10,
        'Chapitre 4 - Diagramme de Classes - Slides (1).pdf',
        '../uploads/garanties/GB-2025-5212_20250418_150457.pdf',
        17
    ),
    (
        11,
        'TP1-ADM.pdf',
        '../uploads/garanties/GB-2025-3779_20250418_150536.pdf',
        18
    ),
    (
        12,
        'USTHB_FI_ING2Info_ThGRA_Chapitre 4_Support.pdf',
        '../uploads/garanties/GB-2025-9298_20250418_150610.pdf',
        19
    ),
    (
        13,
        'TP1-ADM (1).pdf',
        '../uploads/garanties/GB-2025-7608_20250418_150651.pdf',
        20
    ),
    (
        14,
        'Examens-S1-2425-L3.pdf',
        '../uploads/garanties/GB-2025-5648_20250418_155619.pdf',
        21
    ),
    (
        15,
        'Diapos.pdf',
        '../uploads/garanties/GB-2_20250418_155832.pdf',
        22
    ),
    (
        16,
        'TP6-ADM_CS (1).pdf',
        '../uploads/garanties/GB-2025-3553_20250428_123106.pdf',
        23
    ),
    (
        17,
        'TP5-ADM_CS (3).pdf',
        '../uploads/garanties/GB-2025-4999-HIBAAA_20250511_133641.pdf',
        24
    ),
    (
        18,
        'cours-7-documents-structures.pdf',
        '../uploads/garanties/GB-2025-4715-Djouhara_20250514_220614.pdf',
        25
    ),
    (
        19,
        'Rapport_PFE_G43 (2).pdf',
        '../uploads/garanties/GB-2025-9229_20250516_213259.pdf',
        26
    ),
    (
        20,
        '23-24-EMD-INSI.pdf',
        '../uploads/garanties/GB-2025-2484_20250517_114436.pdf',
        27
    ),
    (
        21,
        'Untitled.pdf',
        '../uploads/garanties/GB-2025-6029-hmara_20250517_131853.pdf',
        28
    ),
    (
        22,
        'Règlement du PFE Licence (1).pdf',
        '../uploads/garanties/GB-2025-8828_20250517_132100.pdf',
        29
    );

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
) ENGINE = MyISAM AUTO_INCREMENT = 9 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `document_liberation`
--
INSERT INTO
    `document_liberation` (
        `id`,
        `nom_document`,
        `document_path`,
        `liberation_id`
    )
VALUES
    (
        1,
        'TP4-ADM_CS (1).pdf',
        '../../uploads/documents/TP4-ADM_CS (1).pdf',
        1
    ),
    (
        2,
        'Document sans titre-7.pdf',
        '../../uploads/documents/Document sans titre-7.pdf',
        2
    ),
    (
        3,
        'Chill Minds Presentation.pdf',
        '../../uploads/documents/Chill Minds Presentation.pdf',
        3
    ),
    (
        4,
        'TP3-ADM_CS (3).pdf',
        '../../uploads/documents/TP3-ADM_CS (3).pdf',
        4
    ),
    (
        5,
        'scanner (2).png',
        '../../uploads/documents/scanner (2).png',
        5
    ),
    (
        6,
        'LISTE DES PRIX poly.pdf',
        '../../uploads/documents/LISTE DES PRIX poly.pdf',
        6
    ),
    (
        7,
        '23-24-EMD-INSI.pdf',
        '../../uploads/documents/23-24-EMD-INSI.pdf',
        7
    ),
    (
        8,
        'notes_A.pdf',
        '../../uploads/documents/notes_A.pdf',
        8
    );

-- --------------------------------------------------------
--
-- Structure de la table `first_login`
--
DROP TABLE IF EXISTS `first_login`;

CREATE TABLE IF NOT EXISTS `first_login` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id` (`user_id`)
) ENGINE = MyISAM AUTO_INCREMENT = 19 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `first_login`
--
INSERT INTO
    `first_login` (`id`, `user_id`)
VALUES
    (16, 1),
    (17, 4),
    (18, 16);

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
) ENGINE = MyISAM AUTO_INCREMENT = 19 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

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
    (
        2,
        'FR001',
        'TechSolutions',
        'Tech Solutions Ltd',
        1
    ),
    (
        3,
        'FR002',
        'GlobalTrade',
        'Global Trade Inc.',
        2
    ),
    (
        4,
        'FR003',
        'MediSupply',
        'Medical Supplies Co.',
        3
    ),
    (
        5,
        'FR004',
        'AgroFresh',
        'AgroFresh Enterprises',
        4
    ),
    (
        6,
        'FR005',
        'BuildCorp',
        'Building Materials Corp.',
        5
    ),
    (
        7,
        'FR006',
        'AutoParts',
        'Automotive Parts Ltd.',
        6
    ),
    (
        8,
        'FR007',
        'FoodNation',
        'Food & Beverage Distributors',
        7
    ),
    (
        9,
        'FR008',
        'EcoPower',
        'Renewable Energy Solutions',
        8
    ),
    (
        10,
        'FR009',
        'TextileWorld',
        'Textile Manufacturing Group',
        9
    ),
    (
        11,
        'FR0107',
        'PharmaLife',
        'Pharmaceuticals International',
        3
    ),
    (
        13,
        '12345',
        'aymen',
        'Pharmaceuticals International',
        21
    ),
    (14, '123457', 'kkk', 'kjb7777', 16),
    (15, 'gggg', 'iiiii', 'jnpij', 104),
    (16, '12345777777', 'mm', 'klnj777', 16),
    (17, '1234588', 'SS', 'SS', 1),
    (18, '123458879', 'mm7', 'ss', 18);

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
) ENGINE = MyISAM AUTO_INCREMENT = 80 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

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
        9,
        'GB-2025-4256777777',
        '2025-04-17',
        '2025-04-17',
        '2025-05-17',
        53465.000,
        20,
        9,
        4,
        4,
        2
    ),
    (
        10,
        'GB-2025-916577',
        '2025-04-17',
        '2025-04-17',
        '2025-05-17',
        44444.000,
        4,
        5,
        1,
        2,
        2
    ),
    (
        8,
        'GB-2025-7474',
        '2025-04-17',
        '2025-04-17',
        '2025-06-16',
        123545698.000,
        5,
        5,
        1,
        17,
        8
    ),
    (
        11,
        'GB-2025-6985',
        '2025-04-17',
        '2025-04-17',
        '2025-06-16',
        53465000.000,
        6,
        5,
        8,
        15,
        2
    ),
    (
        12,
        'GB-2025-83211',
        '2025-04-18',
        '2025-04-18',
        '2025-07-17',
        2151.000,
        3,
        3,
        1,
        25,
        8
    ),
    (
        13,
        'GB-2025-4541',
        '2025-04-18',
        '2025-05-18',
        '2025-05-18',
        156.000,
        5,
        5,
        2,
        5,
        2
    ),
    (
        14,
        'GB-2025-9838',
        '2025-04-18',
        '2025-04-18',
        '2025-05-17',
        545.000,
        5,
        7,
        8,
        4,
        7
    ),
    (
        15,
        'GB-2025-7150',
        '2025-04-18',
        '2025-04-18',
        '2025-10-15',
        531.000,
        4,
        7,
        1,
        5,
        2
    ),
    (
        16,
        'GB-2025-5427',
        '2025-04-18',
        '2025-04-18',
        '2025-05-17',
        3515.000,
        3,
        7,
        2,
        4,
        3
    ),
    (
        17,
        'GB-2025-5212',
        '2025-04-18',
        '2025-04-18',
        '2025-10-15',
        541654.000,
        6,
        4,
        4,
        4,
        3
    ),
    (
        18,
        'GB-2025-3779',
        '2025-04-18',
        '2025-04-18',
        '2025-10-15',
        655.000,
        6,
        7,
        4,
        5,
        2
    ),
    (
        19,
        'GB-2025-9298',
        '2025-04-18',
        '2025-04-18',
        '2025-07-29',
        21151.000,
        5,
        6,
        1,
        4,
        3
    ),
    (
        20,
        'GB-2025-7608',
        '2025-04-18',
        '2025-04-18',
        '2025-05-18',
        144.000,
        5,
        8,
        2,
        4,
        1
    ),
    (
        21,
        'GB-2025-5648',
        '2025-02-15',
        '2025-04-15',
        '2025-05-15',
        4444.000,
        3,
        6,
        2,
        5,
        8
    ),
    (
        22,
        'GB-2',
        '2025-02-14',
        '2025-02-14',
        '2025-02-14',
        4.000,
        4,
        4,
        2,
        25,
        7
    ),
    (
        23,
        'GB-2025-3553',
        '2025-04-28',
        '2025-04-28',
        '2025-05-17',
        654555.000,
        4,
        4,
        2,
        2,
        7
    ),
    (
        24,
        'GB-2025-4999-HIBAAA',
        '2025-05-09',
        '2025-05-14',
        '2025-07-21',
        5454.000,
        5,
        7,
        1,
        4,
        7
    ),
    (
        25,
        'GB-2025-4715-Djouhara',
        '2025-05-14',
        '2025-05-14',
        '2025-05-17',
        564654.000,
        3,
        7,
        2,
        1,
        3
    ),
    (
        26,
        'GB-2025-9229',
        '2025-05-16',
        '2025-05-16',
        '2025-05-17',
        8789789789.000,
        6,
        7,
        4,
        195,
        1
    ),
    (
        27,
        'GB-2025-2484',
        '2025-05-17',
        '2025-05-17',
        '2025-06-16',
        111.000,
        8,
        3,
        14,
        113,
        13
    ),
    (
        28,
        'GB-2025-6029-hmara',
        '2025-05-17',
        '2025-05-17',
        '2025-05-17',
        88888.000,
        12,
        8,
        11,
        65,
        9
    ),
    (
        29,
        'GB-2025-8828',
        '2025-05-17',
        '2025-05-17',
        '2025-05-17',
        44444000.000,
        5,
        13,
        13,
        66,
        9
    ),
    (
        30,
        'G-EXP-TMR-1',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        1000.000,
        1,
        1,
        1,
        1,
        1
    ),
    (
        31,
        'G-EXP-TMR-2',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        2000.000,
        2,
        2,
        2,
        2,
        2
    ),
    (
        32,
        'G-EXP-TMR-3',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        3000.000,
        3,
        3,
        3,
        3,
        3
    ),
    (
        33,
        'G-EXP-TMR-4',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        4000.000,
        4,
        4,
        4,
        4,
        4
    ),
    (
        34,
        'G-EXP-TMR-5',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        5000.000,
        5,
        5,
        5,
        5,
        5
    ),
    (
        35,
        'G-EXP-TMR-6',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        6000.000,
        6,
        6,
        6,
        6,
        6
    ),
    (
        36,
        'G-EXP-TMR-7',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        7000.000,
        7,
        7,
        7,
        7,
        7
    ),
    (
        37,
        'G-EXP-TMR-8',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        8000.000,
        8,
        8,
        8,
        8,
        8
    ),
    (
        38,
        'G-EXP-TMR-9',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        9000.000,
        9,
        9,
        9,
        9,
        9
    ),
    (
        39,
        'G-EXP-TMR-10',
        '2025-05-17',
        '2025-05-17',
        '2025-05-18',
        10000.000,
        10,
        10,
        10,
        10,
        10
    ),
    (
        40,
        'G-EXP-3D-1',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1100.000,
        1,
        2,
        1,
        1,
        1
    ),
    (
        41,
        'G-EXP-3D-2',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1200.000,
        2,
        3,
        2,
        2,
        2
    ),
    (
        42,
        'G-EXP-3D-3',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1300.000,
        3,
        4,
        3,
        3,
        3
    ),
    (
        43,
        'G-EXP-3D-4',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1400.000,
        4,
        5,
        4,
        4,
        4
    ),
    (
        44,
        'G-EXP-3D-5',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1500.000,
        5,
        6,
        5,
        5,
        5
    ),
    (
        45,
        'G-EXP-3D-6',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1600.000,
        6,
        7,
        6,
        6,
        6
    ),
    (
        46,
        'G-EXP-3D-7',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1700.000,
        7,
        8,
        7,
        7,
        7
    ),
    (
        47,
        'G-EXP-3D-8',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1800.000,
        8,
        9,
        8,
        8,
        8
    ),
    (
        48,
        'G-EXP-3D-9',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        1900.000,
        9,
        10,
        9,
        9,
        9
    ),
    (
        49,
        'G-EXP-3D-10',
        '2025-05-17',
        '2025-05-17',
        '2025-05-20',
        2000.000,
        10,
        1,
        10,
        10,
        10
    ),
    (
        50,
        'G-EXP-22D-1',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2100.000,
        1,
        3,
        1,
        1,
        1
    ),
    (
        51,
        'G-EXP-22D-2',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2200.000,
        2,
        4,
        2,
        2,
        2
    ),
    (
        52,
        'G-EXP-22D-3',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2300.000,
        3,
        5,
        3,
        3,
        3
    ),
    (
        53,
        'G-EXP-22D-4',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2400.000,
        4,
        6,
        4,
        4,
        4
    ),
    (
        54,
        'G-EXP-22D-5',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2500.000,
        5,
        7,
        5,
        5,
        5
    ),
    (
        55,
        'G-EXP-22D-6',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2600.000,
        6,
        8,
        6,
        6,
        6
    ),
    (
        56,
        'G-EXP-22D-7',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2700.000,
        7,
        9,
        7,
        7,
        7
    ),
    (
        57,
        'G-EXP-22D-8',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2800.000,
        8,
        10,
        8,
        8,
        8
    ),
    (
        58,
        'G-EXP-22D-9',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        2900.000,
        9,
        1,
        9,
        9,
        9
    ),
    (
        59,
        'G-EXP-22D-10',
        '2025-05-17',
        '2025-05-17',
        '2025-06-08',
        3000.000,
        10,
        2,
        10,
        10,
        10
    ),
    (
        60,
        'G-EXP-OLD-1',
        '2025-05-17',
        '2025-05-17',
        '2025-05-16',
        3100.000,
        1,
        4,
        1,
        1,
        1
    ),
    (
        61,
        'G-EXP-OLD-2',
        '2025-05-17',
        '2025-05-17',
        '2025-05-16',
        3200.000,
        2,
        5,
        2,
        2,
        2
    ),
    (
        62,
        'G-EXP-OLD-3',
        '2025-05-17',
        '2025-05-17',
        '2025-05-16',
        3300.000,
        3,
        6,
        3,
        3,
        3
    ),
    (
        63,
        'G-EXP-OLD-4',
        '2025-05-17',
        '2025-05-17',
        '2025-05-15',
        3400.000,
        4,
        7,
        4,
        4,
        4
    ),
    (
        64,
        'G-EXP-OLD-5',
        '2025-05-17',
        '2025-05-17',
        '2025-05-15',
        3500.000,
        5,
        8,
        5,
        5,
        5
    ),
    (
        65,
        'G-EXP-OLD-6',
        '2025-05-17',
        '2025-05-17',
        '2025-05-15',
        3600.000,
        6,
        9,
        6,
        6,
        6
    ),
    (
        66,
        'G-EXP-OLD-7',
        '2025-05-17',
        '2025-05-17',
        '2025-05-15',
        3700.000,
        7,
        10,
        7,
        7,
        7
    ),
    (
        67,
        'G-EXP-OLD-8',
        '2025-05-17',
        '2025-05-17',
        '2025-05-16',
        3800.000,
        8,
        1,
        8,
        8,
        8
    ),
    (
        68,
        'G-EXP-OLD-9',
        '2025-05-17',
        '2025-05-17',
        '2025-05-15',
        3900.000,
        9,
        2,
        9,
        9,
        9
    ),
    (
        69,
        'G-EXP-OLD-10',
        '2025-05-17',
        '2025-05-17',
        '2025-05-16',
        4000.000,
        10,
        3,
        10,
        10,
        10
    ),
    (
        70,
        'GB-2025-A1',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        1000.000,
        1,
        1,
        1,
        1,
        1
    ),
    (
        71,
        'GB-2025-A2',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        2000.000,
        2,
        2,
        2,
        2,
        2
    ),
    (
        72,
        'GB-2025-A3',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        3000.000,
        3,
        3,
        3,
        3,
        3
    ),
    (
        73,
        'GB-2025-A4',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        4000.000,
        4,
        4,
        4,
        4,
        4
    ),
    (
        74,
        'GB-2025-A5',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        5000.000,
        5,
        5,
        5,
        5,
        5
    ),
    (
        75,
        'GB-2025-A6',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        6000.000,
        6,
        6,
        6,
        6,
        6
    ),
    (
        76,
        'GB-2025-A7',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        7000.000,
        7,
        7,
        7,
        7,
        7
    ),
    (
        77,
        'GB-2025-A8',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        8000.000,
        8,
        8,
        8,
        8,
        8
    ),
    (
        78,
        'GB-2025-A9',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        9000.000,
        9,
        9,
        9,
        9,
        9
    ),
    (
        79,
        'GB-2025-A10',
        '2025-05-17',
        '2025-05-17',
        '2025-05-27',
        10000.000,
        10,
        10,
        10,
        10,
        10
    );

-- --------------------------------------------------------
--
-- Structure de la table `garanties_alertes`
--
DROP TABLE IF EXISTS `garanties_alertes`;

CREATE TABLE IF NOT EXISTS `garanties_alertes` (
    `id` int NOT NULL AUTO_INCREMENT,
    `garantie_id` int NOT NULL,
    `utilisateur_id` int NOT NULL,
    `type_alerte` enum('critical', 'urgent', 'preventive', 'custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `date_alerte` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `jours_restants` int NOT NULL,
    `vue` tinyint(1) DEFAULT '0',
    `date_vue` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `garantie_id` (`garantie_id`),
    KEY `utilisateur_id` (`utilisateur_id`),
    KEY `vue` (`vue`),
    KEY `type_alerte` (`type_alerte`)
) ENGINE = InnoDB AUTO_INCREMENT = 77 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `garanties_alertes`
--
INSERT INTO
    `garanties_alertes` (
        `id`,
        `garantie_id`,
        `utilisateur_id`,
        `type_alerte`,
        `date_alerte`,
        `jours_restants`,
        `vue`,
        `date_vue`
    )
VALUES
    (
        1,
        13,
        1,
        'preventive',
        '2025-05-17 15:32:45',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        2,
        22,
        1,
        'preventive',
        '2025-05-17 15:35:00',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        3,
        11,
        1,
        'preventive',
        '2025-05-17 15:35:48',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        4,
        21,
        1,
        'preventive',
        '2025-05-17 15:35:48',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        5,
        23,
        1,
        'preventive',
        '2025-05-17 15:35:48',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        6,
        27,
        1,
        'preventive',
        '2025-05-17 15:35:48',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        7,
        28,
        1,
        'preventive',
        '2025-05-17 15:48:19',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        8,
        28,
        1,
        'preventive',
        '2025-05-17 15:49:18',
        0,
        1,
        '2025-05-17 15:49:18'
    ),
    (
        9,
        14,
        1,
        'preventive',
        '2025-05-17 16:16:41',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        10,
        16,
        1,
        'preventive',
        '2025-05-17 16:20:31',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        11,
        16,
        1,
        'preventive',
        '2025-05-17 16:20:53',
        0,
        1,
        '2025-05-17 16:20:53'
    ),
    (
        12,
        26,
        1,
        'preventive',
        '2025-05-17 16:20:58',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        13,
        29,
        1,
        'preventive',
        '2025-05-17 16:21:03',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        14,
        20,
        1,
        'preventive',
        '2025-05-17 16:45:06',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        15,
        31,
        1,
        'preventive',
        '2025-05-17 16:55:52',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        16,
        31,
        1,
        'preventive',
        '2025-05-17 16:55:57',
        0,
        1,
        '2025-05-17 16:55:57'
    ),
    (
        17,
        31,
        1,
        'preventive',
        '2025-05-17 16:56:06',
        0,
        1,
        '2025-05-17 16:56:06'
    ),
    (
        18,
        31,
        1,
        'preventive',
        '2025-05-17 16:56:12',
        0,
        1,
        '2025-05-17 16:56:12'
    ),
    (
        19,
        31,
        1,
        'preventive',
        '2025-05-17 16:56:15',
        0,
        1,
        '2025-05-17 16:56:15'
    ),
    (
        20,
        31,
        1,
        'preventive',
        '2025-05-17 16:56:21',
        0,
        1,
        '2025-05-17 16:56:21'
    ),
    (
        21,
        31,
        1,
        'preventive',
        '2025-05-17 16:56:28',
        0,
        1,
        '2025-05-17 16:56:28'
    ),
    (
        22,
        31,
        1,
        'preventive',
        '2025-05-17 16:58:10',
        0,
        1,
        '2025-05-17 16:58:10'
    ),
    (
        23,
        31,
        1,
        'preventive',
        '2025-05-17 16:58:14',
        0,
        1,
        '2025-05-17 16:58:14'
    ),
    (
        24,
        31,
        1,
        'preventive',
        '2025-05-17 16:58:23',
        0,
        1,
        '2025-05-17 16:58:23'
    ),
    (
        25,
        31,
        1,
        'preventive',
        '2025-05-17 16:58:27',
        0,
        1,
        '2025-05-17 16:58:27'
    ),
    (
        26,
        31,
        1,
        'preventive',
        '2025-05-17 16:58:31',
        0,
        1,
        '2025-05-17 16:58:31'
    ),
    (
        27,
        31,
        1,
        'preventive',
        '2025-05-17 16:58:33',
        0,
        1,
        '2025-05-17 16:58:33'
    ),
    (
        28,
        30,
        1,
        'preventive',
        '2025-05-17 16:58:55',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        29,
        32,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        30,
        33,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        31,
        34,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        32,
        35,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        33,
        36,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        34,
        37,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        35,
        38,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        36,
        39,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        37,
        40,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        38,
        41,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        39,
        42,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        40,
        43,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        41,
        44,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        42,
        45,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        43,
        46,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        44,
        47,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        45,
        48,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        46,
        49,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        47,
        50,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        48,
        51,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        49,
        52,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        50,
        53,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        51,
        54,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        52,
        55,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        53,
        56,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        54,
        57,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        55,
        58,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        56,
        59,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        57,
        60,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        58,
        61,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        59,
        62,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        60,
        63,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        61,
        64,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        62,
        65,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        63,
        66,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        64,
        67,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        65,
        68,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        66,
        69,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        67,
        70,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        68,
        71,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        69,
        72,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        70,
        73,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        71,
        74,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        72,
        75,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        73,
        76,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        74,
        77,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        75,
        78,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    ),
    (
        76,
        79,
        1,
        'preventive',
        '2025-05-17 16:59:09',
        0,
        1,
        '2025-05-17 16:59:09'
    );

-- --------------------------------------------------------
--
-- Structure de la table `garanties_preferences`
--
DROP TABLE IF EXISTS `garanties_preferences`;

CREATE TABLE IF NOT EXISTS `garanties_preferences` (
    `id` int NOT NULL AUTO_INCREMENT,
    `garantie_id` int NOT NULL,
    `utilisateur_id` int NOT NULL,
    `jours_notification` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_preference` (`garantie_id`, `utilisateur_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `historique`
--
DROP TABLE IF EXISTS `historique`;

CREATE TABLE IF NOT EXISTS `historique` (
    `id` int NOT NULL AUTO_INCREMENT,
    `utilisateur_id` int NOT NULL,
    `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `table_concernee` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `element_id` int DEFAULT NULL,
    `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    `date_operation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ip_utilisateur` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `utilisateur_id` (`utilisateur_id`),
    KEY `table_concernee` (`table_concernee`),
    KEY `element_id` (`element_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `historique`
--
INSERT INTO
    `historique` (
        `id`,
        `utilisateur_id`,
        `action`,
        `table_concernee`,
        `element_id`,
        `details`,
        `date_operation`,
        `ip_utilisateur`
    )
VALUES
    (
        1,
        1,
        'modification',
        'direction',
        21,
        '{\"avant\":{\"code\":\"DIR-011\",\"libelle\":\"SOCIETE GENERALE11\"},\"apres\":{\"code\":\"DIR-011\",\"libelle\":\"SOCIETE GENERALE17\"}}',
        '2025-05-17 23:40:54',
        '::1'
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
) ENGINE = MyISAM AUTO_INCREMENT = 9 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `liberation`
--
INSERT INTO
    `liberation` (
        `id`,
        `num`,
        `date_liberation`,
        `Type_liberation_id`,
        `garantie_id`
    )
VALUES
    (1, 123, '2025-04-17', 0, 6),
    (2, 555, '2025-04-17', 0, 8),
    (3, 12346, '2025-04-17', 0, 9),
    (4, 1234, '2025-04-17', 0, 10),
    (5, 3545, '2025-04-18', 0, 12),
    (6, 12354, '2025-04-18', 0, 19),
    (7, 7777, '2025-05-16', 0, 25),
    (8, 5557, '2025-05-18', 0, 61);

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
) ENGINE = MyISAM AUTO_INCREMENT = 19 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `monnaie`
--
INSERT INTO
    `monnaie` (`id`, `code`, `label`, `symbole`)
VALUES
    (1, 'USD', 'Dollar Américain', '$'),
    (2, 'EUR', 'Euro', '€'),
    (8, '123', 'meriem', 'is '),
    (4, 'GBP', 'Livre Sterling', '£'),
    (5, 'CAD', 'Dollar Canadiene', '$'),
    (9, '12365', 'lb', '7'),
    (6, 'JPY', 'Yen Japonais', '¥'),
    (7, 'CHF', 'Franc Suisse', 'CHF'),
    (10, 'AUD', 'Dollar Australien', 'A$'),
    (11, 'CNY', 'Yuan Chinois', '¥'),
    (12, 'INR', 'Roupie Indienne', '₹'),
    (13, 'RUB', 'Rouble Russe', '₽'),
    (14, 'BRL', 'Réal Brésilien', 'R$'),
    (15, 'MXN', 'Peso Mexicain', '$'),
    (16, 'SEK', 'Couronne Suédoise', 'kr'),
    (17, 'DZD', 'Dinar Algérien', 'دج'),
    (18, 'SARA', 'SARA', '$$');

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
) ENGINE = MyISAM AUTO_INCREMENT = 148 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `pays`
--
INSERT INTO
    `pays` (`id`, `code`, `label`)
VALUES
    (1, 'AF', 'Afghanistan'),
    (2, 'AL', 'Albanie'),
    (3, 'DZ', 'Algérie'),
    (4, 'AD', 'Andorre'),
    (5, 'AO', 'Angola'),
    (6, 'AG', 'Antigua-et-Barbuda'),
    (7, 'AR', 'Argentine'),
    (8, 'AM', 'Arménie'),
    (9, 'AU', 'Australie'),
    (10, 'AT', 'Autriche'),
    (11, 'AZ', 'Azerbaïdjan'),
    (12, 'BS', 'Bahamas'),
    (13, 'BH', 'Bahreïn'),
    (14, 'BD', 'Bangladesh'),
    (15, 'BB', 'Barbade'),
    (16, 'BY', 'Biélorussie'),
    (17, 'BE', 'Belgique'),
    (18, 'BZ', 'Belize'),
    (19, 'BJ', 'Bénin'),
    (20, 'BT', 'Bhoutan'),
    (21, 'BO', 'Bolivie'),
    (22, 'BA', 'Bosnie-Herzégovine'),
    (23, 'BW', 'Botswana'),
    (24, 'BR', 'Brésil'),
    (25, 'BN', 'Brunei'),
    (26, 'BG', 'Bulgarie'),
    (27, 'BF', 'Burkina Faso'),
    (28, 'BI', 'Burundi'),
    (29, 'KH', 'Cambodge'),
    (30, 'CM', 'Cameroun'),
    (31, 'CA', 'Canada'),
    (32, 'CV', 'Cap-Vert'),
    (33, 'CF', 'République Centrafricaine'),
    (34, 'TD', 'Tchad'),
    (35, 'CL', 'Chili'),
    (36, 'CN', 'Chine'),
    (37, 'CO', 'Colombie'),
    (38, 'KM', 'Comores'),
    (39, 'CG', 'Congo'),
    (40, 'CR', 'Costa Rica'),
    (41, 'HR', 'Croatie'),
    (42, 'CU', 'Cuba'),
    (43, 'CY', 'Chypre'),
    (44, 'CZ', 'République Tchèque'),
    (45, 'DK', 'Danemark'),
    (46, 'DJ', 'Djibouti'),
    (47, 'DO', 'République Dominicaine'),
    (48, 'EC', 'Équateur'),
    (49, 'EG', 'Égypte'),
    (50, 'SV', 'El Salvador'),
    (51, 'GQ', 'Guinée Équatoriale'),
    (52, 'ER', 'Érythrée'),
    (53, 'EE', 'Estonie'),
    (54, 'ET', 'Éthiopie'),
    (55, 'FI', 'Finlande'),
    (56, 'FR', 'France'),
    (57, 'GA', 'Gabon'),
    (58, 'GM', 'Gambie'),
    (59, 'GE', 'Géorgie'),
    (60, 'DE', 'Allemagne'),
    (61, 'GH', 'Ghana'),
    (62, 'GR', 'Grèce'),
    (63, 'GT', 'Guatemala'),
    (64, 'GN', 'Guinée'),
    (65, 'HT', 'Haïti'),
    (66, 'HN', 'Honduras'),
    (67, 'HU', 'Hongrie'),
    (68, 'IS', 'Islande'),
    (69, 'IN', 'Inde'),
    (70, 'ID', 'Indonésie'),
    (71, 'IR', 'Iran'),
    (72, 'IQ', 'Irak'),
    (73, 'IE', 'Irlande'),
    (74, 'IT', 'Italie'),
    (75, 'JM', 'Jamaïque'),
    (76, 'JP', 'Japon'),
    (77, 'JO', 'Jordanie'),
    (78, 'KZ', 'Kazakhstan'),
    (79, 'KE', 'Kenya'),
    (80, 'KW', 'Koweït'),
    (81, 'KG', 'Kirghizistan'),
    (82, 'LA', 'Laos'),
    (83, 'LV', 'Lettonie'),
    (84, 'LB', 'Liban'),
    (85, 'LY', 'Libye'),
    (86, 'LT', 'Lituanie'),
    (87, 'LU', 'Luxembourg'),
    (88, 'MG', 'Madagascar'),
    (89, 'MY', 'Malaisie'),
    (90, 'MV', 'Maldives'),
    (91, 'ML', 'Mali'),
    (92, 'MT', 'Malte'),
    (93, 'MX', 'Mexique'),
    (94, 'MD', 'Moldavie'),
    (95, 'MC', 'Monaco'),
    (96, 'MA', 'Maroc'),
    (97, 'MZ', 'Mozambique'),
    (98, 'MM', 'Myanmar'),
    (99, 'NA', 'Namibie'),
    (100, 'NP', 'Népal'),
    (101, 'NL', 'Pays-Bas'),
    (102, 'NZ', 'Nouvelle-Zélande'),
    (103, 'NI', 'Nicaragua'),
    (104, 'NE', 'Niger'),
    (105, 'NG', 'Nigéria'),
    (106, 'NO', 'Norvège'),
    (107, 'OM', 'Oman'),
    (108, 'PK', 'Pakistan'),
    (109, 'PA', 'Panama'),
    (110, 'PY', 'Paraguay'),
    (111, 'PE', 'Pérou'),
    (112, 'PH', 'Philippines'),
    (113, 'PL', 'Pologne'),
    (114, 'PT', 'Portugal'),
    (115, 'QA', 'Qatar'),
    (116, 'RO', 'Roumanie'),
    (117, 'RU', 'Russie'),
    (118, 'SA', 'Arabie Saoudite'),
    (119, 'SN', 'Sénégal'),
    (120, 'RS', 'Serbie'),
    (121, 'SG', 'Singapour'),
    (122, 'SK', 'Slovaquie'),
    (123, 'SI', 'Slovénie'),
    (124, 'ZA', 'Afrique du Sud'),
    (125, 'KR', 'Corée du Sud'),
    (126, 'ES', 'Espagne'),
    (127, 'LK', 'Sri Lanka'),
    (128, 'SE', 'Suède'),
    (129, 'CH', 'Suisse'),
    (130, 'SY', 'Syrie'),
    (131, 'TW', 'Taïwan'),
    (132, 'TJ', 'Tadjikistan'),
    (133, 'TZ', 'Tanzanie'),
    (134, 'TH', 'Thaïlande'),
    (135, 'TN', 'Tunisie'),
    (136, 'TR', 'Turquie'),
    (137, 'UA', 'Ukraine'),
    (138, 'AE', 'Émirats Arabes Unis'),
    (139, 'GB', 'Royaume-Uni'),
    (140, 'US', 'États-Unis'),
    (141, 'UY', 'Uruguay'),
    (142, 'UZ', 'Ouzbékistan'),
    (143, 'VE', 'Venezuela'),
    (144, 'VN', 'Vietnam'),
    (145, 'YE', 'Yémen'),
    (146, 'ZM', 'Zambie'),
    (147, 'ZW', 'Zimbabwe');

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
) ENGINE = MyISAM AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--
INSERT INTO
    `role` (`id`, `nom_role`)
VALUES
    (1, 'admin'),
    (2, 'agent'),
    (3, 'responsable');

-- --------------------------------------------------------
--
-- Structure de la table `type_amd`
--
DROP TABLE IF EXISTS `type_amd`;

CREATE TABLE IF NOT EXISTS `type_amd` (
    `id` int NOT NULL AUTO_INCREMENT,
    `code` varchar(5) NOT NULL,
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
    (1, 'TOTA', 'TOTA'),
    (2, 'AUG_M', 'AUGMENTATION MONTANT'),
    (3, 'PRORO', 'PROROGATION');

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
    KEY `direction` (`structure`)
) ENGINE = MyISAM AUTO_INCREMENT = 17 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--
INSERT INTO
    `users` (
        `id`,
        `nom_user`,
        `prenom_user`,
        `username`,
        `password`,
        `status`,
        `Role`,
        `structure`
    )
VALUES
    (4, 'Aymen', 'Aymeen', 'Aymeeen', '123', 1, 5, 9),
    (
        15,
        'ait',
        '',
        'ait',
        '$2y$10$z0W5PmLSnutL7/wBrq9ODOBlVhyWMbsW.xmtq5.5wvcf5VkHZwTzG',
        1,
        1,
        14
    ),
    (
        2,
        'Djouhara',
        'Deghbar',
        'djou1',
        '$2y$10$lQN71HcY5pd54QyT.EZ36OBePhBT.IBD/kSC5N6sO9bI3MJnD6Vwq',
        1,
        1,
        20
    ),
    (
        3,
        'kln',
        '',
        'kjbij',
        '$2y$10$9lTQ7AfwcTm9iuF1sbOQzuFZ5bUV92HqLJVXGll9qrxlF/YXiFYsC',
        1,
        2,
        8
    ),
    (
        5,
        'kljj',
        '',
        'klon',
        '$2y$10$8qxEuCdrukWR9G5bxyhuW.aTI1hkW0XDKxcv4r8hR4hfBFZqrD.M2',
        1,
        1,
        3
    ),
    (
        6,
        'Djouhara',
        'Deghbar',
        'meriem',
        '$2y$10$ujy0TbSrPWYtJq3G5YWPOOWNALQKaQKR13.2vHiVjBgpWMoRsGKGS',
        1,
        1,
        10
    ),
    (
        7,
        'jn',
        '',
        'kln',
        '$2y$10$1NZfTkfd/Pm0BivFYbv2AOSymoEse0CKCvd7Iv.AkyCnfeTb3cF6W',
        1,
        1,
        2
    ),
    (
        8,
        'djouharadgb',
        '',
        'jorrnjf',
        '$2y$10$0pHJE8nvtK9oFDoxK.jWPuWjhI0CsQp683fOGHl0M.dXYzXPSoUcy',
        1,
        3,
        10
    ),
    (
        9,
        'Djouhara',
        'Deghbar',
        'dd',
        '$2y$10$QYPt96ZUICiRXJAFJDscUeySn5b5.fGpMYF2d7i2PU0zOpsLJK8TS',
        1,
        1,
        7
    ),
    (
        10,
        'n',
        '',
        'n',
        '$2y$10$aKdE.3wEIKubagCkqluWoOCzOInTqGhhl.GQtiKUmze97deUJSbRa',
        1,
        2,
        8
    ),
    (
        11,
        'Djouhara',
        'Deghbar',
        'djouharadgb1',
        '$2y$10$YwE6klxKVCLs.Z3eUnx6CuRGmm0wIioo0aBlX4mBDio27EZXz3q.2',
        0,
        2,
        8
    ),
    (
        12,
        'Djouhara',
        'Deghbar',
        'klon3',
        '$2y$10$A4xTL1aqU04mcIUdnZOWpubVjQ0HBTcULJxv/tPPMff/JnT4lafwS',
        0,
        3,
        19
    ),
    (
        13,
        'kjkj',
        '',
        '12345',
        '$2y$10$8ArYzPDLRXGjz1T2r/GLDeWH0iDw3r9CNIwPpZouqOiV04D39dH12',
        1,
        1,
        15
    ),
    (
        14,
        'Djouhara',
        'Deghbar',
        'djouharadgb4',
        '$2y$10$xjk0JBvdL.IV56iZxPV1kuQ46gIT0eLWhSQLuuRWPwDDohNrnsTbi',
        1,
        1,
        6
    ),
    (
        16,
        'a',
        '',
        'a',
        'P@ssword123P@ssword123P@ssword123',
        1,
        1,
        6
    );

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;