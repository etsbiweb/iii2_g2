-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 06:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elearning`
--

-- --------------------------------------------------------

--
-- Table structure for table `cas`
--

CREATE TABLE `cas` (
  `cas_id` int(11) NOT NULL,
  `razred_id` int(11) DEFAULT NULL,
  `redni_broj` enum('1','2','3','4','5','6','7') DEFAULT NULL,
  `smjena` enum('1','2') DEFAULT NULL,
  `profesor_predmet_id` int(11) NOT NULL,
  `dan` enum('Monday','Tuesday','Wednesday','Thursday','Friday') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izostanci`
--

CREATE TABLE `izostanci` (
  `izostanak_id` int(11) NOT NULL,
  `ucenik_id` int(11) DEFAULT NULL,
  `status_izostanka` enum('Opravdan','Neopravdan') DEFAULT NULL,
  `vrijeme` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ime_predmeta` varchar(75) DEFAULT NULL,
  `redni_broj_casa` enum('1','2','3','4','5','6','7') DEFAULT NULL,
  `ime_profesora` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_id` int(11) NOT NULL,
  `log_text` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `predmet`
--

CREATE TABLE `predmet` (
  `predmet_id` int(11) NOT NULL,
  `ime_predmeta` varchar(75) DEFAULT NULL,
  `boja` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profesor`
--

CREATE TABLE `profesor` (
  `profesor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ime_prezime` varchar(80) DEFAULT NULL,
  `razred_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profesor_predmet`
--

CREATE TABLE `profesor_predmet` (
  `profesor_id` int(11) DEFAULT NULL,
  `predmet_id` int(11) DEFAULT NULL,
  `profesor_predmet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `razred`
--

CREATE TABLE `razred` (
  `razred_id` int(11) NOT NULL,
  `godina` enum('I','II','III','IV') DEFAULT NULL,
  `odjeljene` enum('1','2','3','4','5') DEFAULT NULL,
  `smjena` enum('1','2') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `razred`
--

INSERT INTO `razred` (`razred_id`, `godina`, `odjeljene`, `smjena`) VALUES
(1, 'I', '1', '1'),
(2, 'I', '2', '1'),
(3, 'I', '3', '1'),
(4, 'I', '4', '1'),
(5, 'I', '5', '1'),
(6, 'II', '1', '2'),
(7, 'II', '2', '2'),
(8, 'II', '3', '2'),
(9, 'II', '4', '2'),
(10, 'II', '5', '2'),
(11, 'III', '1', '1'),
(12, 'III', '2', '1'),
(13, 'III', '3', '1'),
(14, 'III', '4', '1'),
(15, 'III', '5', '1'),
(16, 'IV', '1', '2'),
(17, 'IV', '2', '2'),
(18, 'IV', '3', '2'),
(19, 'IV', '4', '2');

-- --------------------------------------------------------

--
-- Table structure for table `ucenici`
--

CREATE TABLE `ucenici` (
  `ucenik_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jmbg` int(40) DEFAULT NULL,
  `razred_id` int(11) DEFAULT NULL,
  `opravdani` smallint(6) DEFAULT NULL,
  `neopravdani` smallint(6) DEFAULT NULL,
  `ime` varchar(30) DEFAULT NULL,
  `prezime` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `pristup` enum('ucenik','admin','profesor') DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `pristup`, `token`) VALUES
(1, 'sender.americanfood@gmail.com', '$2y$10$12TN0MSZK/yWhDf.sbXl/urTI0ZqctPLGhJpC6P4MBUwhgtxPOiZe', 'admin', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cas`
--
ALTER TABLE `cas`
  ADD PRIMARY KEY (`cas_id`),
  ADD KEY `razred_id` (`razred_id`),
  ADD KEY `cas_ibfk_2` (`profesor_predmet_id`);

--
-- Indexes for table `izostanci`
--
ALTER TABLE `izostanci`
  ADD PRIMARY KEY (`izostanak_id`),
  ADD KEY `ucenik_id` (`ucenik_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `predmet`
--
ALTER TABLE `predmet`
  ADD PRIMARY KEY (`predmet_id`);

--
-- Indexes for table `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`profesor_id`),
  ADD UNIQUE KEY `razred_id` (`razred_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profesor_predmet`
--
ALTER TABLE `profesor_predmet`
  ADD PRIMARY KEY (`profesor_predmet_id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `profesor_predmet_ibfk_2` (`predmet_id`);

--
-- Indexes for table `razred`
--
ALTER TABLE `razred`
  ADD PRIMARY KEY (`razred_id`);

--
-- Indexes for table `ucenici`
--
ALTER TABLE `ucenici`
  ADD PRIMARY KEY (`ucenik_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `razred_id` (`razred_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cas`
--
ALTER TABLE `cas`
  MODIFY `cas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `izostanci`
--
ALTER TABLE `izostanci`
  MODIFY `izostanak_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `predmet`
--
ALTER TABLE `predmet`
  MODIFY `predmet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `profesor`
--
ALTER TABLE `profesor`
  MODIFY `profesor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `profesor_predmet`
--
ALTER TABLE `profesor_predmet`
  MODIFY `profesor_predmet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `razred`
--
ALTER TABLE `razred`
  MODIFY `razred_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ucenici`
--
ALTER TABLE `ucenici`
  MODIFY `ucenik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cas`
--
ALTER TABLE `cas`
  ADD CONSTRAINT `cas_ibfk_1` FOREIGN KEY (`razred_id`) REFERENCES `razred` (`razred_id`),
  ADD CONSTRAINT `cas_ibfk_2` FOREIGN KEY (`profesor_predmet_id`) REFERENCES `profesor_predmet` (`profesor_predmet_id`);

--
-- Constraints for table `izostanci`
--
ALTER TABLE `izostanci`
  ADD CONSTRAINT `izostanci_ibfk_2` FOREIGN KEY (`ucenik_id`) REFERENCES `ucenici` (`ucenik_id`);

--
-- Constraints for table `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `fk_razred_id` FOREIGN KEY (`razred_id`) REFERENCES `razred` (`razred_id`),
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `profesor_predmet`
--
ALTER TABLE `profesor_predmet`
  ADD CONSTRAINT `profesor_predmet_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesor` (`profesor_id`),
  ADD CONSTRAINT `profesor_predmet_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmet` (`predmet_id`);

--
-- Constraints for table `ucenici`
--
ALTER TABLE `ucenici`
  ADD CONSTRAINT `ucenici_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ucenici_ibfk_2` FOREIGN KEY (`razred_id`) REFERENCES `razred` (`razred_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
