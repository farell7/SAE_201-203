-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: resauge
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Suppression de la base si elle existe et création d'une nouvelle
DROP DATABASE IF EXISTS resauge;
CREATE DATABASE resauge;
USE resauge;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `pseudo` varchar(50) DEFAULT NULL,
  `code_postal` varchar(5) DEFAULT NULL CHECK (code_postal REGEXP '^[0-9]{5}$'),
  `numero_etudiant` varchar(8) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `role` enum('student','teacher','agent','admin') NOT NULL,
  `valide` tinyint(1) DEFAULT 0,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `numero_etudiant` (`numero_etudiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `materiel`
--

DROP TABLE IF EXISTS `materiel`;
CREATE TABLE `materiel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `etat` enum('neuf','bon','moyen','mauvais') DEFAULT 'bon',
  `photo` varchar(255) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_serie` (`numero_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `salle`
--

DROP TABLE IF EXISTS `salle`;
CREATE TABLE `salle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `capacite` int(11) NOT NULL CHECK (capacite > 0),
  `type` varchar(50) NOT NULL,
  `equipements` text DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `photo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `reservation_materiel`
--

DROP TABLE IF EXISTS `reservation_materiel`;
CREATE TABLE `reservation_materiel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `materiel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `statut` enum('en_attente','validee','refusee','annulee') DEFAULT 'en_attente',
  `commentaire` text DEFAULT NULL,
  `signature_admin` varchar(255) DEFAULT NULL,
  `date_signature` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `materiel_id` (`materiel_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_dates` (`date_debut`, `date_fin`),
  CONSTRAINT `reservation_materiel_ibfk_1` FOREIGN KEY (`materiel_id`) REFERENCES `materiel` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_materiel_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_dates_materiel` CHECK (`date_fin` > `date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `reservation_salle`
--

DROP TABLE IF EXISTS `reservation_salle`;
CREATE TABLE `reservation_salle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `statut` enum('en_attente','validee','refusee','annulee') NOT NULL DEFAULT 'en_attente',
  `commentaire` text DEFAULT NULL,
  `signature_admin` varchar(255) DEFAULT NULL,
  `date_signature` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `salle_id` (`salle_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_dates` (`date_debut`, `date_fin`),
  CONSTRAINT `reservation_salle_ibfk_1` FOREIGN KEY (`salle_id`) REFERENCES `salle` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservation_salle_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_dates_salle` CHECK (`date_fin` > `date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `demande_materiel`
--

DROP TABLE IF EXISTS `demande_materiel`;
CREATE TABLE `demande_materiel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `numero_etudiant` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `annee_mmi` int(11) NOT NULL,
  `groupe_tp` varchar(10) NOT NULL,
  `statut` enum('en_attente','validee','refusee','annulee') NOT NULL DEFAULT 'en_attente',
  `commentaire` text DEFAULT NULL,
  `signature_admin` varchar(255) DEFAULT NULL,
  `date_signature` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_dates` (`date_debut`, `date_fin`),
  CONSTRAINT `demande_materiel_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_dates_demande` CHECK (`date_fin` > `date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `demande_materiel_items`
--

DROP TABLE IF EXISTS `demande_materiel_items`;
CREATE TABLE `demande_materiel_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demande_id` int(11) NOT NULL,
  `nom_materiel` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `specifications` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `demande_id` (`demande_id`),
  CONSTRAINT `demande_materiel_items_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demande_materiel` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion des données de test
INSERT INTO `utilisateur` (`nom`, `prenom`, `email`, `mot_de_passe`, `pseudo`, `code_postal`, `numero_etudiant`, `date_naissance`, `role`, `valide`) VALUES
('Admin', 'System', 'admin@resauge.fr', '$2y$10$3YEGQbHPECHGEpRrEXg0qeABGG1EVe6TJUXdYZkqFGIHQkPVOKii.', 'admin', '77420', NULL, '1990-01-01', 'admin', 1),
('Dupont', 'Jean', 'jean.dupont@student.fr', '$2y$10$3YEGQbHPECHGEpRrEXg0qeABGG1EVe6TJUXdYZkqFGIHQkPVOKii.', 'jdupont', '75001', '20230001', '2000-01-01', 'student', 1),
('Martin', 'Sophie', 'sophie.martin@teacher.fr', '$2y$10$3YEGQbHPECHGEpRrEXg0qeABGG1EVe6TJUXdYZkqFGIHQkPVOKii.', 'smartin', '75002', NULL, '1985-05-15', 'teacher', 1);

INSERT INTO `materiel` (`nom`, `type`, `description`, `numero_serie`, `etat`, `photo`, `disponible`) VALUES
('GoPro Hero 10', 'Camera', 'Caméra d''action 5.3K', 'GP2023001', 'neuf', 'uploads/materiel/gopro_hero10.jpg', 1),
('Canon EOS R5', 'Appareil Photo', 'Appareil photo hybride plein format', 'CN2023001', 'bon', 'uploads/materiel/canon_r5.jpg', 1),
('DJI Mavic Air 2', 'Drone', 'Drone avec caméra 4K', 'DJ2023001', 'bon', 'uploads/materiel/mavic_air2.jpg', 1);

INSERT INTO `salle` (`nom`, `capacite`, `type`, `equipements`, `disponible`, `description`) VALUES
('Salle 101', 30, 'Cours', 'Vidéoprojecteur, Tableau blanc', 1, 'Salle de cours standard'),
('Studio Photo', 10, 'Studio', 'Fond vert, Éclairages LED, Réflecteurs', 1, 'Studio photo professionnel'),
('Salle 212', 15, 'TP', 'Ordinateurs, Logiciels Adobe CC', 1, 'Salle informatique avec postes de travail');

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */; 