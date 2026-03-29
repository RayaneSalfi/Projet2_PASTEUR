-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 11 sep. 2025 à 07:58
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
-- Base de données : `cliniquepasteur`
--

USE grp03ClinPasteur ;

-- --------------------------------------------------------

--
-- Structure de la table `etatinclusion`
--

DROP TABLE IF EXISTS `etatinclusion`;
CREATE TABLE IF NOT EXISTS `etatinclusion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleEtat` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etude`
--

DROP TABLE IF EXISTS `etude`;
CREATE TABLE IF NOT EXISTS `etude` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomEtu` varchar(100) NOT NULL,
  `descEtude` text,
  `idProtocole` int DEFAULT NULL,
  `idQuestionnaire` int DEFAULT NULL, 
  `idOrganisme` int DEFAULT NULL,
  `dateDebEtu` date DEFAULT NULL,
  `dateFinEtu` date DEFAULT NULL,
  `idChirResp` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idProtocole` (`idProtocole`),
  KEY `idQuestionnaire` (`idQuestionnaire`),
  KEY `idOrganisme` (`idOrganisme`),
  KEY `idChirResp` (`idChirResp`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inclusion`
--

DROP TABLE IF EXISTS `inclusion`;
CREATE TABLE IF NOT EXISTS `inclusion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idPatient` int DEFAULT NULL,
  `dateInclusion` date DEFAULT NULL,
  `idEtude` int DEFAULT NULL,
  `idEtat` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_patient_etude` (`idPatient`,`idEtude`),
  KEY `idEtude` (`idEtude`),
  KEY `idEtat` (`idEtat`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `maladie`
--

DROP TABLE IF EXISTS `maladie`;
CREATE TABLE IF NOT EXISTS `maladie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomMaladie` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
CREATE TABLE IF NOT EXISTS `personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomPersonnels` varchar(100) DEFAULT NULL,
  `prenomPersonnels` varchar(100) DEFAULT NULL,
  `idSpecialite` int DEFAULT NULL,
  `idService` int DEFAULT NULL,
  `idRole` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idSpecialite` (`idSpecialite`),
  KEY `idService` (`idService`),
  KEY `idRole` (`idRole`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `organe`
--

DROP TABLE IF EXISTS `organe`;
CREATE TABLE IF NOT EXISTS `organe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomOrgane` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `organisme`
--

DROP TABLE IF EXISTS `organisme`;
CREATE TABLE IF NOT EXISTS `organisme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomOrg` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomPat` varchar(100) DEFAULT NULL,
  `prenomPat` varchar(100) DEFAULT NULL,
  `dateNaisPat` date DEFAULT NULL,
  `sexe` char(1) DEFAULT NULL,
  `numDossierClinique` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numDossierClinique` (`numDossierClinique`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `maladiepatient`
--

DROP TABLE IF EXISTS `maladiepatient`;
CREATE TABLE IF NOT EXISTS `maladiepatient` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idPatient` int DEFAULT NULL,
  `idMaladie` int DEFAULT NULL,
  `dateDiagnostic` date DEFAULT NULL,
  `idStade` int DEFAULT NULL,
  `idOrgane` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_maladie_patient` (`idPatient`,`idMaladie`, `dateDiagnostic`),
  KEY `idPatient` (`idPatient`),
  KEY `idMaladie` (`idMaladie`),
  KEY `idStade` (`idStade`),
  KEY `idOrgane` (`idOrgane`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleRole` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleServ` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `specialite`
--

DROP TABLE IF EXISTS `specialite`;
CREATE TABLE IF NOT EXISTS `specialite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleSpec` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stade`
--

DROP TABLE IF EXISTS `stade`;
CREATE TABLE IF NOT EXISTS `stade` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomStade` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `protocole`
--

DROP TABLE IF EXISTS `protocole`;
CREATE TABLE IF NOT EXISTS `protocole` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleProtocole` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `questionnaire`
--

DROP TABLE IF EXISTS `questionnaire`;
CREATE TABLE IF NOT EXISTS `questionnaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleQuestionnaire` varchar(100) NOT NULL,
  `descQuestionnaire` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `theme`
--

DROP TABLE IF EXISTS `theme` ;
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleTheme` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `detailquestionnaire`
--

DROP TABLE IF EXISTS `detailquestionnaire` ;
CREATE TABLE IF NOT EXISTS `detailquestionnaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idQuestionnaire` int NOT NULL,
  `idTheme` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idQuestionnaire` (`idQuestionnaire`),
  KEY `idTheme` (`idTheme`),
  UNIQUE KEY `idx_theme_questionnaire` (`idQuestionnaire`, `idTheme`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `question`
--

DROP TABLE IF EXISTS `question` ;
CREATE TABLE IF NOT EXISTS `question` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numQuestion` int NOT NULL, 
  `idTheme` int NOT NULL,
  `libelleQuestion` varchar(255),
  PRIMARY KEY (`id`),
  KEY `idTheme` (`idTheme`),
  UNIQUE KEY `idx_theme_question` (`numQuestion`, `idTheme`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `reponse`
--

DROP TABLE IF EXISTS `reponse` ;
CREATE TABLE IF NOT EXISTS `reponse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numQuestion` int NOT NULL,
  `idInclusion` int NOT NULL,
  `dateReponse` date,
  PRIMARY KEY (`id`),
  KEY `idInclusion` (`idInclusion`),
  UNIQUE KEY `idx_reponse_patient` (`numQuestion`, `idInclusion`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `detailreponse`
--

DROP TABLE IF EXISTS `detailreponse` ;
CREATE TABLE IF NOT EXISTS `detailreponse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idReponse` int NOT NULL,
  `idQuestion` int NOT NULL,
  `idValeurReponse` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idReponse` (`idReponse`),
  KEY `idQuestion` (`idQuestion`),
  KEY `idValeurReponse` (`idValeurReponse`),
  UNIQUE KEY `idx_reponse_question` (`idReponse`, `idQuestion`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `consultation`
--

DROP TABLE IF EXISTS `consultation` ;
CREATE TABLE IF NOT EXISTS `consultation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idChirResp` int NOT NULL,
  `idInclusion` int NOT NULL,
  `dateConsult` date,
  PRIMARY KEY (`id`),
  KEY `idChirResp` (`idChirResp`),
  KEY `idInclusion` (`idInclusion`),
  UNIQUE KEY `idx_chirurgien_patient` (`idChirResp`, `idInclusion`, `dateConsult`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------

--
-- Structure de la table `valeurreponse`
--

DROP TABLE IF EXISTS `valeurreponse` ; 
CREATE TABLE IF NOT EXISTS `valeurreponse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelleValeur` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
