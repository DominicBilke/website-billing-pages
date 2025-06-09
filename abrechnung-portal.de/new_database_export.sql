-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 07. Okt 2022 um 19:41
-- Server-Version: 10.5.16-MariaDB-1:10.5.16+maria~focal-log
-- PHP-Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `d03adf9b`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `id` int(128) NOT NULL,
  `benutzer` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `pswd` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `status` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`id`, `benutzer`, `pswd`, `status`) VALUES
(1, 'Dagmar', 'DB', 'firma'),
(2, 'Dominic', 'DB', 'admin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzerdaten`
--

CREATE TABLE `benutzerdaten` (
  `id` int(128) NOT NULL,
  `benutzer_id` int(128) NOT NULL,
  `vorname` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `nachname` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `strasse_nr` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `plz_ort` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `lohn` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `benutzerdaten`
--

INSERT INTO `benutzerdaten` (`id`, `benutzer_id`, `vorname`, `nachname`, `strasse_nr`, `plz_ort`, `lohn`) VALUES
(1, 2, 'Dominic', 'Bilke', 'Lugaer StraÃŸe 3d', '01259 Dresden', '0'),
(2, 1, 'Dagmar', 'Bilke', 'ThÃ¼rmsdorfer StraÃŸe 6', '01259 Dresden', '12');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tourdaten`
--

CREATE TABLE `tourdaten` (
  `id` int(128) NOT NULL,
  `tour` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `verteiler` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `datum` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `startzeit` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `dauer` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `pause` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `flyer` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `gebiet` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `arbeitszeit` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `monat` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `datei` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `benutzer_id` int(128) NOT NULL,
  `projekt` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `gpx` varchar(128) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `benutzerdaten`
--
ALTER TABLE `benutzerdaten`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tourdaten`
--
ALTER TABLE `tourdaten`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `benutzerdaten`
--
ALTER TABLE `benutzerdaten`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `tourdaten`
--
ALTER TABLE `tourdaten`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
