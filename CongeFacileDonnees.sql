-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 mai 2025 à 14:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `conge-facile`
--

-- --------------------------------------------------------

--
-- Structure de la table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `department`
--

INSERT INTO `department` (`id`, `name`) VALUES
(4, 'BU Applications mobiles'),
(1, 'BU Symfony'),
(2, 'BU Wordpress');

-- --------------------------------------------------------

--
-- Structure de la table `person`
--

CREATE TABLE `person` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `alert_new_request` tinyint(1) NOT NULL DEFAULT 0,
  `alert_on_answer` tinyint(1) NOT NULL DEFAULT 0,
  `alert_before_vacation` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `person`
--

INSERT INTO `person` (`id`, `last_name`, `first_name`, `manager_id`, `department_id`, `position_id`, `alert_new_request`, `alert_on_answer`, `alert_before_vacation`) VALUES
(1, 'Salesse', 'Frederic', NULL, 1, 3, 0, 0, 0),
(2, 'Martins', 'Jeff', 9, 1, 1, 0, 0, 0),
(3, 'Dupas', 'Lucas', 1, 1, 2, 0, 0, 0),
(5, 'De Oliveira', 'Diego', 1, 1, 3, 0, 0, 0),
(8, 'Turcey', 'Adrien', 1, 2, 2, 0, 0, 0),
(9, 'Daniel', 'Gael', NULL, 4, 4, 0, 0, 0),
(10, 'Ammar', 'fethy', NULL, 2, 4, 0, 0, 0),
(11, 'Valenti', 'Nicolas', 9, 4, 3, 0, 0, 0),
(12, 'De Oliveira', 'Diego', 1, 1, 3, 0, 0, 0),
(16, 'Idasiak', 'Mikael', 1, 1, 3, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `positions`
--

INSERT INTO `positions` (`id`, `name`) VALUES
(3, 'Chef de projet'),
(2, 'Developpement web'),
(4, 'Manager'),
(1, 'Marketing');

-- --------------------------------------------------------

--
-- Structure de la table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `request_type_id` int(11) NOT NULL,
  `collaborator_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `period` int(11) NOT NULL,
  `receipt_file` blob DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `answer_comment` text DEFAULT NULL,
  `answer` tinyint(1) DEFAULT NULL,
  `answer_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `request`
--

INSERT INTO `request` (`id`, `request_type_id`, `collaborator_id`, `department_id`, `created_at`, `start_at`, `end_at`, `period`, `receipt_file`, `comment`, `answer_comment`, `answer`, `answer_at`) VALUES
(4, 2, 2, 1, '2025-05-15 11:00:16', '2025-05-16 00:00:00', '2025-05-17 00:00:00', 1, NULL, NULL, NULL, NULL, NULL),
(5, 2, 2, 1, '2025-05-16 15:39:47', '2025-05-10 15:39:00', '2025-05-17 15:39:00', 5, NULL, 'depart a bali ', NULL, NULL, NULL),
(6, 2, 2, 1, '2025-05-17 17:02:27', '2025-05-24 00:00:00', '2025-05-30 00:00:00', 3, NULL, NULL, NULL, NULL, NULL),
(7, 2, 2, 1, '2025-05-19 09:32:44', '2025-06-13 00:00:00', '2025-06-21 00:00:00', 6, NULL, 'depart', 'desole on veut que tu travailles', 0, '2025-05-19 09:40:40'),
(8, 2, 1, 1, '2025-05-19 13:44:52', '2025-05-09 00:00:00', '2025-05-16 00:00:00', 6, NULL, NULL, NULL, NULL, NULL),
(9, 2, 2, 1, '2025-05-19 13:47:59', '2025-05-01 00:00:00', '2025-05-18 00:00:00', 10, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `request_type`
--

CREATE TABLE `request_type` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `request_type`
--

INSERT INTO `request_type` (`id`, `name`) VALUES
(2, 'Congé maladie'),
(3, 'Congé sans solde');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `role` varchar(50) NOT NULL,
  `person_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `enabled`, `created_at`, `role`, `person_id`) VALUES
(1, 'salesse@gmail.com', '$2y$10$Bzqo2lN/RZPhE76KHTvQE.Ut36qi/i8Cp3p.cvPvGDNs5G3N6l316', 1, '2025-04-28 15:17:46', 'manager', 1),
(2, 'martins@gmail.com', '$2y$10$88YuTaUP.JxJ/VK69uOqxOtOc58wkA4L4IqxZ6w0Fs9ecG6i5szBO', 1, '2025-04-28 15:18:23', 'collaborateur', 2),
(3, 'Dupas@gmail.com', '$2y$10$RYt.U4GnynK9rYDkdgBhFO0ZR.K3csU78mYJFUsmoMVf5i1dcH/FG', 1, '2025-04-28 15:19:15', 'collaborateur', 3),
(6, 'diego@gmail.com', '$2y$10$b63Cab.eLN0GkjJaX9ijJurjCYq244UQinA4ctkkK57PzHtOqssDC', 1, '0000-00-00 00:00:00', 'collaborateur', 5),
(9, 'Turcey@gmail.com', '$2y$10$0pfBz7fNWCdtLF.Tp6LAnu5uikstM6Bkk3HhUEs3ghWTZYs1/tqJm', 1, '0000-00-00 00:00:00', '', 8),
(10, 'daniel@gmail.com', '$2y$10$MqvKc0Hns0zFlgT9IijvD.szc3RNisTD8Z8xwx1aAHVfqzs9vKNCO', 1, '0000-00-00 00:00:00', 'manager', 9),
(11, 'ammar@gmail.com', '$2y$10$P4o6waWYZTNTnnNuw4MnB.HKxSHMY2fK9P1fArI4NhdUwvvOeUT0e', 1, '0000-00-00 00:00:00', 'manager', 10),
(12, 'valenti@gmail.com', '$2y$10$ry/93slJ8weNR5HYH67BMOgp7udKjE1k2duuyNxebtJ4HPmGURFn2', 1, '0000-00-00 00:00:00', 'collaborateur', 11),
(16, 'idasiak@gmail.com', '$2y$10$yNKvWjeBgP3rCIq3RmWrd.w/Qde1SyIwffTkX3G7syl67DRDDv8Ju', 1, '0000-00-00 00:00:00', 'collaborateur', 16);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_person_manager` (`manager_id`),
  ADD KEY `fk_person_department` (`department_id`),
  ADD KEY `fk_person_position` (`position_id`);

--
-- Index pour la table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_request_type` (`request_type_id`),
  ADD KEY `fk_request_collaborator` (`collaborator_id`),
  ADD KEY `fk_request_manager` (`department_id`);

--
-- Index pour la table `request_type`
--
ALTER TABLE `request_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `fk_user_person` (`person_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `person`
--
ALTER TABLE `person`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `request_type`
--
ALTER TABLE `request_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `fk_person_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_person_manager` FOREIGN KEY (`manager_id`) REFERENCES `person` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_person_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `fk_request_collaborator` FOREIGN KEY (`collaborator_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_request_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_request_type` FOREIGN KEY (`request_type_id`) REFERENCES `request_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_person` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
