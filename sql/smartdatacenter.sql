-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 02:59 PM
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
-- Database: `smartdatacenter`
--
CREATE DATABASE IF NOT EXISTS `smartdatacenter` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `smartdatacenter`;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `ID` int(11) NOT NULL,
  `device_ID` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `dateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`ID`, `device_ID`, `data`, `dateTime`) VALUES
(1, 1, '{\n\"current\": 173, \"factor\": 85, \"power\": 35, \"power_sum\": 329992, \"state\": \"open\", \"voltage\": 239}', '2025-10-22 14:48:24'),
(2, 2, '{\r\n  \"Ext\": 1,\r\n  \"Hum_SHT\": 42.4,\r\n  \"Systimestamp\": 1761136134,\r\n  \"TempC_DS\": 327.67,\r\n  \"TempC_SHT\": 25.44\r\n}', '2025-10-22 14:29:16'),
(3, 1, '{\r\n  \"current\": 124,\r\n  \"factor\": 80,\r\n  \"power\": 24,\r\n  \"power_sum\": 330048,\r\n  \"state\": \"open\",\r\n  \"voltage\": 240.9\r\n}', '2025-10-22 14:47:21');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `ID` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`ID`, `name`) VALUES
(1, 'Milesight'),
(2, 'Dragino Technology Co., Limited');

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `ID` int(11) NOT NULL,
  `deviceID` varchar(100) NOT NULL,
  `type_ID` int(11) NOT NULL,
  `brand_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`ID`, `deviceID`, `type_ID`, `brand_ID`) VALUES
(1, 'powersocket-001', 1, 1),
(2, 'temphumidity-001', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `ID` int(11) NOT NULL,
  `device_ID` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `startTime` datetime NOT NULL,
  `endTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sensortype`
--

CREATE TABLE `sensortype` (
  `ID` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sensortype`
--

INSERT INTO `sensortype` (`ID`, `name`) VALUES
(1, 'power socket'),
(2, 'Temperature & Humidity Sensor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_device_id` (`device_ID`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `uk_device_id` (`deviceID`),
  ADD KEY `idx_type_id` (`type_ID`),
  ADD KEY `idx_brand_id` (`brand_ID`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_device_id` (`device_ID`);

--
-- Indexes for table `sensortype`
--
ALTER TABLE `sensortype`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sensortype`
--
ALTER TABLE `sensortype`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `fk_activity_device` FOREIGN KEY (`device_ID`) REFERENCES `device` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `fk_device_brand` FOREIGN KEY (`brand_ID`) REFERENCES `brand` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_device_sensortype` FOREIGN KEY (`type_ID`) REFERENCES `sensortype` (`ID`) ON UPDATE CASCADE;

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `fk_log_device` FOREIGN KEY (`device_ID`) REFERENCES `device` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
