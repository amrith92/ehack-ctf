-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 17, 2013 at 01:41 AM
-- Server version: 5.5.29
-- PHP Version: 5.4.12-2~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ctf`
--

-- --------------------------------------------------------

--
-- Table structure for table `announce`
--

CREATE TABLE IF NOT EXISTS `announce` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `announce` text COLLATE utf8_unicode_ci NOT NULL,
  `updated_tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `auth_users`
--

CREATE TABLE IF NOT EXISTS `auth_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `google_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter_access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_id` bigint(20) DEFAULT NULL,
  `fname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dp` text COLLATE utf8_unicode_ci,
  `dob` date DEFAULT NULL,
  `about_me` longtext COLLATE utf8_unicode_ci,
  `gender` enum('Male','Female') COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` int(6) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` longtext COLLATE utf8_unicode_ci,
  `location` point DEFAULT NULL COMMENT '(DC2Type:point)',
  `login_mode` enum('facebook','google','twitter','manual') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'manual',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D8A1F49C92FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_D8A1F49CA0D96FBF` (`email_canonical`),
  KEY `org` (`org_id`),
  KEY `state_id` (`state_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `country_id` int(6) NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `iso_code_2` text COLLATE utf8_unicode_ci,
  `iso_code_3` text COLLATE utf8_unicode_ci,
  `post_code_required` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `global_state`
--

CREATE TABLE IF NOT EXISTS `global_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enable_ctf` tinyint(1) NOT NULL DEFAULT '1',
  `enable_chat` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level` mediumint(6) NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `answer_tpl` text COLLATE utf8_unicode_ci NOT NULL,
  `hints` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `ref_invites`
--

CREATE TABLE IF NOT EXISTS `ref_invites` (
  `referrer_id` bigint(20) NOT NULL,
  `invitee_id` bigint(20) NOT NULL,
  PRIMARY KEY (`referrer_id`,`invitee_id`),
  KEY `invitee_id` (`invitee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stages`
--

CREATE TABLE IF NOT EXISTS `stages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `stages_levels`
--

CREATE TABLE IF NOT EXISTS `stages_levels` (
  `stage_id` bigint(20) NOT NULL,
  `question_id` bigint(20) NOT NULL,
  PRIMARY KEY (`stage_id`,`question_id`),
  UNIQUE KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `status` text COLLATE utf8_unicode_ci,
  `score` int(11) NOT NULL,
  `team_pic` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_name_uk` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `team_member_request`
--

CREATE TABLE IF NOT EXISTS `team_member_request` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `status` enum('requested','accepted','rejected','acceptedAndAdmin') COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `updated_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `team_requests`
--

CREATE TABLE IF NOT EXISTS `team_requests` (
  `team_id` bigint(20) NOT NULL,
  `request_id` bigint(20) NOT NULL,
  PRIMARY KEY (`team_id`,`request_id`),
  UNIQUE KEY `request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_quest`
--

CREATE TABLE IF NOT EXISTS `user_quest` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `quest_stage` bigint(20) NOT NULL,
  `quest_level` bigint(20) NOT NULL,
  `current_stage` bigint(20) NOT NULL,
  `current_level` bigint(20) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `qu_stage_fk_id` (`quest_stage`),
  KEY `cu_stage_fk_id` (`current_stage`),
  KEY `quest_level` (`quest_level`),
  KEY `current_level` (`current_level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_quest_history`
--

CREATE TABLE IF NOT EXISTS `user_quest_history` (
  `quest_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  PRIMARY KEY (`quest_id`,`item_id`),
  UNIQUE KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_quest_history_item`
--

CREATE TABLE IF NOT EXISTS `user_quest_history_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) NOT NULL,
  `status` enum('ATTEMPTING','CORRECT','WRONG') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ATTEMPTING',
  `used_hint` tinyint(1) NOT NULL DEFAULT '0',
  `attempted_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `first_attempt_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uqh_q_fk_id` (`question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE IF NOT EXISTS `zone` (
  `zone_id` int(11) NOT NULL,
  `country_id` int(6) NOT NULL,
  `code` text COLLATE utf8_unicode_ci,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`zone_id`),
  KEY `country_id` (`country_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_users`
--
ALTER TABLE `auth_users`
  ADD CONSTRAINT `auth_users_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_users_ibfk_2` FOREIGN KEY (`state_id`) REFERENCES `zone` (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_users_ibfk_3` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_invites`
--
ALTER TABLE `ref_invites`
  ADD CONSTRAINT `ref_invites_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ref_invites_ibfk_2` FOREIGN KEY (`invitee_id`) REFERENCES `auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stages_levels`
--
ALTER TABLE `stages_levels`
  ADD CONSTRAINT `sl_q_fk_id` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sl_s_fk_id` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `team_member_request`
--
ALTER TABLE `team_member_request`
  ADD CONSTRAINT `team_member_request_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `team_requests`
--
ALTER TABLE `team_requests`
  ADD CONSTRAINT `team_requests_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `team_requests_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `team_member_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_quest`
--
ALTER TABLE `user_quest`
  ADD CONSTRAINT `cu_stage_fk_id` FOREIGN KEY (`current_stage`) REFERENCES `stages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `qu_stage_fk_id` FOREIGN KEY (`quest_stage`) REFERENCES `stages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_quest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_quest_ibfk_2` FOREIGN KEY (`quest_level`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_quest_ibfk_3` FOREIGN KEY (`current_level`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_quest_history`
--
ALTER TABLE `user_quest_history`
  ADD CONSTRAINT `qu_fk_id` FOREIGN KEY (`quest_id`) REFERENCES `user_quest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_quest_history_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `user_quest_history_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_quest_history_item`
--
ALTER TABLE `user_quest_history_item`
  ADD CONSTRAINT `uqh_q_fk_id` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `zone`
--
ALTER TABLE `zone`
  ADD CONSTRAINT `zone_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
