-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 26 avr. 2026 à 11:33
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `uber_cueillette`
--

-- --------------------------------------------------------

--
-- Structure de la table `agriculteurs`
--

DROP TABLE IF EXISTS `agriculteurs`;
CREATE TABLE IF NOT EXISTS `agriculteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `cin` varchar(8) NOT NULL,
  `email` varchar(100) NOT NULL,
  `adresse` text NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cin` (`cin`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `pseudo` (`pseudo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `agriculteurs`
--

INSERT INTO `agriculteurs` (`id`, `nom`, `prenom`, `cin`, `email`, `adresse`, `pseudo`, `mot_de_passe`, `date_inscription`) VALUES
(1, 'Kaabi', 'Hiba', '12345678', 'ghkaabi@gmail.com', 'tunis', 'hiba', '$2y$10$i3A5n3WiCT479AjbXalEPuCAuG5CRZwIU6E3q6CgIZX3TJsI7okO2', '2026-04-13 19:20:15'),
(2, 'Kaabi', 'yahya', '12369874', 'yah@gmail.com', 'tunis', 'yahya', '$2y$10$Fvh7GEkRgHp9n6lkt4LlnOGOalq/nv76RKon2b/BJdWFRvdb1ha.W', '2026-04-26 08:57:56'),
(3, 'admin', 'admin', '14785236', 'admin@gmail.com', 'tunis', 'admin', '$2y$10$TRDGHUaXxrCEQwOAa85FK.sgm5oGxhtqaI4oxOS5yUuLWQg5lekUe', '2026-04-26 09:25:40');

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

DROP TABLE IF EXISTS `candidatures`;
CREATE TABLE IF NOT EXISTS `candidatures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offre_id` int NOT NULL,
  `ouvrier_id` int NOT NULL,
  `date_candidature` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','acceptee','refusee') DEFAULT 'en_attente',
  `note` decimal(3,1) DEFAULT NULL,
  `commentaire` text,
  `remuneration_totale` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offre_id` (`offre_id`),
  KEY `ouvrier_id` (`ouvrier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `offre_id`, `ouvrier_id`, `date_candidature`, `statut`, `note`, `commentaire`, `remuneration_totale`) VALUES
(1, 1, 1, '2026-04-13 19:25:37', 'acceptee', NULL, NULL, NULL),
(2, 2, 1, '2026-04-14 12:53:03', 'acceptee', NULL, NULL, NULL),
(3, 3, 3, '2026-04-26 09:04:47', 'acceptee', NULL, NULL, NULL),
(4, 3, 1, '2026-04-26 09:07:41', 'en_attente', NULL, NULL, NULL),
(5, 4, 4, '2026-04-26 09:30:00', 'acceptee', 8.5, 'bien', 450.00),
(6, 3, 6, '2026-04-26 09:49:28', 'en_attente', NULL, NULL, NULL),
(7, 5, 6, '2026-04-26 09:51:54', 'acceptee', 10.0, 'bon', 300.00),
(8, 6, 6, '2026-04-26 09:55:39', 'acceptee', 10.0, 'bon', 400.00);

-- --------------------------------------------------------

--
-- Structure de la table `offres`
--

DROP TABLE IF EXISTS `offres`;
CREATE TABLE IF NOT EXISTS `offres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agriculteur_id` int NOT NULL,
  `type_fruit` varchar(50) NOT NULL,
  `gouvernorat` varchar(50) NOT NULL,
  `adresse_recolte` text NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_ouvriers_demandes` int NOT NULL,
  `nb_ouvriers_acceptes` int DEFAULT '0',
  `prix_journee` decimal(10,2) NOT NULL,
  `date_limite_postulation` date NOT NULL,
  `statut` enum('ouverte','cloturee','terminee') DEFAULT 'ouverte',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `agriculteur_id` (`agriculteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offres`
--

INSERT INTO `offres` (`id`, `agriculteur_id`, `type_fruit`, `gouvernorat`, `adresse_recolte`, `date_debut`, `date_fin`, `nb_ouvriers_demandes`, `nb_ouvriers_acceptes`, `prix_journee`, `date_limite_postulation`, `statut`, `date_creation`) VALUES
(1, 1, 'Abricots', 'Manouba', 'rue khaled', '2026-04-23', '2026-05-10', 10, 1, 30.00, '2026-04-16', 'ouverte', '2026-04-13 19:23:07'),
(2, 1, 'Fraises', 'Le Kef', 'rue khaled', '2026-04-23', '2026-05-10', 10, 1, 30.00, '2026-04-20', 'terminee', '2026-04-14 12:52:40'),
(3, 2, 'Agrumes', 'Manouba', 'rue khaled', '2026-05-08', '2026-05-10', 10, 1, 30.00, '2026-05-05', 'ouverte', '2026-04-26 08:59:14'),
(4, 3, 'Olives', 'Sfax', 'Route de tunis, km 5, Sfax', '2026-05-10', '2026-05-20', 5, 1, 40.00, '2026-05-05', 'terminee', '2026-04-26 09:27:29'),
(5, 3, 'Pêches', 'Bizerte', 'rue khaled', '2026-05-01', '2026-05-09', 8, 1, 25.00, '2026-04-28', 'terminee', '2026-04-26 09:51:19'),
(6, 3, 'Raisins', 'Monastir', 'rue khaled', '2026-05-10', '2026-05-16', 7, 1, 50.00, '2026-04-30', 'terminee', '2026-04-26 09:55:11');

-- --------------------------------------------------------

--
-- Structure de la table `ouvriers`
--

DROP TABLE IF EXISTS `ouvriers`;
CREATE TABLE IF NOT EXISTS `ouvriers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cin` varchar(8) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `note_moyenne` decimal(3,2) DEFAULT '0.00',
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cin` (`cin`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `pseudo` (`pseudo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ouvriers`
--

INSERT INTO `ouvriers` (`id`, `nom`, `prenom`, `email`, `cin`, `photo`, `description`, `pseudo`, `mot_de_passe`, `note_moyenne`, `date_inscription`) VALUES
(1, 'kaabi', 'ghada', 'ghdakaabi@gmail.com', '98765432', '', 'je veux un travaille', 'ghada', '$2y$10$Uy4g2QcTxo3.jOT6FKsV9.gmn/.keY4QPtAA.gJJQVS0Zf2Wg5K9a', 0.00, '2026-04-13 19:25:17'),
(3, 'kaabi', 'Med Hedi', 'hedi@gmail.com', '15963248', '', 'bec', 'hedi', '$2y$10$jdzntcOkVqLmcfr27ucYcObup68tRr0O8Z0VRADr8vcPkvdwqwlHG', 0.00, '2026-04-26 09:04:06'),
(4, 'ouvrier', 'ouvrier', 'ouv@gmail.com', '96385212', '', 'bac', 'ouvrier', '$2y$10$q2E4Z5x6Y7a8B9c0D1e2F3u4v5w6x7y8z9A0B1C2D3E4F5G6H7I8J9K0L1', 8.50, '2026-04-26 09:29:32'),
(6, 'ouvrier', 'ouvrier', 'ouuv@gmail.com', '96385300', '', 'bac+2', 'ouvrierr', '$2y$10$FWf6Sx4Y4GqCJQ1dRyaPxeJYar1NAgUNNVO0EeRUlHKpTfJa.g4jC', 9.99, '2026-04-26 09:49:03');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`ouvrier_id`) REFERENCES `ouvriers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offres`
--
ALTER TABLE `offres`
  ADD CONSTRAINT `offres_ibfk_1` FOREIGN KEY (`agriculteur_id`) REFERENCES `agriculteurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
