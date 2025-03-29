-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 29, 2025 at 11:17 AM
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
  `batchdays` varchar(10) NOT NULL,
  `batchinstructor` int DEFAULT NULL,
  `currentsem` varchar(50) NOT NULL,
  `batchtype` varchar(200) DEFAULT NULL,
  `batchstartdate` date DEFAULT NULL,
  `batchstatus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`batchid`),
  KEY `batchinstructor` (`batchinstructor`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`batchid`, `batchcode`, `batchtimings`, `batchdays`, `batchinstructor`, `currentsem`, `batchtype`, `batchstartdate`, `batchstatus`) VALUES
(4, '2407A', '9-11', 'T.T.S', 7, 'CPISM', 'ACCP', '2025-03-11', 'Not Active');

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
  `dateofjoining` date NOT NULL,
  `dateofresignation` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`staffid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffid`, `staffname`, `staffemail`, `staffpassword`, `staffdesignation`, `stafftimings`, `staffphone`, `dateofjoining`, `dateofresignation`, `status`) VALUES
(4, 'Hassan', 'hassan@aptechgdn.net', '12345', 'Center Academic Head', 'Full Time', '03211267223', '2025-03-04', NULL, 'active'),
(6, 'Muneeb', 'muneeb_hasham@hotmail.com', '12345', 'Center Manager', 'Full Time', '03211267223', '2025-03-20', NULL, 'not active'),
(7, 'Hassan', 'hassan@aptechgdn.net', '12345', 'Faculty', 'Part Time', '03211267223', '2025-03-15', NULL, 'active'),
(8, 'Hassan', 'hassan@aptechgdn.net', '12345', 'Center Academic Head', 'Part Time', '03211267223', '2025-03-15', NULL, 'active'),
(9, 'Hassan', 'muneeb_hasham@hotmail.com', '12345', 'Center Manager', 'Full Time', '03211267223', '2025-03-12', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `studentid` int NOT NULL AUTO_INCREMENT,
  `studentname` varchar(100) DEFAULT NULL,
  `enrollmentno` varchar(1000) DEFAULT NULL,
  `studentemail` varchar(200) DEFAULT NULL,
  `studentpassword` varchar(200) DEFAULT NULL,
  `studentbatch` int DEFAULT NULL,
  `studentphoneno` varchar(40) DEFAULT NULL,
  `studentguardianphoneno` varchar(40) DEFAULT NULL,
  `studentstatus` varchar(10) NOT NULL,
  PRIMARY KEY (`studentid`),
  KEY `studentbatch` (`studentbatch`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentid`, `studentname`, `enrollmentno`, `studentemail`, `studentpassword`, `studentbatch`, `studentphoneno`, `studentguardianphoneno`, `studentstatus`) VALUES
(1, 'Rehman', '0', 'rehmansarkar786@gmail.com', '202cb962ac59075b964b07152d234b70', 4, '03331267223', '03333817782', 'Active');

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
