-- MASTER SQL FILE FOR THE PROJECT
-- This file contains all table structures and sample data for the doctor appointment system.
-- Import this file into your MySQL database to set up the entire schema and demo data.

-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 06:06 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edoc_db`
--

CREATE DATABASE IF NOT EXISTS `edoc_db`;
USE `edoc_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `aemail` (`aemail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `aemail`, `apassword`) VALUES
(1, 'admin@edoc.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE IF NOT EXISTS `specialties` (
  `id` int(2) NOT NULL,
  `sname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `sname`) VALUES
(1, 'Accident and emergency medicine'),
(2, 'Allergology'),
(3, 'Anaesthetics'),
(4, 'Biological hematology'),
(5, 'Cardiology'),
(6, 'Child psychiatry'),
(7, 'Clinical biology'),
(8, 'Clinical chemistry'),
(9, 'Clinical neurophysiology'),
(10, 'Clinical radiology'),
(11, 'Dental, oral and maxillo-facial surgery'),
(12, 'Dermato-venerology'),
(13, 'Dermatology'),
(14, 'Endocrinology'),
(15, 'Gastro-enterologic surgery'),
(16, 'Gastroenterology'),
(17, 'General hematology'),
(18, 'General Practice'),
(19, 'General surgery'),
(20, 'Geriatrics'),
(21, 'Immunology'),
(22, 'Infectious diseases'),
(23, 'Internal medicine'),
(24, 'Laboratory medicine'),
(25, 'Maxillo-facial surgery'),
(26, 'Microbiology'),
(27, 'Nephrology'),
(28, 'Neuro-psychiatry'),
(29, 'Neurology'),
(30, 'Neurosurgery'),
(31, 'Nuclear medicine'),
(32, 'Obstetrics and gynecology'),
(33, 'Occupational medicine'),
(34, 'Ophthalmology'),
(35, 'Orthopaedics'),
(36, 'Otorhinolaryngology'),
(37, 'Paediatric surgery'),
(38, 'Paediatrics'),
(39, 'Pathology'),
(40, 'Pharmacology'),
(41, 'Physical medicine and rehabilitation'),
(42, 'Plastic surgery'),
(43, 'Podiatric Medicine'),
(44, 'Podiatric Surgery'),
(45, 'Psychiatry'),
(46, 'Public health and Preventive Medicine'),
(47, 'Radiology'),
(48, 'Radiotherapy'),
(49, 'Respiratory medicine'),
(50, 'Rheumatology'),
(51, 'Stomatology'),
(52, 'Thoracic surgery'),
(53, 'Tropical medicine'),
(54, 'Urology'),
(55, 'Vascular surgery'),
(56, 'Venereology');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE IF NOT EXISTS `patient` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) DEFAULT NULL,
  `paddress` varchar(255) DEFAULT NULL,
  `pnic` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `ptel` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`pid`, `pemail`, `fullname`, `ppassword`, `paddress`, `pnic`, `pdob`, `ptel`) VALUES
(1, 'patient@edoc.com', 'Test Patient', '123', 'Sri Lanka', '0000000000', '2000-01-01', '0120000000'),
(2, 'john.doe@email.com', 'John Doe', '123', '123 Main St', '1234567890', '1985-05-15', '555-0123'),
(3, 'jane.smith@email.com', 'Jane Smith', '123', '456 Oak Ave', '0987654321', '1990-08-20', '555-0124'),
(4, 'mike.johnson@email.com', 'Mike Johnson', '123', '789 Pine Rd', '1122334455', '1978-03-10', '555-0125'),
(5, 'sarah.wilson@email.com', 'Sarah Wilson', '123', '321 Elm St', '5544332211', '1995-11-25', '555-0126'),
(6, 'david.brown@email.com', 'David Brown', '123', '654 Maple Dr', '6677889900', '1982-07-30', '555-0127'),
(7, 'emma.davis@email.com', 'Emma Davis', '123', '987 Cedar Ln', '9988776655', '1992-04-05', '555-0128'),
(8, 'james.miller@email.com', 'James Miller', '123', '147 Birch Way', '4433221100', '1988-09-15', '555-0129');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE IF NOT EXISTS `doctor` (
  `docid` int(11) NOT NULL AUTO_INCREMENT,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL,
  PRIMARY KEY (`docid`),
  KEY `specialties` (`specialties`),
  CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`specialties`) REFERENCES `specialties` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(1, 'dr.smith@edoc.com', 'Dr. John Smith', '123', 'DOC001', '555-0001', 5),
(2, 'dr.johnson@edoc.com', 'Dr. Sarah Johnson', '123', 'DOC002', '555-0002', 13),
(3, 'dr.williams@edoc.com', 'Dr. Michael Williams', '123', 'DOC003', '555-0003', 29),
(4, 'dr.brown@edoc.com', 'Dr. Emily Brown', '123', 'DOC004', '555-0004', 16),
(5, 'dr.davis@edoc.com', 'Dr. Robert Davis', '123', 'DOC005', '555-0005', 35),
(6, 'dr.miller@edoc.com', 'Dr. Lisa Miller', '123', 'DOC006', '555-0006', 45),
(7, 'dr.wilson@edoc.com', 'Dr. David Wilson', '123', 'DOC007', '555-0007', 23),
(8, 'dr.moore@edoc.com', 'Dr. Jennifer Moore', '123', 'DOC008', '555-0008', 32);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `scheduleid` int(11) NOT NULL AUTO_INCREMENT,
  `docid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `nop` int(4) DEFAULT NULL,
  PRIMARY KEY (`scheduleid`),
  KEY `docid` (`docid`),
  CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`docid`) REFERENCES `doctor` (`docid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `scheduletime`, `nop`) VALUES
(1, 1, 'Morning Consultation', '2025-06-15', '09:00:00', 10),
(2, 1, 'Afternoon Consultation', '2025-06-15', '14:00:00', 10),
(3, 2, 'Dermatology Clinic', '2025-06-15', '10:00:00', 8),
(4, 3, 'Neurology Session', '2025-06-15', '11:00:00', 6),
(5, 4, 'Gastroenterology Clinic', '2025-06-15', '13:00:00', 8),
(6, 5, 'Orthopedics Consultation', '2025-06-15', '15:00:00', 10),
(7, 6, 'Psychiatry Session', '2025-06-15', '16:00:00', 6),
(8, 7, 'Internal Medicine Clinic', '2025-06-15', '09:30:00', 8),
(9, 8, 'Obstetrics Clinic', '2025-06-15', '10:30:00', 6);

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE IF NOT EXISTS `appointment` (
  `appoid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `apponum` int(3) DEFAULT NULL,
  `scheduleid` int(11) DEFAULT NULL,
  `appodate` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`),
  CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `patient` (`pid`),
  CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`scheduleid`) REFERENCES `schedule` (`scheduleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`) VALUES
(1, 1, 1, 1, '2025-06-15'),
(2, 2, 2, 1, '2025-06-15'),
(3, 3, 1, 3, '2025-06-15'),
(4, 4, 1, 4, '2025-06-15'),
(5, 5, 1, 5, '2025-06-15'),
(6, 6, 1, 6, '2025-06-15'),
(7, 7, 1, 7, '2025-06-15'),
(8, 8, 1, 8, '2025-06-15');

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE IF NOT EXISTS `time_slots` (
  `slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `time_slot` time NOT NULL,
  `is_booked` tinyint(1) DEFAULT '0',
  `appointment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`slot_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `time_slots_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`scheduleid`),
  CONSTRAINT `time_slots_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`slot_id`, `schedule_id`, `time_slot`, `is_booked`, `appointment_id`) VALUES
-- Schedule 1 (Dr. Smith - Morning Consultation)
(1, 1, '09:00:00', 0, NULL),
(2, 1, '09:15:00', 0, NULL),
(3, 1, '09:30:00', 0, NULL),
(4, 1, '09:45:00', 0, NULL),

-- Schedule 2 (Dr. Smith - Afternoon Consultation)
(5, 2, '14:00:00', 0, NULL),
(6, 2, '14:15:00', 0, NULL),
(7, 2, '14:30:00', 0, NULL),
(8, 2, '14:45:00', 0, NULL),

-- Schedule 3 (Dr. Johnson - Dermatology Clinic)
(9, 3, '10:00:00', 0, NULL),
(10, 3, '10:15:00', 0, NULL),
(11, 3, '10:30:00', 0, NULL),
(12, 3, '10:45:00', 0, NULL),

-- Schedule 4 (Dr. Williams - Neurology Session)
(13, 4, '11:00:00', 0, NULL),
(14, 4, '11:15:00', 0, NULL),
(15, 4, '11:30:00', 0, NULL),
(16, 4, '11:45:00', 0, NULL),

-- Schedule 5 (Dr. Brown - Gastroenterology Clinic)
(17, 5, '13:00:00', 0, NULL),
(18, 5, '13:15:00', 0, NULL),
(19, 5, '13:30:00', 0, NULL),
(20, 5, '13:45:00', 0, NULL),

-- Schedule 6 (Dr. Davis - Orthopedics Consultation)
(21, 6, '15:00:00', 0, NULL),
(22, 6, '15:15:00', 0, NULL),
(23, 6, '15:30:00', 0, NULL),
(24, 6, '15:45:00', 0, NULL),

-- Schedule 7 (Dr. Miller - Psychiatry Session)
(25, 7, '16:00:00', 0, NULL),
(26, 7, '16:15:00', 0, NULL),
(27, 7, '16:30:00', 0, NULL),
(28, 7, '16:45:00', 0, NULL),

-- Schedule 8 (Dr. Wilson - Internal Medicine Clinic)
(29, 8, '09:30:00', 0, NULL),
(30, 8, '09:45:00', 0, NULL),
(31, 8, '10:00:00', 0, NULL),
(32, 8, '10:15:00', 0, NULL),

-- Schedule 9 (Dr. Moore - Obstetrics Clinic)
(33, 9, '10:30:00', 0, NULL),
(34, 9, '10:45:00', 0, NULL),
(35, 9, '11:00:00', 0, NULL),
(36, 9, '11:15:00', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE IF NOT EXISTS `diseases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `symptoms` text,
  `specialty_id` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `specialty_id` (`specialty_id`),
  CONSTRAINT `diseases_ibfk_1` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `diseases`
--

INSERT INTO `diseases` (`id`, `name`, `symptoms`, `specialty_id`) VALUES
(1, 'Common Cold', 'Runny nose, Sore throat, Cough, Fatigue', 18),
(2, 'Influenza', 'Fever, Cough, Fatigue, Muscle ache, Chills', 18),
(3, 'COVID-19', 'Fever, Cough, Shortness of breath, Loss of taste/smell', 18),
(4, 'Hypertension', 'Headache, Fatigue, Shortness of breath', 5),
(5, 'Diabetes', 'Frequent urination, Excessive thirst, Fatigue', 14),
(6, 'Asthma', 'Shortness of breath, Wheezing, Chest tightness', 49),
(7, 'Migraine', 'Severe headache, Nausea, Sensitivity to light', 29),
(8, 'Arthritis', 'Joint pain, Stiffness, Swelling', 50),
(9, 'Depression', 'Sadness, Loss of interest, Fatigue', 45),
(10, 'Anxiety', 'Worry, Restlessness, Rapid heartbeat', 45);

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE IF NOT EXISTS `recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disease_id` int(11) DEFAULT NULL,
  `recommendation` text,
  PRIMARY KEY (`id`),
  KEY `disease_id` (`disease_id`),
  CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`id`, `disease_id`, `recommendation`) VALUES
(1, 1, 'Rest, stay hydrated, take over-the-counter cold medicine'),
(2, 2, 'Rest, take antiviral medication if prescribed, stay hydrated'),
(3, 3, 'Isolate, rest, monitor symptoms, seek medical attention if severe'),
(4, 4, 'Regular exercise, healthy diet, medication as prescribed'),
(5, 5, 'Monitor blood sugar, healthy diet, regular exercise'),
(6, 6, 'Avoid triggers, use inhaler as prescribed, regular check-ups'),
(7, 7, 'Rest in dark room, take prescribed medication, avoid triggers'),
(8, 8, 'Regular exercise, maintain healthy weight, take prescribed medication'),
(9, 9, 'Therapy, medication if prescribed, regular exercise'),
(10, 10, 'Therapy, relaxation techniques, medication if prescribed');

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE IF NOT EXISTS `symptoms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`id`, `name`) VALUES
(1, 'Fever'),
(2, 'Cough'),
(3, 'Headache'),
(4, 'Fatigue'),
(5, 'Sore throat'),
(6, 'Runny nose'),
(7, 'Shortness of breath'),
(8, 'Nausea'),
(9, 'Diarrhea'),
(10, 'Chest pain'),
(11, 'Dizziness'),
(12, 'Muscle ache'),
(13, 'Skin rash'),
(14, 'Loss of taste'),
(15, 'Loss of smell'),
(16, 'Abdominal pain'),
(17, 'Back pain'),
(18, 'Chills'),
(19, 'Confusion'),
(20, 'Constipation'),
(21, 'Dry mouth'),
(22, 'Excessive sweating'),
(23, 'Frequent urination'),
(24, 'Heart palpitations'),
(25, 'High blood pressure'),
(26, 'Itchy eyes'),
(27, 'Joint pain'),
(28, 'Nasal congestion'),
(29, 'Pale skin'),
(30, 'Swollen lymph nodes'),
(31, 'Vomiting'),
(32, 'Weight loss'),
(33, 'Blurred vision'),
(34, 'Cold hands or feet'),
(35, 'Difficulty swallowing'),
(36, 'Hair loss'),
(37, 'Hearing loss'),
(38, 'Mood swings'),
(39, 'Night sweats'),
(40, 'Rapid breathing'),
(41, 'Sensitivity to light'),
(42, 'Sleep disturbances'),
(43, 'Swelling in legs or feet'),
(44, 'Tingling sensation'),
(45, 'Unexplained bruising');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_ratings`
--

CREATE TABLE IF NOT EXISTS `doctor_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `review` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rating_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `doctor_ratings_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`docid`),
  CONSTRAINT `doctor_ratings_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctor_ratings`
--

INSERT INTO `doctor_ratings` (`rating_id`, `doctor_id`, `patient_id`, `rating`, `review`, `created_at`) VALUES
(1, 1, 1, 5, 'Excellent doctor, very knowledgeable and caring', '2024-02-01 10:00:00'),
(2, 2, 2, 4, 'Good consultation, explained everything clearly', '2024-02-02 11:00:00'),
(3, 3, 3, 5, 'Very professional and thorough', '2024-02-03 12:00:00'),
(4, 4, 4, 4, 'Great bedside manner, made me feel comfortable', '2024-02-04 13:00:00'),
(5, 5, 5, 5, 'Excellent treatment and follow-up care', '2024-02-05 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `medical_reports`
--

CREATE TABLE IF NOT EXISTS `medical_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `prescription` text NOT NULL,
  `notes` text,
  `next_appointment_date` date DEFAULT NULL,
  `next_appointment_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `medical_reports_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appoid`),
  CONSTRAINT `medical_reports_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`docid`),
  CONSTRAINT `medical_reports_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `medical_reports`
--

INSERT INTO `medical_reports` (`report_id`, `appointment_id`, `doctor_id`, `patient_id`, `diagnosis`, `prescription`, `notes`, `next_appointment_date`, `next_appointment_time`, `created_at`) VALUES
(1, 1, 1, 1, 'Hypertension', 'Lisinopril 10mg daily', 'Monitor blood pressure regularly', '2025-07-15', '09:00:00', '2025-06-15 09:30:00'),
(2, 2, 1, 2, 'Type 2 Diabetes', 'Metformin 500mg twice daily', 'Follow diabetic diet, exercise regularly', '2025-07-15', '09:30:00', '2025-06-15 10:00:00'),
(3, 3, 2, 3, 'Eczema', 'Topical steroid cream', 'Avoid triggers, use moisturizer', '2025-07-15', '10:00:00', '2025-06-15 10:30:00'),
(4, 4, 3, 4, 'Migraine', 'Sumatriptan 50mg as needed', 'Keep headache diary', '2025-07-15', '11:00:00', '2025-06-15 11:30:00'),
(5, 5, 4, 5, 'GERD', 'Omeprazole 20mg daily', 'Avoid spicy foods, eat smaller meals', '2025-07-15', '13:00:00', '2025-06-15 13:30:00'),
(6, 6, 5, 6, 'Lower Back Pain', 'Ibuprofen 400mg three times daily', 'Physical therapy recommended', '2025-07-20', '15:00:00', '2025-06-15 15:30:00'),
(7, 7, 6, 7, 'Anxiety Disorder', 'Sertraline 50mg daily', 'Regular counseling sessions advised', '2025-07-25', '16:00:00', '2025-06-15 16:30:00'),
(8, 8, 7, 8, 'Bronchitis', 'Amoxicillin 500mg three times daily', 'Rest and plenty of fluids', '2025-07-18', '09:30:00', '2025-06-15 17:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('patient','doctor','admin') NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_type`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 'patient', 1, 'Appointment Confirmed', 'Your appointment with Dr. Smith has been confirmed for Jun 15, 2025', 0, '2025-06-14 10:00:00'),
(2, 'doctor', 1, 'New Appointment', 'New appointment scheduled with John Doe', 0, '2025-06-14 10:05:00'),
(3, 'admin', 1, 'System Update', 'System maintenance scheduled for Jun 20, 2025', 0, '2025-06-14 10:10:00'),
(4, 'patient', 2, 'Appointment Reminder', 'Reminder: Your appointment is tomorrow at 9:00 AM', 0, '2025-06-14 10:15:00'),
(5, 'doctor', 2, 'Patient Report', 'New medical report submitted for Jane Smith', 0, '2025-06-14 10:20:00'),
(6, 'patient', 3, 'Follow-up Appointment', 'Your follow-up appointment has been scheduled for July 15, 2025', 0, '2025-06-14 10:25:00'),
(7, 'doctor', 3, 'Patient Feedback', 'New rating received from Mike Johnson', 0, '2025-06-14 10:30:00'),
(8, 'admin', 1, 'New Doctor Registration', 'New doctor registration pending approval', 0, '2025-06-14 10:35:00'),
(9, 'patient', 4, 'Prescription Ready', 'Your prescription is ready for collection', 0, '2025-06-14 10:40:00'),
(10, 'doctor', 4, 'Schedule Update', 'Your schedule has been updated for next week', 0, '2025-06-14 10:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `webuser`
--

CREATE TABLE IF NOT EXISTS `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`) VALUES
('admin@edoc.com', 'a'),
('dr.smith@edoc.com', 'd'),
('dr.johnson@edoc.com', 'd'),
('dr.williams@edoc.com', 'd'),
('dr.brown@edoc.com', 'd'),
('dr.davis@edoc.com', 'd'),
('dr.miller@edoc.com', 'd'),
('dr.wilson@edoc.com', 'd'),
('dr.moore@edoc.com', 'd'),
('patient@edoc.com', 'p'),
('john.doe@email.com', 'p'),
('jane.smith@email.com', 'p'),
('mike.johnson@email.com', 'p'),
('sarah.wilson@email.com', 'p'),
('david.brown@email.com', 'p'),
('emma.davis@email.com', 'p'),
('james.miller@email.com', 'p');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;