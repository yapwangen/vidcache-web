-- phpMyAdmin SQL Dump
-- version 3.4.7.1
-- http://www.phpmyadmin.net
--
-- Host: dev.vidcache.net
-- Generation Time: Jan 27, 2013 at 12:40 PM
-- Server version: 5.5.29
-- PHP Version: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vidcache_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `primary_contact_id` int(10) unsigned DEFAULT NULL,
  `password` varchar(60) COLLATE ascii_bin NOT NULL,
  `company` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` int(10) DEFAULT NULL,
  `created` int(10) NOT NULL,
  `deleted` int(10) DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  KEY `primary_contact_id` (`primary_contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `client_api_keys` (
  `client_id` int(10) unsigned NOT NULL,
  `api_key` char(44) COLLATE ascii_bin NOT NULL,
  `crypt_key` char(44) COLLATE ascii_bin NOT NULL,
  `crypt_iv` char(44) COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `client_api_sessions`
--

CREATE TABLE IF NOT EXISTS `client_api_sessions` (
  `client_id` int(11) NOT NULL,
  `remote_addr` varchar(40) COLLATE ascii_bin NOT NULL,
  `token` char(40) COLLATE ascii_bin NOT NULL,
  `crypt_key` char(44) COLLATE ascii_bin NOT NULL,
  `crypt_iv` char(44) COLLATE ascii_bin NOT NULL,
  `request_count` int(10) unsigned NOT NULL DEFAULT '0',
  `last_request` int(10) unsigned DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `is_expired` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`token`),
  KEY `client_id` (`client_id`),
  KEY `is_expired` (`is_expired`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `client_embed_files`
--

CREATE TABLE IF NOT EXISTS `client_embed_files` (
  `client_file_id` int(10) unsigned NOT NULL,
  `handle` char(6) COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`client_file_id`,`handle`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `client_embed_tpl`
--

CREATE TABLE IF NOT EXISTS `client_embed_tpl` (
  `client_embed_tpl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE ascii_bin NOT NULL,
  `handle` char(6) CHARACTER SET ascii NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`client_embed_tpl_id`),
  UNIQUE KEY `handle` (`handle`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_embed_tpl_vars`
--

CREATE TABLE IF NOT EXISTS `client_embed_tpl_vars` (
  `handle` char(6) COLLATE ascii_bin NOT NULL,
  `name` char(40) COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`handle`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `client_embed_vars`
--

CREATE TABLE IF NOT EXISTS `client_embed_vars` (
  `client_id` int(10) unsigned NOT NULL,
  `client_folder_id` int(10) unsigned NOT NULL,
  `handle` char(6) COLLATE ascii_bin NOT NULL,
  `name` char(40) COLLATE ascii_bin NOT NULL,
  `value` varchar(255) COLLATE ascii_bin DEFAULT NULL,
  KEY `handle` (`handle`,`name`),
  KEY `client_id` (`client_id`,`client_folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `client_files`
--

CREATE TABLE IF NOT EXISTS `client_files` (
  `client_file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `client_folder_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `file_chksum` char(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned DEFAULT NULL,
  `deleted` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`client_file_id`),
  KEY `client_folder_id` (`client_folder_id`),
  KEY `file_id` (`file_id`),
  KEY `created` (`created`),
  KEY `deleted` (`deleted`),
  KEY `client_id` (`client_id`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_folders`
--

CREATE TABLE IF NOT EXISTS `client_folders` (
  `client_folder_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `parent_client_folder_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE ascii_bin NOT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  `updated` int(10) unsigned DEFAULT NULL,
  `deleted` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`client_folder_id`),
  UNIQUE KEY `client_id_2` (`client_id`,`parent_client_folder_id`,`name`,`deleted`),
  KEY `parent_client_folder_id` (`parent_client_folder_id`),
  KEY `deleted` (`deleted`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `client_session`
--

CREATE TABLE IF NOT EXISTS `client_session` (
  `token` char(32) COLLATE ascii_bin NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `remote_ip` varchar(255) COLLATE ascii_bin NOT NULL,
  `user_agent` varchar(255) COLLATE ascii_bin NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`token`),
  KEY `client_id` (`contact_id`),
  KEY `expires` (`expires`,`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned DEFAULT NULL,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE ascii_bin NOT NULL,
  `contact_password` varchar(60) COLLATE ascii_bin NOT NULL,
  `contact_type` enum('bill','ship','both') COLLATE ascii_bin NOT NULL DEFAULT 'both',
  `contact_is_active` tinyint(1) NOT NULL,
  `contact_last_login` int(10) NOT NULL,
  `contact_created` int(10) NOT NULL,
  `contact_updated` int(10) NOT NULL,
  `phone` varchar(255) COLLATE ascii_bin NOT NULL,
  `fax` varchar(255) COLLATE ascii_bin DEFAULT NULL,
  `address_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_1` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_2` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `city` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `state` char(2) COLLATE ascii_bin NOT NULL,
  `zip` varchar(20) COLLATE ascii_bin NOT NULL,
  `country` char(2) COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `staff_id` (`staff_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chksum` char(40) COLLATE ascii_bin NOT NULL,
  `mime_type` char(255) COLLATE ascii_bin NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `stream_rate` int(10) unsigned DEFAULT NULL,
  `download_rate` int(10) unsigned DEFAULT NULL,
  `data_copies` int(10) unsigned NOT NULL,
  `data_copies_req` int(10) unsigned NOT NULL,
  `cache_copies` int(10) unsigned NOT NULL,
  `cache_copies_req` int(10) unsigned NOT NULL,
  `created` decimal(20,5) unsigned NOT NULL,
  `updated` decimal(20,5) unsigned NOT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `chksum` (`chksum`),
  KEY `mime_type` (`mime_type`,`created`,`updated`),
  KEY `data_copies` (`data_copies`,`data_copies_req`),
  KEY `cache_copies` (`cache_copies`,`cache_copies_req`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_clones`
--

CREATE TABLE IF NOT EXISTS `file_clones` (
  `file_id` int(10) unsigned NOT NULL,
  `node_id` int(10) unsigned NOT NULL,
  `is_cache` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated` int(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `file_id` (`file_id`,`node_id`),
  KEY `is_cache` (`is_cache`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `file_stats`
--

CREATE TABLE IF NOT EXISTS `file_stats` (
  `file_id` int(10) unsigned NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `hits` bigint(20) unsigned NOT NULL,
  `bytes` bigint(20) unsigned NOT NULL,
  `period_start` int(10) unsigned NOT NULL,
  `period_length` int(10) unsigned NOT NULL,
  KEY `hits` (`hits`,`bytes`),
  KEY `period_start` (`period_start`),
  KEY `file_id` (`file_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(8) COLLATE ascii_bin NOT NULL,
  `reference` int(11) unsigned NOT NULL,
  `date` decimal(20,5) unsigned NOT NULL,
  `message` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `date` (`date`),
  KEY `reference` (`reference`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE IF NOT EXISTS `nodes` (
  `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` char(255) COLLATE ascii_bin NOT NULL,
  `address` int(10) unsigned NOT NULL,
  `port` int(5) unsigned NOT NULL,
  `load_1m` double(5,2) unsigned NOT NULL,
  `load_5m` double(5,2) unsigned NOT NULL,
  `load_15m` double(5,2) unsigned NOT NULL,
  `total_space` bigint(20) unsigned NOT NULL,
  `free_space` bigint(20) unsigned NOT NULL,
  `total_data` bigint(20) unsigned NOT NULL,
  `total_cache` bigint(20) unsigned NOT NULL,
  `pct_data` tinyint(2) unsigned NOT NULL,
  `pct_cache` tinyint(2) unsigned NOT NULL,
  `max_pct_data` tinyint(2) unsigned NOT NULL,
  `max_pct_cache` tinyint(2) unsigned NOT NULL,
  `cache_total` bigint(20) unsigned NOT NULL,
  `cache_free` bigint(20) unsigned NOT NULL,
  `cache_file_count` int(10) unsigned NOT NULL,
  `last_updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`node_id`),
  UNIQUE KEY `address` (`address`),
  UNIQUE KEY `hostname` (`hostname`),
  KEY `cache_total` (`cache_total`,`cache_free`,`cache_file_count`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `note_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(8) COLLATE ascii_bin NOT NULL,
  `reference` int(10) unsigned NOT NULL,
  `author_type` char(8) COLLATE ascii_bin NOT NULL,
  `author_reference` int(10) unsigned NOT NULL,
  `date` decimal(20,5) unsigned NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `primary_contact_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE ascii_bin NOT NULL,
  `password` char(60) COLLATE ascii_bin NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `is_manager` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` int(10) NOT NULL,
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `email` (`email`),
  KEY `primary_contact_id` (`primary_contact_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=15 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `primary_contact_id`, `email`, `password`, `name`, `is_manager`, `is_active`, `last_login`) VALUES
(1, NULL, 'admin@vidcache.net', '$2a$12$u83T7V/zxHGM4/pvfeu5F.zqy1RTvS7X69jUYOCyymlq9ZBHuCbDW', 'Administrator', 1, 1, 1359183246);

-- --------------------------------------------------------

--
-- Table structure for table `staff_session`
--

CREATE TABLE IF NOT EXISTS `staff_session` (
  `token` char(40) COLLATE ascii_bin NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `remote_ip` varchar(255) COLLATE ascii_bin NOT NULL,
  `user_agent` varchar(255) COLLATE ascii_bin NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`token`),
  KEY `staff_id` (`staff_id`),
  KEY `expires` (`expires`,`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_department_id` int(10) unsigned NOT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE ascii_bin DEFAULT NULL,
  `sender_emails` tinytext COLLATE ascii_bin,
  `staff_emails` tinytext COLLATE ascii_bin,
  `status` enum('open','holding','closed','deleted') COLLATE ascii_bin NOT NULL DEFAULT 'open',
  `created` int(10) unsigned DEFAULT NULL,
  `updated` int(10) unsigned DEFAULT NULL,
  `deleted` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `client_id` (`client_id`),
  KEY `staff_id` (`staff_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `deleted` (`deleted`),
  KEY `ticket_department_id` (`ticket_department_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_departments`
--

CREATE TABLE IF NOT EXISTS `ticket_departments` (
  `ticket_department_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE ascii_bin NOT NULL,
  `email` varchar(255) COLLATE ascii_bin NOT NULL,
  `cc` tinytext COLLATE ascii_bin,
  `bcc` tinytext COLLATE ascii_bin,
  PRIMARY KEY (`ticket_department_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_messages`
--

CREATE TABLE IF NOT EXISTS `ticket_messages` (
  `ticket_message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `author_email` varchar(255) COLLATE ascii_bin NOT NULL,
  `author_name` varchar(255) COLLATE ascii_bin DEFAULT NULL,
  `author_type` enum('staff','client','vendor','brand','anonymous') COLLATE ascii_bin NOT NULL DEFAULT 'anonymous',
  `author_id` int(10) unsigned DEFAULT NULL,
  `message` longtext COLLATE ascii_bin NOT NULL,
  `message_type` enum('staff_reply','staff_post','staff_comment','client_reply','client_post','anonymous_reply','anonymous_post') COLLATE ascii_bin NOT NULL DEFAULT 'anonymous_reply',
  `posted` decimal(20,5) unsigned NOT NULL,
  `deleted` decimal(20,5) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_message_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_message_attachment`
--

CREATE TABLE IF NOT EXISTS `ticket_message_attachment` (
  `ticket_message_attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_message_id` int(10) unsigned NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL,
  `mime_type` varchar(255) COLLATE ascii_bin NOT NULL,
  `file_name` varchar(255) COLLATE ascii_bin NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL,
  `content` longblob NOT NULL,
  `checksum` char(40) COLLATE ascii_bin NOT NULL,
  `added` decimal(20,5) unsigned NOT NULL,
  PRIMARY KEY (`ticket_message_attachment_id`),
  KEY `ticket_message_id` (`ticket_message_id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE IF NOT EXISTS `todo` (
  `todo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(8) COLLATE ascii_bin NOT NULL,
  `reference` int(10) unsigned NOT NULL,
  `date` decimal(20,5) unsigned NOT NULL,
  `due` int(10) unsigned NOT NULL,
  `message` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `is_complete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`todo_id`),
  KEY `is_complete` (`is_complete`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=9 ;

