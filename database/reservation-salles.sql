-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 10 juil. 2025 à 08:58
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
-- Base de données : `reservation-salles`
--

-- --------------------------------------------------------

--
-- Structure de la table `associations`
--

CREATE TABLE `associations` (
  `id_association` int(11) NOT NULL,
  `nom_association` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `associations`
--

INSERT INTO `associations` (`id_association`, `nom_association`) VALUES
(1, 'Les Rives du Cérou'),
(2, 'Le Cercle du Vieux Bourg'),
(3, 'La Clé des Remparts'),
(4, 'L\'Union du Pays Monestiéen'),
(5, 'Les Collines d\'Occitanie'),
(6, 'Les Voix du Tarn'),
(7, 'La Maison des Quatre Saisons'),
(8, 'Le Foyer de la Place Haute'),
(9, 'Esprit de Village'),
(10, 'Aucune Association');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id_comment` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `heure_comment` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id_reservation` int(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `pieces_jointe` text DEFAULT NULL,
  `salle_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `association_id` int(11) NOT NULL,
  `recurrent` tinyint(1) NOT NULL,
  `menageCheckbox` tinyint(1) NOT NULL,
  `Menage` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id_reservation`, `date_debut`, `date_fin`, `heure_debut`, `heure_fin`, `pieces_jointe`, `salle_id`, `utilisateur_id`, `association_id`, `recurrent`, `menageCheckbox`, `Menage`) VALUES
(119, '2025-07-11', '2025-07-11', '20:00:00', '22:00:00', NULL, 2, 2, 10, 1, 0, 0),
(120, '2025-07-25', '2025-07-25', '20:00:00', '22:00:00', NULL, 2, 2, 10, 1, 0, 0),
(121, '2025-08-08', '2025-08-08', '20:00:00', '22:00:00', NULL, 2, 2, 10, 1, 0, 0),
(122, '2025-07-10', '2025-07-10', '20:00:00', '22:00:00', NULL, 2, 2, 10, 1, 0, 0),
(123, '2025-07-24', '2025-07-24', '20:00:00', '22:00:00', NULL, 2, 2, 10, 1, 0, 0),
(124, '2025-08-07', '2025-08-07', '20:00:00', '22:00:00', NULL, 2, 2, 10, 1, 0, 0),
(126, '2025-07-30', '2025-07-30', '20:00:00', '22:00:00', NULL, 1, 2, 8, 1, 0, 0),
(127, '2025-08-13', '2025-08-13', '20:00:00', '22:00:00', NULL, 1, 2, 8, 1, 0, 0),
(128, '2025-07-11', '2025-07-11', '20:00:00', '22:00:00', NULL, 1, 2, 8, 0, 0, 0),
(129, '2025-07-17', '2025-07-17', '20:00:00', '22:00:00', NULL, 2, 2, 7, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

CREATE TABLE `salles` (
  `id_salle` int(11) NOT NULL,
  `nom_salle` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `salles`
--

INSERT INTO `salles` (`id_salle`, `nom_salle`) VALUES
(1, 'salle de réunion'),
(2, 'bar'),
(3, 'réfectoire');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` text NOT NULL,
  `prenom_utilisateur` text NOT NULL,
  `telephone` text NOT NULL,
  `email` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `profil` text NOT NULL,
  `association_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom_utilisateur`, `prenom_utilisateur`, `telephone`, `email`, `password`, `profil`, `association_id`) VALUES
(2, 'Admin', 'Mairie', '0618698068', 'mairie@yahoo.fr', 'admin', 'Gestionnaire', 10),
(3, 'prez', 'asso', '0613436135', 'prez@yahoo.fr', 'test', 'Président d\'association', 8),
(5, 'marley', 'bob', '0644596832', 'marley@yahoo.fr', '$2y$10$YZcMRaydh9W1v42eVAnZVe0uDQUcxnbshTxh2pJcKFjWEgnE.LUM6', 'Membre', 4),
(6, 'ferrari', 'lolo', '0643512563', 'ferrari@yahoo.fr', '$2y$10$G1r3f019KVgONi.UNiQczOj9inQH0oXiMdT//QmNoyV81XKiwK0GW', 'Ménage', 10),
(7, 'bricoleur', 'bob', '0752324586', 'bob@yahoo.fr', '$2y$10$JnCcI14eckmDtmW.zKlUG.RKmkht7cX8uisCIZx2mjeW53ykXEIce', 'Président d\'association', 5);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `associations`
--
ALTER TABLE `associations`
  ADD PRIMARY KEY (`id_association`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `salle_id` (`salle_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `association_id` (`association_id`);

--
-- Index pour la table `salles`
--
ALTER TABLE `salles`
  ADD PRIMARY KEY (`id_salle`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD KEY `association_id` (`association_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `associations`
--
ALTER TABLE `associations`
  MODIFY `id_association` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservation` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT pour la table `salles`
--
ALTER TABLE `salles`
  MODIFY `id_salle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id_reservation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`salle_id`) REFERENCES `salles` (`id_salle`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`association_id`) REFERENCES `associations` (`id_association`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `associations` (`id_association`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
