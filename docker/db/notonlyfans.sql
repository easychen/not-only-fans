# ************************************************************
# Sequel Ace SQL dump
# Version 3038
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 8.0.25)
# Database: notonlyfans
# Generation Time: 2021-08-25 10:13:00 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int unsigned NOT NULL DEFAULT '0',
  `uid` int unsigned NOT NULL DEFAULT '0',
  `text` varchar(256) DEFAULT NULL,
  `is_delete` tinyint unsigned NOT NULL DEFAULT '0',
  `reply_to` int unsigned NOT NULL DEFAULT '0',
  `timeline` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table feed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `feed`;

CREATE TABLE `feed` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL,
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `text` longtext,
  `is_paid` tinyint unsigned NOT NULL DEFAULT '0',
  `files` text,
  `images` text,
  `timeline` datetime DEFAULT NULL,
  `is_forward` tinyint unsigned NOT NULL DEFAULT '0',
  `forward_feed_id` int unsigned NOT NULL DEFAULT '0',
  `forward_uid` int unsigned NOT NULL DEFAULT '0',
  `forward_text` text,
  `forward_is_paid` tinyint unsigned NOT NULL DEFAULT '0',
  `forward_group_id` int unsigned NOT NULL DEFAULT '0',
  `to_groups` varchar(256) NOT NULL DEFAULT '',
  `forward_timeline` datetime DEFAULT NULL,
  `is_delete` tinyint unsigned NOT NULL DEFAULT '0',
  `comment_count` int unsigned DEFAULT NULL,
  `up_count` int unsigned DEFAULT NULL,
  `is_top` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table feed_contribute
# ------------------------------------------------------------

DROP TABLE IF EXISTS `feed_contribute`;

CREATE TABLE `feed_contribute` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `feed_id` int unsigned NOT NULL DEFAULT '0',
  `group_id` int unsigned NOT NULL DEFAULT '0',
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `backward` tinyint unsigned NOT NULL DEFAULT '0',
  `timeline` datetime DEFAULT NULL,
  `forward_feed_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI` (`feed_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group`;

CREATE TABLE `group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `author_uid` int unsigned NOT NULL,
  `price_wei` bigint unsigned NOT NULL,
  `author_address` varchar(256) NOT NULL DEFAULT '',
  `is_paid` tinyint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint unsigned NOT NULL DEFAULT '0',
  `cover` varchar(256) DEFAULT NULL,
  `background` varchar(256) DEFAULT NULL,
  `seller_uid` int unsigned DEFAULT '0',
  `timeline` datetime NOT NULL,
  `member_count` int NOT NULL DEFAULT '0',
  `feed_count` int NOT NULL DEFAULT '0',
  `todo_count` int DEFAULT NULL,
  `promo_level` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '首页推广等级',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table group_blacklist
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_blacklist`;

CREATE TABLE `group_blacklist` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL DEFAULT '0',
  `uid` int NOT NULL DEFAULT '0',
  `timeline` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI` (`group_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table group_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_member`;

CREATE TABLE `group_member` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int unsigned NOT NULL,
  `uid` int unsigned NOT NULL,
  `is_author` tinyint unsigned NOT NULL DEFAULT '0',
  `is_vip` tinyint unsigned NOT NULL DEFAULT '0',
  `timeline` datetime DEFAULT NULL,
  `vip_expire` datetime DEFAULT NULL,
  `can_contribute` tinyint unsigned NOT NULL DEFAULT '1',
  `can_comment` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`group_id`,`uid`),
  KEY `vip_user_intime` (`uid`,`is_vip`,`vip_expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message`;

CREATE TABLE `message` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0' COMMENT '本条记录持有人',
  `from_uid` int unsigned NOT NULL DEFAULT '0',
  `to_uid` int unsigned NOT NULL DEFAULT '0',
  `text` text,
  `timeline` datetime DEFAULT NULL,
  `is_read` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table message_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message_group`;

CREATE TABLE `message_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `from_uid` int unsigned NOT NULL DEFAULT '0',
  `to_uid` int unsigned NOT NULL DEFAULT '0',
  `text` text,
  `timeline` datetime DEFAULT NULL,
  `is_read` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI` (`uid`,`from_uid`,`to_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='每个 from_uid 的最新一条消息聚合';



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL DEFAULT '',
  `username` varchar(32) NOT NULL DEFAULT '',
  `nickname` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(1) NOT NULL DEFAULT '1',
  `avatar` varchar(255) DEFAULT NULL,
  `group_count` int NOT NULL DEFAULT '0',
  `feed_count` int NOT NULL DEFAULT '0',
  `up_count` int NOT NULL DEFAULT '0',
  `timeline` datetime NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cover` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table user_blacklist
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_blacklist`;

CREATE TABLE `user_blacklist` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `block_uid` int unsigned NOT NULL DEFAULT '0',
  `timeline` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI` (`uid`,`block_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
