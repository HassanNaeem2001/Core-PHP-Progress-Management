-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 28, 2025 at 10:06 AM
-- Server version: 8.3.0
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studentprogresssystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `adminid` int NOT NULL AUTO_INCREMENT,
  `adminname` varchar(50) DEFAULT NULL,
  `adminpassword` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`adminid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminid`, `adminname`, `adminpassword`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

DROP TABLE IF EXISTS `batches`;
CREATE TABLE IF NOT EXISTS `batches` (
  `batchid` int NOT NULL AUTO_INCREMENT,
  `batchcode` varchar(100) DEFAULT NULL,
  `batchtimings` varchar(100) DEFAULT NULL,
  `batchinstructor` int DEFAULT NULL,
  `batchtype` varchar(200) DEFAULT NULL,
  `batchstartdate` date DEFAULT NULL,
  PRIMARY KEY (`batchid`),
  KEY `batchinstructor` (`batchinstructor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `staffid` int NOT NULL AUTO_INCREMENT,
  `staffname` varchar(100) DEFAULT NULL,
  `staffemail` varchar(1000) DEFAULT NULL,
  `staffpassword` varchar(1000) DEFAULT NULL,
  `staffdesignation` varchar(100) DEFAULT NULL,
  `stafftimings` varchar(50) DEFAULT NULL,
  `staffphone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`staffid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `studentid` int NOT NULL AUTO_INCREMENT,
  `studentname` varchar(100) DEFAULT NULL,
  `studentemail` varchar(200) DEFAULT NULL,
  `studentpassword` varchar(200) DEFAULT NULL,
  `studentbatch` int DEFAULT NULL,
  `studentphoneno` varchar(40) DEFAULT NULL,
  `studentguardianphoneno` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`studentid`),
  KEY `studentbatch` (`studentbatch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studentprogress`
--

DROP TABLE IF EXISTS `studentprogress`;
CREATE TABLE IF NOT EXISTS `studentprogress` (
  `progressno` int NOT NULL AUTO_INCREMENT,
  `studentid` int DEFAULT NULL,
  `assignmentmarks` bigint DEFAULT NULL,
  `quizmarksinternal` bigint DEFAULT NULL,
  `practical` bigint DEFAULT NULL,
  `modular` bigint DEFAULT NULL,
  `dateofprogress` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`progressno`),
  KEY `studentid` (`studentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`batchinstructor`) REFERENCES `staff` (`staffid`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`studentbatch`) REFERENCES `batches` (`batchid`);

--
-- Constraints for table `studentprogress`
--
ALTER TABLE `studentprogress`
  ADD CONSTRAINT `studentprogress_ibfk_1` FOREIGN KEY (`studentid`) REFERENCES `student` (`studentid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
