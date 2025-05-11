-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 11, 2025 at 09:33 PM
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
  `predmet_id` int(11) DEFAULT NULL,
  `profesor_id` int(11) DEFAULT NULL,
  `vrijeme_pocetka` time DEFAULT NULL,
  `vrijeme_zavrsetka` time DEFAULT NULL,
  `redni_broj` enum('1','2','3','4','5','6','7') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izostanci`
--

CREATE TABLE `izostanci` (
  `izostanak_id` int(11) NOT NULL,
  `cas_id` int(11) DEFAULT NULL,
  `ucenik_id` int(11) DEFAULT NULL,
  `status_izostanka` enum('Opravdan','Neopravdan') DEFAULT NULL,
  `vrijeme` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `izostanci`
--

INSERT INTO `izostanci` (`izostanak_id`, `cas_id`, `ucenik_id`, `status_izostanka`, `vrijeme`) VALUES
(1, NULL, 1, 'Opravdan', '2025-05-10 10:07:57');

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
  `broj_modula` smallint(6) DEFAULT NULL,
  `ime_predmeta` varchar(75) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profesor`
--

CREATE TABLE `profesor` (
  `profesor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ime_prezime` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profesor_predmet`
--

CREATE TABLE `profesor_predmet` (
  `profesor_id` int(11) DEFAULT NULL,
  `predmet_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `razred`
--

CREATE TABLE `razred` (
  `razred_id` int(11) NOT NULL,
  `godina` enum('I','II','III','IV') DEFAULT NULL,
  `odjeljene` enum('1','2','3','4','5') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `razred`
--

INSERT INTO `razred` (`razred_id`, `godina`, `odjeljene`) VALUES
(1, 'I', '1'),
(2, 'I', '2'),
(3, 'I', '3'),
(4, 'I', '4'),
(5, 'I', '5'),
(6, 'II', '1'),
(7, 'II', '2'),
(8, 'II', '3'),
(9, 'II', '4'),
(10, 'II', '5'),
(11, 'III', '1'),
(12, 'III', '2'),
(13, 'III', '3'),
(14, 'III', '4'),
(15, 'III', '5'),
(16, 'IV', '1'),
(17, 'IV', '2'),
(18, 'IV', '3'),
(19, 'IV', '4');

-- --------------------------------------------------------

--
-- Table structure for table `ucenici`
--

CREATE TABLE `ucenici` (
  `ucenik_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ime_prezime` varchar(100) DEFAULT NULL,
  `jmbg` int(40) DEFAULT NULL,
  `razred_id` int(11) DEFAULT NULL,
  `opravdani` smallint(6) DEFAULT NULL,
  `neopravdani` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ucenici`
--

INSERT INTO `ucenici` (`ucenik_id`, `user_id`, `ime_prezime`, `jmbg`, `razred_id`, `opravdani`, `neopravdani`) VALUES
(1, 2, 'Teo Svilenski', 123123, 1, 1, 1);

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
(1, 'sender.americanfood@gmail.com', '$2y$10$12TN0MSZK/yWhDf.sbXl/urTI0ZqctPLGhJpC6P4MBUwhgtxPOiZe', 'admin', NULL),
(2, 'mikajlo367@gmail.com', '$2y$10$Vm1xvPKFBD/fNiHueEH8sOFxX9geYKFN/IzedM/N/RfdXf.frZAFe', 'ucenik', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cas`
--
ALTER TABLE `cas`
  ADD PRIMARY KEY (`cas_id`),
  ADD KEY `razred_id` (`razred_id`),
  ADD KEY `predmet_id` (`predmet_id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indexes for table `izostanci`
--
ALTER TABLE `izostanci`
  ADD PRIMARY KEY (`izostanak_id`),
  ADD KEY `cas_id` (`cas_id`),
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
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profesor_predmet`
--
ALTER TABLE `profesor_predmet`
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `predmet_id` (`predmet_id`);

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
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cas`
--
ALTER TABLE `cas`
  MODIFY `cas_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `izostanci`
--
ALTER TABLE `izostanci`
  MODIFY `izostanak_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `predmet`
--
ALTER TABLE `predmet`
  MODIFY `predmet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `profesor`
--
ALTER TABLE `profesor`
  MODIFY `profesor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `razred`
--
ALTER TABLE `razred`
  MODIFY `razred_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ucenici`
--
ALTER TABLE `ucenici`
  MODIFY `ucenik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cas`
--
ALTER TABLE `cas`
  ADD CONSTRAINT `cas_ibfk_1` FOREIGN KEY (`razred_id`) REFERENCES `razred` (`razred_id`),
  ADD CONSTRAINT `cas_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmet` (`predmet_id`),
  ADD CONSTRAINT `cas_ibfk_3` FOREIGN KEY (`profesor_id`) REFERENCES `profesor` (`profesor_id`);

--
-- Constraints for table `izostanci`
--
ALTER TABLE `izostanci`
  ADD CONSTRAINT `izostanci_ibfk_1` FOREIGN KEY (`cas_id`) REFERENCES `cas` (`cas_id`),
  ADD CONSTRAINT `izostanci_ibfk_2` FOREIGN KEY (`ucenik_id`) REFERENCES `ucenici` (`ucenik_id`);

--
-- Constraints for table `profesor`
--
ALTER TABLE `profesor`
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
