CREATE DATABASE feliix CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

-- CREATE USER 'feliix'@'localhost' IDENTIFIED WITH mysql_native_password BY '1qaz@WSX';


-- CREATE DATABASE feliix CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- CREATE USER 'feliix'@'localhost' IDENTIFIED  BY '1qaz@WSX';

-- GRANT ALL PRIVILEGES ON feliix.* TO 'feliix'@'%' WITH GRANT OPTION;

CREATE USER 'feliix'@'%' IDENTIFIED WITH mysql_native_password BY '1qaz@WSX';

GRANT ALL PRIVILEGES ON feliix.* TO 'feliix'@'%';



-- --------------------------------------------------------
-- 主機:                           127.0.0.1
-- 伺服器版本:                        10.4.11-MariaDB - mariadb.org binary distribution
-- 伺服器作業系統:                      Win64
-- HeidiSQL 版本:                  10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 傾印 feliix 的資料庫結構
CREATE DATABASE IF NOT EXISTS `feliix` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `feliix`;



-- 傾印  表格 ludb.user 結構
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apartment_id` int(11) DEFAULT 0,
  `title_id` int(11) DEFAULT 0,
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `is_admin` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- user 登入歷史
CREATE TABLE IF NOT EXISTS `login_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';



-- 傾印  表格 ludb.user 結構
CREATE TABLE IF NOT EXISTS `on_duty` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `duty_date` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_type` varchar(100) COLLATE utf8mb4_unicode_ci default '',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci default '',
  `remark` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `duty_time` varchar(10) COLLATE utf8mb4_unicode_ci default '',
  `explain` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `pic_time` varchar(64) COLLATE utf8mb4_unicode_ci  default '',
  `pic_lat` decimal(10, 8) default 0.0,
  `pic_lng` decimal(11, 8) default 0.0,
  `pic_server_time` varchar(64) COLLATE utf8mb4_unicode_ci  default '',
  `pic_server_lat` decimal(10, 8) default 0.0,
  `pic_server_lng` decimal(11, 8) default 0.0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


ALTER TABLE on_duty
ADD COLUMN `pos_lat` decimal(10, 8) default 0.0 AFTER `explain`;

ALTER TABLE on_duty
ADD COLUMN `pos_lng` decimal(11, 8) default 0.0 AFTER pos_lat;





ALTER TABLE user
ADD COLUMN `need_punch`  int(11) DEFAULT 1 AFTER is_admin;

CREATE TABLE IF NOT EXISTS `user_department` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

INSERT INTO user_department (department) VALUES('SALES');
INSERT INTO user_department (department) VALUES('LIGHTING');
INSERT INTO user_department (department) VALUES('OFFICE');
INSERT INTO user_department (department) VALUES('DESIGN');
INSERT INTO user_department (department) VALUES('SERVICE');
INSERT INTO user_department (department) VALUES('ADMIN');
INSERT INTO user_department (department) VALUES('STORE');


CREATE TABLE IF NOT EXISTS `user_title` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

INSERT INTO user_title(department_id, title) VALUES(1, 'JR. ACCOUNT EXECUTIVE');
INSERT INTO user_title(department_id, title) VALUES(1, 'ACCOUNT EXECUTIVE');
INSERT INTO user_title(department_id, title) VALUES(1, 'ACCOUNT MANAGER');
INSERT INTO user_title(department_id, title) VALUES(1, 'ASSISTANT SALES MANAGER');
INSERT INTO user_title(department_id, title) VALUES(1, 'SALES MANAGER');

INSERT INTO user_title(department_id, title) VALUES(2, 'JR. LIGHTING DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(2, 'LIGHTING DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(2, 'SR. LIGHTING DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(2, 'ASSISTANT LIGHTING MANAGER');
INSERT INTO user_title(department_id, title) VALUES(2, 'LIGHTING MANAGER');

INSERT INTO user_title(department_id, title) VALUES(3, 'JR. OFFICE SYSTEMS DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(3, 'OFFICE SYSTEMS DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(3, 'SR. OFFICE SYSTEMS DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(3, 'ASSISTANT OFFICE SYSTEMS MANAGER');
INSERT INTO user_title(department_id, title) VALUES(3, 'OFFICE SYSTEMS MANAGER');

INSERT INTO user_title(department_id, title) VALUES(4, 'JR. GRAPHIC DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(4, 'GRAPHIC DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(4, 'SR. GRAPHIC DESIGNER');
INSERT INTO user_title(department_id, title) VALUES(4, 'ASSISTANT BRAND MANAGER');
INSERT INTO user_title(department_id, title) VALUES(4, 'BRAND MANAGER');

INSERT INTO user_title(department_id, title) VALUES(5, 'TECHNICIAN');
INSERT INTO user_title(department_id, title) VALUES(5, 'LEAD MAN');
INSERT INTO user_title(department_id, title) VALUES(5, 'JR. ENGINEER');
INSERT INTO user_title(department_id, title) VALUES(5, 'SR. ENGINEER');
INSERT INTO user_title(department_id, title) VALUES(5, 'ENGINERING MANAGER');

INSERT INTO user_title(department_id, title) VALUES(6, 'JR. OFFICE ADMIN ASSOCIATE');
INSERT INTO user_title(department_id, title) VALUES(6, 'OFFICE ADMIN ASSOCIATE');
INSERT INTO user_title(department_id, title) VALUES(6, 'SR. OFFICE ADMIN ASSOCIATE');
INSERT INTO user_title(department_id, title) VALUES(6, 'ASSISTANT OFFICE ADMIN ASSOCIATE');
INSERT INTO user_title(department_id, title) VALUES(6, 'OPERATIONS MANAGER');

INSERT INTO user_title(department_id, title) VALUES(7, 'STORE SALES EXECUTIVE');
INSERT INTO user_title(department_id, title) VALUES(7, 'SR. STORE SALES EXECUTIVE');
INSERT INTO user_title(department_id, title) VALUES(7, 'ASSISTANT STORE MANAGER');
INSERT INTO user_title(department_id, title) VALUES(7, 'STORE MANAGER');


alter table on_duty change `pic_lat` `pic_lat` decimal(12, 8) default 0.0;

alter table on_duty change `pic_server_lat` `pic_server_lat` decimal(12, 8) default 0.0;

alter table on_duty change `pic_lng` `pic_lng` decimal(12, 8) default 0.0;

alter table on_duty change `pic_server_lng` `pic_server_lng` decimal(12, 8) default 0.0;

alter table on_duty change `pos_lng` `pos_lng` decimal(12, 8) default 0.0;

alter table on_duty change `pos_lat` `pos_lat` decimal(12, 8) default 0.0;


CREATE TABLE IF NOT EXISTS `apply_for_leave` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `start_date` varchar(20) COLLATE utf8mb4_unicode_ci default '',
  `start_time` varchar(20) COLLATE utf8mb4_unicode_ci default '',
  `end_date` varchar(20) COLLATE utf8mb4_unicode_ci default '',
  `end_time` varchar(20) COLLATE utf8mb4_unicode_ci default '',
  `leave_type` varchar(10) COLLATE utf8mb4_unicode_ci default '',
  `leave` decimal(10, 2) default 0.0,
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `reason` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `approval_id` bigint(20) unsigned default 0,
  `approval_at` timestamp NULL DEFAULT NULL,
  `reject_reason` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `reject_at` timestamp NULL DEFAULT NULL,

  `re_approval_id` bigint(20) unsigned default 0,
  `re_approval_at` timestamp NULL DEFAULT NULL,
  `re_reject_reason` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `re_reject_at` timestamp NULL DEFAULT NULL,


  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


ALTER TABLE user_title
ADD COLUMN `head_of_department`  int(11) DEFAULT 0 AFTER title;

ALTER TABLE user
ADD COLUMN `annual_leave`  int(11) DEFAULT 0 after is_admin;

ALTER TABLE user
ADD COLUMN `sick_leave`  int(11) DEFAULT 0 after is_admin;

ALTER TABLE user
ADD COLUMN `is_manager`  int(11) DEFAULT 0 after is_admin;


alter table on_duty change `pic_lat` `pic_lat` decimal(24, 12) default 0.0;

alter table on_duty change `pic_server_lat` `pic_server_lat` decimal(24, 12) default 0.0;

alter table on_duty change `pic_lng` `pic_lng` decimal(24, 12) default 0.0;

alter table on_duty change `pic_server_lng` `pic_server_lng` decimal(24, 12) default 0.0;

alter table on_duty change `pos_lng` `pos_lng` decimal(24, 12) default 0.0;

alter table on_duty change `pos_lat` `pos_lat` decimal(24, 12) default 0.0;


-- excel formula =IF(OR(WEEKDAY(A375)=1, WEEKDAY(A375) = 7), YEAR(A375) & TEXT(MONTH(A375), "00") & TEXT(DAY(A375), "00"),"")
CREATE TABLE `holiday` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `holiday` varchar(256) DEFAULT '',
  `from_date` varchar(64) DEFAULT '',
  `year` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `holiday` (`from_date`,  `year`) VALUES
('20200104', '2020'),
('20200105', '2020'),
('20200111', '2020'),
('20200112', '2020'),
('20200118', '2020'),
('20200119', '2020'),
('20200125', '2020'),
('20200126', '2020'),
('20200201', '2020'),
('20200202', '2020'),
('20200208', '2020'),
('20200209', '2020'),
('20200215', '2020'),
('20200216', '2020'),
('20200222', '2020'),
('20200223', '2020'),
('20200229', '2020'),
('20200301', '2020'),
('20200307', '2020'),
('20200308', '2020'),
('20200314', '2020'),
('20200315', '2020'),
('20200321', '2020'),
('20200322', '2020'),
('20200328', '2020'),
('20200329', '2020'),
('20200404', '2020'),
('20200405', '2020'),
('20200411', '2020'),
('20200412', '2020'),
('20200418', '2020'),
('20200419', '2020'),
('20200425', '2020'),
('20200426', '2020'),
('20200502', '2020'),
('20200503', '2020'),
('20200509', '2020'),
('20200510', '2020'),
('20200516', '2020'),
('20200517', '2020'),
('20200523', '2020'),
('20200524', '2020'),
('20200530', '2020'),
('20200531', '2020'),
('20200606', '2020'),
('20200607', '2020'),
('20200613', '2020'),
('20200614', '2020'),
('20200620', '2020'),
('20200621', '2020'),
('20200627', '2020'),
('20200628', '2020'),
('20200704', '2020'),
('20200705', '2020'),
('20200711', '2020'),
('20200712', '2020'),
('20200718', '2020'),
('20200719', '2020'),
('20200725', '2020'),
('20200726', '2020'),
('20200801', '2020'),
('20200802', '2020'),
('20200808', '2020'),
('20200809', '2020'),
('20200815', '2020'),
('20200816', '2020'),
('20200822', '2020'),
('20200823', '2020'),
('20200829', '2020'),
('20200830', '2020'),
('20200905', '2020'),
('20200906', '2020'),
('20200912', '2020'),
('20200913', '2020'),
('20200919', '2020'),
('20200920', '2020'),
('20200926', '2020'),
('20200927', '2020'),
('20201003', '2020'),
('20201004', '2020'),
('20201010', '2020'),
('20201011', '2020'),
('20201017', '2020'),
('20201018', '2020'),
('20201024', '2020'),
('20201025', '2020'),
('20201031', '2020'),
('20201101', '2020'),
('20201107', '2020'),
('20201108', '2020'),
('20201114', '2020'),
('20201115', '2020'),
('20201121', '2020'),
('20201122', '2020'),
('20201128', '2020'),
('20201129', '2020'),
('20201205', '2020'),
('20201206', '2020'),
('20201212', '2020'),
('20201213', '2020'),
('20201219', '2020'),
('20201220', '2020'),
('20201226', '2020'),
('20201227', '2020'),
('20200101', '2020'),
('20200123', '2020'),
('20200225', '2020'),
('20200320', '2020'),
('20200409', '2020'),
('20200410', '2020'),
('20200501', '2020'),
('20200525', '2020'),
('20200612', '2020'),
('20200731', '2020'),
('20200820', '2020'),
('20200821', '2020'),
('20200831', '2020'),
('20200903', '2020'),
('20200908', '2020'),
('20200922', '2020'),
('20201029', '2020'),
('20201102', '2020'),
('20201130', '2020'),
('20201208', '2020'),
('20201221', '2020'),
('20201224', '2020'),
('20201225', '2020'),
('20201230', '2020'),
('20201231', '2020');



CREATE TABLE IF NOT EXISTS `leave` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `apply_id` bigint(20) unsigned NOT NULL,
  `apply_date` varchar(20) COLLATE utf8mb4_unicode_ci default '',
  `apply_period` varchar(10) COLLATE utf8mb4_unicode_ci default '',
  `leave_type` varchar(10) COLLATE utf8mb4_unicode_ci default '',
  `duration` decimal(10, 2) default 0.0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


