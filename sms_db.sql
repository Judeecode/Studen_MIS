-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2025 at 09:58 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`) VALUES
(6, 'admin', '0192023a7bbd73250516f069df18b500', 'System Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id_fk` int(11) NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  `semester_id_fk` int(11) NOT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `remarks` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id_fk`,`subject_id_fk`,`semester_id_fk`),
  KEY `subject_id_fk` (`subject_id_fk`),
  KEY `semester_id_fk` (`semester_id_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id_fk`, `subject_id_fk`, `semester_id_fk`, `grade`, `remarks`) VALUES
(1, 2, 1, 1, '92.00', NULL),
(2, 1, 1, 1, '91.00', NULL),
(3, 2, 2, 1, '91.00', NULL),
(4, 1, 2, 1, '87.00', NULL),
(5, 3, 4, 2, '92.00', NULL),
(6, 1, 5, 2, '87.00', NULL),
(7, 4, 1, 1, '80.00', NULL),
(8, 4, 2, 1, NULL, NULL),
(9, 4, 6, 1, NULL, NULL),
(10, 4, 4, 2, NULL, NULL),
(11, 4, 5, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE IF NOT EXISTS `semesters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `name`) VALUES
(1, 'First Semester 2024-2025'),
(2, 'Second Semester 2024-2025'),
(3, 'First Semester 2024-2025'),
(4, 'Second Semester 2024-2025');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `strand_track` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `password`, `full_name`, `grade_level`, `strand_track`) VALUES
(1, '166298006744', 'cc03e747a6afbbcbf8be7668acfebee5', 'Giselle R. Trinidad', '11', 'ABM'),
(2, '166200077600', '16d7a4fca7442dda3ad93c9a726597e4', 'Jazric Gideus', '11', 'ABM'),
(3, '166277600892', 'c06db68e819be6ec3d26c6038d8e8d1f', 'Jake Justine L. Globio', '12', 'EIM'),
(4, '122606140071', '47ec2dd791e31e2ef2076caf64ed9b3d', 'Elija Mae P. Pimentel', '11', 'STEM');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semester_id` (`semester_id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `title`, `semester_id`, `teacher_id`) VALUES
(1, 'Emp_101', 'Empowerment Technology', 1, 1),
(2, 'GenMath_101', 'General Mathematics', 1, 1),
(4, 'EIM_101', 'Electrical Installation Management', 2, 3),
(5, 'STAT_101', 'Statistics and Probabilities', 2, 1),
(6, 'SCI_101', 'Science', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `full_name`, `username`, `password`) VALUES
(1, 'Joel Jude A. Dalumias', 'Judee27', '3a443140a9be58a65ab8c0c3bb172a86'),
(3, 'Dionald B. Montes', 'Apperz123', '22847c697d4cee51f6acd700ffdd9bd3');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id_fk`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id_fk`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`semester_id_fk`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
