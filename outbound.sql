-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2024 at 01:30 PM
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
-- Database: `tech360inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `outbound`
--

CREATE TABLE `outbound` (
  `SN` int(3) NOT NULL,
  `item_id` varchar(6) NOT NULL,
  `item_description` varchar(50) NOT NULL,
  `item_quantity` varchar(100) NOT NULL,
  `unit_price` varchar(10000) NOT NULL,
  `date_shipped` date NOT NULL,
  `department` varchar(50) NOT NULL,
  `destination` varchar(50) NOT NULL,
  `total_price` varchar(100) NOT NULL,
  `remarks` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `outbound`
--
ALTER TABLE `outbound`
  ADD PRIMARY KEY (`SN`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `outbound`
--
ALTER TABLE `outbound`
  MODIFY `SN` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
