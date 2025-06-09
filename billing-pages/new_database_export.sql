-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 07, 2022 at 19:41
-- Server version: 10.5.16-MariaDB-1:10.5.16+maria~focal-log
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `billing_pages`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(128) NOT NULL,
  `user` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `pswd` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `status` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Data for table `users`
--

INSERT INTO `users` (`id`, `user`, `pswd`, `status`) VALUES
(1, 'Admin', 'admin', 'admin'),
(2, 'User', 'user', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE `user_data` (
  `id` int(128) NOT NULL,
  `user_id` int(128) NOT NULL,
  `firstname` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `lastname` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `street_number` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `postal_city` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `salary` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Data for table `user_data`
--

INSERT INTO `user_data` (`id`, `user_id`, `firstname`, `lastname`, `street_number`, `postal_city`, `salary`) VALUES
(1, 2, 'John', 'Doe', '123 Main St', '10001 New York', '0'),
(2, 1, 'Jane', 'Smith', '456 Oak Ave', '20001 Washington', '12');

-- --------------------------------------------------------

--
-- Table structure for table `tour_data`
--

CREATE TABLE `tour_data` (
  `id` int(128) NOT NULL,
  `tour` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `distributor` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `date` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `start_time` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `duration` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `break` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `flyer` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `area` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `work_time` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `month` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `file` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `user_id` int(128) NOT NULL,
  `project` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `gpx` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tour_data`
--
ALTER TABLE `tour_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_data`
--
ALTER TABLE `user_data`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tour_data`
--
ALTER TABLE `tour_data`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; 