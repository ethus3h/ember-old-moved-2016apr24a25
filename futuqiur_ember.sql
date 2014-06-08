-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 08, 2014 at 03:16 PM
-- Server version: 5.5.32-cll-lve
-- PHP Version: 5.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `futuqiur_ember`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_type`
--

CREATE TABLE IF NOT EXISTS `auth_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `auth_type`
--

INSERT INTO `auth_type` (`id`, `name`, `description`) VALUES
(0, 'No authorization', 'For example, a non-logged-in user.'),
(1, 'User', 'Normal logged-in user'),
(2, 'Moderator', NULL),
(3, 'Administrator', NULL),
(4, 'Oversight', NULL),
(5, 'Record Keeper', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chunks`
--

CREATE TABLE IF NOT EXISTS `chunks` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` text NOT NULL,
  `altaddress` text NOT NULL COMMENT 'Backup; not currently used',
  `md5` binary(16) NOT NULL COMMENT 'For deduplication',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `chunks`
--

INSERT INTO `chunks` (`id`, `address`, `altaddress`, `md5`) VALUES
(1, 'ia:0.0016865.COALPROJECT.RECORD33', '', '•Ì$x˘9=èÉÓyÊp'),
(2, 'ia:0.0027360.COALPROJECT.RECORD33', '', '\rà÷''4û=ç¢Ã4h‡W'),
(3, 'ia:0.0033123.COALPROJECT.RECORD33', '', 'xF:8JZ§˙’˙s‚ıÏ¸'),
(4, 'ia:0.0047472.COALPROJECT.RECORD33', '', '0bÛÃ±÷?ˇxi⁄&´Ø^'),
(5, 'ia:0.0058868.COALPROJECT.RECORD33', '', '¥˘EC>§√i¡''Aˆ*#Ã¿'),
(6, 'ia:0.0066638.COALPROJECT.RECORD33', '', 'jMûh“í>Ü√2vg◊wÙ'),
(0, '', '', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0');

-- --------------------------------------------------------

--
-- Table structure for table `dc`
--

CREATE TABLE IF NOT EXISTS `dc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `number` bigint(20) unsigned NOT NULL COMMENT 'This Dc''s ID',
  `name` bigint(20) unsigned NOT NULL,
  `class` int(11) unsigned NOT NULL COMMENT 'Dc class',
  `subclass` int(11) unsigned NOT NULL COMMENT 'Dc subclass',
  `simple` text NOT NULL,
  `complex` longtext NOT NULL COMMENT 'Complex rendering rules for this Dc',
  `description` bigint(20) unsigned NOT NULL COMMENT 'A description of this Dc',
  `variants` longtext NOT NULL COMMENT 'List of Dcs that are variants of this Dc',
  `decomposition` longtext NOT NULL,
  `replaces` bigint(20) unsigned NOT NULL COMMENT 'The Dc that this is a newer revision of',
  `annotations` bigint(20) unsigned NOT NULL COMMENT 'localizable string',
  `synonyms` bigint(20) unsigned NOT NULL COMMENT 'localizable string',
  `see also` bigint(20) unsigned NOT NULL COMMENT 'localizable string',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dcclasses`
--

CREATE TABLE IF NOT EXISTS `dcclasses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` bigint(20) unsigned NOT NULL COMMENT 'localizable string',
  `container` bigint(20) unsigned NOT NULL COMMENT 'Containers may not be nested more than 1 deep',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `encmappings`
--

CREATE TABLE IF NOT EXISTS `encmappings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `encoding` bigint(20) unsigned NOT NULL,
  `table` bigint(20) unsigned NOT NULL,
  `entry` bigint(20) unsigned NOT NULL COMMENT 'The ID in the source encoding',
  `dc` bigint(20) unsigned NOT NULL COMMENT 'The Dc(s) that this maps to',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `encodings`
--

CREATE TABLE IF NOT EXISTS `encodings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text_identifier` varchar(10) NOT NULL COMMENT 'For example, utf8 (for use in programming)',
  `name` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `enctables`
--

CREATE TABLE IF NOT EXISTS `enctables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `encoding` bigint(20) unsigned NOT NULL,
  `text_identifier` varchar(20) NOT NULL,
  `name` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'relates to strings:language',
  `name` bigint(20) unsigned NOT NULL COMMENT 'relates to strings:id; used to provide UI reference to what a language is',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Localizable names for languages (related to the strings table)' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`) VALUES
(0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `localized`
--

CREATE TABLE IF NOT EXISTS `localized` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `language` int(10) unsigned NOT NULL,
  `data` bigint(20) unsigned NOT NULL COMMENT 'Coal ID of the string in this language',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Localizable strings.' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `localized`
--

INSERT INTO `localized` (`id`, `language`, `data`) VALUES
(1, 0, 2),
(2, 0, 3),
(0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `recordmeta`
--

CREATE TABLE IF NOT EXISTS `recordmeta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trait` bigint(20) unsigned NOT NULL,
  `value` bigint(20) unsigned NOT NULL COMMENT 'A localizable string.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `recordrevisions`
--

CREATE TABLE IF NOT EXISTS `recordrevisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `record` bigint(20) unsigned NOT NULL,
  `date` bigint(20) unsigned NOT NULL COMMENT 'date this change was made, as a coal',
  `user` bigint(20) unsigned NOT NULL COMMENT 'user who made this change',
  `owner` bigint(20) unsigned NOT NULL COMMENT 'owner of this record',
  `flags` bigint(20) unsigned NOT NULL COMMENT 'A non-localizable string',
  `types` bigint(20) unsigned NOT NULL COMMENT 'list of record types that apply to this record',
  `permissions` bigint(20) unsigned NOT NULL COMMENT 'A non-localizable string',
  `metadata` longtext NOT NULL COMMENT 'List of relevant record metadata records',
  `relationships` bigint(20) unsigned NOT NULL COMMENT 'A non-localizable string',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `recordrevisions`
--

INSERT INTO `recordrevisions` (`id`, `record`, `date`, `user`, `owner`, `flags`, `types`, `permissions`, `metadata`, `relationships`) VALUES
(0, 0, 0, 0, 0, 0, 0, 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE IF NOT EXISTS `records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `latest` bigint(20) unsigned NOT NULL COMMENT 'Latest revision of this record',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `latest`) VALUES
(0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `recordtypes`
--

CREATE TABLE IF NOT EXISTS `recordtypes` (
  `id` bigint(20) unsigned NOT NULL,
  `name` bigint(20) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE IF NOT EXISTS `relationships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target` bigint(20) unsigned NOT NULL COMMENT 'a record',
  `destination` bigint(20) unsigned NOT NULL COMMENT 'a record',
  `relationship` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `relationships`
--

INSERT INTO `relationships` (`id`, `target`, `destination`, `relationship`) VALUES
(0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `relationtypes`
--

CREATE TABLE IF NOT EXISTS `relationtypes` (
  `id` bigint(20) unsigned NOT NULL,
  `name` bigint(20) unsigned NOT NULL,
  `namerev` bigint(20) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `strings`
--

CREATE TABLE IF NOT EXISTS `strings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chunk` bigint(20) unsigned NOT NULL,
  `md5` binary(16) NOT NULL COMMENT 'to prevent an attacker sending fake data in response to a coal retrieval',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `strings`
--

INSERT INTO `strings` (`id`, `chunk`, `md5`) VALUES
(1, 2, '•Ì$x˘9=èÉÓyÊp'),
(2, 4, 'xF:8JZ§˙’˙s‚ıÏ¸'),
(3, 6, '¥˘EC>§√i¡''Aˆ*#Ã¿'),
(0, 0, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` binary(16) NOT NULL COMMENT 'md5 of user name',
  `password` varchar(60) NOT NULL COMMENT 'salted sha1',
  `record` bigint(20) unsigned NOT NULL COMMENT 'record relating to this person',
  `authorisation` int(10) unsigned NOT NULL COMMENT 'relate to auth_type',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `record`, `authorisation`) VALUES
(0, '	èkÕF!”s ﬁNÉ&''¥ˆ', '$2a$08$2UaRnw9KvJ.7QfAQM/AxcuRFHqpO.S6pu/ZJdj6fdp8UA0TB1jQya', 0, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
