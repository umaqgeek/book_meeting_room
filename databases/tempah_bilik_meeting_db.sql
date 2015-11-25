-- phpMyAdmin SQL Dump
-- version 4.4.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 24, 2015 at 12:00 PM
-- Server version: 5.6.24
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tempah_bilik_meeting_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bilik_meeting`
--

CREATE TABLE IF NOT EXISTS `bilik_meeting` (
  `bm_id` int(11) NOT NULL,
  `bm_desc` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bilik_meeting`
--

INSERT INTO `bilik_meeting` (`bm_id`, `bm_desc`) VALUES
(1, 'Bilik Mesyuarat 1'),
(2, 'Bilik Mesyuarat 2');

-- --------------------------------------------------------

--
-- Table structure for table `masa`
--

CREATE TABLE IF NOT EXISTS `masa` (
  `m_id` int(11) NOT NULL,
  `m_desc` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `masa`
--

INSERT INTO `masa` (`m_id`, `m_desc`) VALUES
(1, '8.00 AM - 12.00 PM'),
(2, '2.00 PM - 6.00 PM'),
(3, '8.00 PM - 11.00 PM');

-- --------------------------------------------------------

--
-- Table structure for table `tempahan`
--

CREATE TABLE IF NOT EXISTS `tempahan` (
  `t_id` int(11) NOT NULL,
  `t_datereg` datetime NOT NULL,
  `t_date` date NOT NULL,
  `t_day` varchar(200) NOT NULL,
  `m_id` int(11) NOT NULL,
  `t_perkara` varchar(500) NOT NULL,
  `bm_id` int(11) NOT NULL,
  `t_status` int(11) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bilik_meeting`
--
ALTER TABLE `bilik_meeting`
  ADD PRIMARY KEY (`bm_id`);

--
-- Indexes for table `masa`
--
ALTER TABLE `masa`
  ADD PRIMARY KEY (`m_id`);

--
-- Indexes for table `tempahan`
--
ALTER TABLE `tempahan`
  ADD PRIMARY KEY (`t_id`),
  ADD UNIQUE KEY `unique_book` (`t_date`,`m_id`,`bm_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bilik_meeting`
--
ALTER TABLE `bilik_meeting`
  MODIFY `bm_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `masa`
--
ALTER TABLE `masa`
  MODIFY `m_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `tempahan`
--
ALTER TABLE `tempahan`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
