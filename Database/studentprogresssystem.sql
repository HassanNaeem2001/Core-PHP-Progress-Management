-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 10, 2025 at 12:39 PM
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
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `assignmentid` int NOT NULL AUTO_INCREMENT,
  `assignmentname` varchar(1000) DEFAULT NULL,
  `assignmentdescription` varchar(1000) DEFAULT NULL,
  `assignmentfile` varchar(1000) DEFAULT NULL,
  `assignmentdeadline` date DEFAULT NULL,
  `assignedto` int DEFAULT NULL,
  `marks` int NOT NULL,
  `uploadedby` int NOT NULL,
  PRIMARY KEY (`assignmentid`),
  KEY `assignedto` (`assignedto`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments_uploaded`
--

DROP TABLE IF EXISTS `assignments_uploaded`;
CREATE TABLE IF NOT EXISTS `assignments_uploaded` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uploaded_by` int DEFAULT NULL,
  `uploaded_file` varchar(255) DEFAULT NULL,
  `uploaded_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `uploading_for` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `uploading_for` (`uploading_for`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`batchid`, `batchcode`, `batchtimings`, `batchdays`, `batchinstructor`, `currentsem`, `batchtype`, `batchstartdate`, `batchstatus`) VALUES
(8, '2408A', '9-11', 'T.T.S', 7, 'CPISM', 'ACCP', '2025-03-10', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `bookid` int NOT NULL AUTO_INCREMENT,
  `bookname` varchar(1000) DEFAULT NULL,
  `bookfile` mediumtext,
  `booksem` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`bookid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`bookid`, `bookname`, `bookfile`, `booksem`) VALUES
(7, 'Programming with JS', '', 'DISM'),
(8, 'Programming with JS 2', 'VIII_eng_lang.pdf', 'DISM'),
(9, 'Database MYSQLi 2', 'All_Tools_to_Use_in_ACCP_AI_7144.pdf', 'DISM'),
(10, 'Programming with javascript', 'mohsinkhan.rar', 'CPISM');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
CREATE TABLE IF NOT EXISTS `exams` (
  `examsid` int NOT NULL AUTO_INCREMENT,
  `skillname` varchar(100) DEFAULT NULL,
  `examtype` enum('Modular','Practical','Prepratory','Other') DEFAULT NULL,
  `examdate` date DEFAULT NULL,
  `examofbatch` int DEFAULT NULL,
  PRIMARY KEY (`examsid`),
  KEY `examofbatch` (`examofbatch`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `jobid` int NOT NULL AUTO_INCREMENT,
  `jobtitle` varchar(50) DEFAULT NULL,
  `jobdescription` mediumtext,
  `applybefore` date DEFAULT NULL,
  PRIMARY KEY (`jobid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sent_reports`
--

DROP TABLE IF EXISTS `sent_reports`;
CREATE TABLE IF NOT EXISTS `sent_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `studentid` int NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `studentid` (`studentid`,`month`,`year`)
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
  `dateofjoining` date NOT NULL,
  `dateofresignation` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`staffid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffid`, `staffname`, `staffemail`, `staffpassword`, `staffdesignation`, `stafftimings`, `staffphone`, `dateofjoining`, `dateofresignation`, `status`) VALUES
(4, 'Hassan', 'hassan@aptechgdn.net', '12345', 'Center Academic Head', 'Full Time', '03331267223', '2025-03-04', NULL, 'active'),
(7, 'Hassan', 'hassan@aptechgdn.net', '123456', 'Faculty', 'Part Time', '03211267223', '2025-03-15', NULL, 'active'),
(9, 'Hassan', 'muneeb_hasham@hotmail.com', '12345', 'Center Manager', 'Full Time', '03211267223', '2025-03-12', NULL, 'active'),
(11, 'Tabinda', 'tabinda@gmail.com', '123', 'Counselor', 'Full Time', '03332110982', '2025-04-02', NULL, 'active');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentid`, `studentname`, `enrollmentno`, `studentemail`, `studentpassword`, `studentbatch`, `studentphoneno`, `studentguardianphoneno`, `studentstatus`) VALUES
(8, 'Waiz Jamals', 'Student123123', 'Student123123', 'd41d8cd98f00b204e9800998ecf8427e', 8, '03332110982', '03331267223', 'Active');

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
  `classes_conducted` int NOT NULL,
  `classes_held` int NOT NULL,
  `remarks` varchar(1000) NOT NULL,
  `dateofprogress` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`progressno`),
  KEY `studentid` (`studentid`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `studentprogress`
--

INSERT INTO `studentprogress` (`progressno`, `studentid`, `assignmentmarks`, `quizmarksinternal`, `practical`, `modular`, `classes_conducted`, `classes_held`, `remarks`, `dateofprogress`) VALUES
(25, 8, 40, 50, 20, 10, 12, 15, 'Satisfied', '2025-02-28 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `student_complaints`
--

DROP TABLE IF EXISTS `student_complaints`;
CREATE TABLE IF NOT EXISTS `student_complaints` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `batch` varchar(50) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `complaint_type` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `remarks` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_complaints`
--

INSERT INTO `student_complaints` (`id`, `student_name`, `batch`, `faculty`, `complaint_type`, `created_at`, `remarks`) VALUES
(1, 'Muhammad Maaz', '2407A', 'Sir Hassan', 'Administration', '2025-04-02 07:59:16', 'Net nahi chalta time per'),
(2, 'Muhammad Maaz', '2407A', 'Sir Hassan', 'Academic', '2025-04-02 08:00:25', 'kjasjaf'),
(3, 'Muhammad Maaz', '2408A', 'Sir Hassan', 'Administration', '2025-04-02 08:04:58', 'skdsdfj'),
(4, 'Muhammad Maaz', '2408A', 'Sir Hassan', 'Administration', '2025-04-02 08:07:53', 'skdsdfj'),
(5, 'Muhammad Maaz', '2407A', 'Hassan', 'Other', '2025-04-02 08:08:59', 'Hello 123'),
(6, 'Rehman', '2407A', 'Hassan', 'Academic', '2025-04-02 08:17:05', 'Nothing');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`assignedto`) REFERENCES `batches` (`batchid`);

--
-- Constraints for table `assignments_uploaded`
--
ALTER TABLE `assignments_uploaded`
  ADD CONSTRAINT `assignments_uploaded_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `student` (`studentid`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_uploaded_ibfk_2` FOREIGN KEY (`uploading_for`) REFERENCES `assignments` (`assignmentid`) ON DELETE CASCADE;

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`batchinstructor`) REFERENCES `staff` (`staffid`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`examofbatch`) REFERENCES `batches` (`batchid`);

--
-- Constraints for table `sent_reports`
--
ALTER TABLE `sent_reports`
  ADD CONSTRAINT `sent_reports_ibfk_1` FOREIGN KEY (`studentid`) REFERENCES `student` (`studentid`) ON DELETE CASCADE;

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
