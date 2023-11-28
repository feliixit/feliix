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

ALTER TABLE user
ADD COLUMN `head_of_department`  int(11) DEFAULT 0 after is_admin;


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

-- 20200620 for manager leave credit
ALTER TABLE user
ADD COLUMN `manager_leave`  int(11) DEFAULT 0 after is_admin;

-- 20200622 add reject id for apply_for_leave
ALTER TABLE apply_for_leave
ADD COLUMN `reject_id`  int(11) DEFAULT 0 after approval_at;

ALTER TABLE apply_for_leave
ADD COLUMN `re_reject_id`  int(11) DEFAULT 0 after re_approval_at;


CREATE TABLE IF NOT EXISTS `leave_flow` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `apartment_id` bigint(20) unsigned NOT NULL,
  `flow` bigint(20) default 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 信件歷史
CREATE TABLE IF NOT EXISTS `mail_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `approve` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_time` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `project_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_client_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_type` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `class_name`varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_priority` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `priority` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `class_name`varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_status` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `project_stage` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stage` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_main` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `catagory_id` bigint(20)  DEFAULT 0 NOT NULL,
  `client_type_id` bigint(20)  DEFAULT 0 NOT NULL,
  `priority_id` bigint(20)  DEFAULT 0 NOT NULL,
  `project_status_id` bigint(20)  DEFAULT 0 not null,
  `stage_id` bigint(20)  DEFAULT 0  not null,
  `project_name` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `estimate_close_prob` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `period_start` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `period_end` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `close_reason` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE project_main
ADD COLUMN `special_note` varchar(128) DEFAULT '' AFTER project_status_id;

ALTER TABLE user
ADD COLUMN `test_manager` varchar(1)  DEFAULT '' AFTER is_admin;

-- 2020/08/18
ALTER TABLE project_main
ADD COLUMN `location` varchar(1024) DEFAULT '' AFTER estimate_close_prob;

ALTER TABLE project_main
ADD COLUMN `contactor` varchar(256) DEFAULT '' AFTER location;

ALTER TABLE project_main
ADD COLUMN `contact_number` varchar(256) DEFAULT '' AFTER contactor;

ALTER TABLE project_main
ADD COLUMN `edit_reason` varchar(256) DEFAULT '' AFTER contact_number;

CREATE TABLE IF NOT EXISTS `project_comments` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `comment` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_probability` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `probability` int(11) DEFAULT 0,
  `reason` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `type_id` bigint(20)  DEFAULT 0 NOT NULL,
  `description` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `doc_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_proof` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `doc_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_stages` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `sequence` bigint(20)  DEFAULT 0 NOT NULL,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `stages_status_id` bigint(20)  DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_statuses` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `reason` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `price_record` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` timestamp NULL DEFAULT NULL,
  `cash_in` bigint(20) default 0,
  `cash_out` bigint(20) default 0,
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE user
ADD COLUMN `is_viewer` bool  DEFAULT false AFTER is_admin;

CREATE TABLE IF NOT EXISTS `project_edit_info` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `reason` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `project_action_comment` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `comment` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `picname1` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `picname2` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_est_prob` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `prob` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `comment` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE price_record MODIFY COLUMN paid_date Date;


-- 20200830 add for project_action_detail
CREATE TABLE IF NOT EXISTS `project_action_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `detail_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `detail_desc` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20200830 add for gcp storage
CREATE TABLE IF NOT EXISTS `gcp_storage_file` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `batch_id` bigint(20)  DEFAULT 0 NOT NULL,
  `batch_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `bucketname` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT 'feliiximg',
  `filename` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gcp_name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gcp_msg` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT 0,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20200905 add for project_edit_stage
CREATE TABLE IF NOT EXISTS `project_edit_stage` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `reason` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20200914 stage_client
CREATE TABLE IF NOT EXISTS `project_stage_client` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `option` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20200914 stage_client_task
CREATE TABLE IF NOT EXISTS `project_stage_client_task` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- fix cash in out to decimal
alter table price_record change `cash_in` `cash_in` decimal(10, 2) default 0.0;
alter table price_record change `cash_out` `cash_out` decimal(10, 2) default 0.0;



-- 20200923 stage_client_task
CREATE TABLE IF NOT EXISTS `project_stage_client_task_comment` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


ALTER TABLE project_proof
ADD COLUMN `proof_remark` varchar(2048) DEFAULT '' AFTER status;

-- 20201001 add holiday location
ALTER TABLE holiday
ADD COLUMN `location` varchar(64) DEFAULT '' AFTER `year`;

-- 20201005 add proof batch
ALTER TABLE project_proof
ADD COLUMN `batch_id` int(11) DEFAULT 0 AFTER project_id;


-- 20201019 stage_other_task
CREATE TABLE IF NOT EXISTS `project_other_task` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20201020 project_other_task_message
CREATE TABLE IF NOT EXISTS `project_other_task_message` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20201005 add project_other_task_message reply
ALTER TABLE project_other_task_message
ADD COLUMN `parent_id` int(11) DEFAULT 0 AFTER task_id;

-- 20201028 add project_other_task_message reply
ALTER TABLE project_other_task_message_reply
ADD COLUMN `reply_id` int(11) DEFAULT 0 AFTER message_id;



-- 20201030stage_other_task
CREATE TABLE IF NOT EXISTS `project_other_task_r` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_r` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_r` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE project_other_task_message_r
ADD COLUMN `parent_id` int(11) DEFAULT 0 AFTER task_id;

ALTER TABLE project_other_task_message_reply_r
ADD COLUMN `reply_id` int(11) DEFAULT 0 AFTER message_id;

CREATE TABLE IF NOT EXISTS `work_calendar_main` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci  default '',
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `color` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `text_color` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `sales_executive` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `project_in_charge` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `installer_needed` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `installer_needed_location` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `things_to_bring` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `things_to_bring_location` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `products_to_bring` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `products_to_bring_files` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `service` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `driver` varchar(200) COLLATE utf8mb4_unicode_ci  default '',
  `back_up_driver` varchar(200) COLLATE utf8mb4_unicode_ci  default '',
  `photoshoot_request` bool default false,
  `notes` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_enabled` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `work_calendar_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci  default '',
  `appoint_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `agenda` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_enabled` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `work_calendar_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `is_enabled` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `work_calendar_meetings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(128) COLLATE utf8mb4_unicode_ci  default '',
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `attendee` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `is_enabled` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


ALTER TABLE `work_calendar_main` ADD `all_day` BOOLEAN DEFAULT FALSE AFTER `title`;
ALTER TABLE `work_calendar_details` ADD `main_id` bigint(20) DEFAULT 0 AFTER `id`;


ALTER TABLE `gcp_storage_file` ADD `deleted_id` int(11) DEFAULT 0 AFTER `updated_at`;
ALTER TABLE `gcp_storage_file` ADD `deleted_at` timestamp NULL AFTER `deleted_id`;

ALTER TABLE `work_calendar_details` ADD `sort` int(11) DEFAULT 0 AFTER `agenda`;


-- 20201210 project02_1 modified
ALTER TABLE project_main
ADD COLUMN `client` varchar(256) DEFAULT '' AFTER contact_number;

CREATE TABLE IF NOT EXISTS `project_quotation` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `doc_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE project_quotation
ADD COLUMN `batch_id` int(11) DEFAULT 0 AFTER project_id;


ALTER TABLE apply_for_leave
ADD COLUMN `too_many` varchar(1) DEFAULT '' AFTER `leave`;

-- 20201217 calendar with lock modified
ALTER TABLE work_calendar_main
ADD COLUMN `lock` varchar(1) COLLATE utf8mb4_unicode_ci  default '' AFTER `notes`;

-- 20201230 project02 extend
ALTER TABLE project_main
ADD COLUMN `client_name` varchar(512) DEFAULT '' AFTER client;

ALTER TABLE project_main
ADD COLUMN `designer` varchar(512) DEFAULT '' AFTER client;

ALTER TABLE project_main
ADD COLUMN `type` varchar(512) DEFAULT '' AFTER client;

ALTER TABLE project_main
ADD COLUMN `scope` varchar(512) DEFAULT '' AFTER client;

ALTER TABLE project_main
ADD COLUMN `office_location` varchar(512) DEFAULT '' AFTER client;

ALTER TABLE project_main
ADD COLUMN `background_client` varchar(512) DEFAULT '' AFTER client;

ALTER TABLE project_main
ADD COLUMN `background_project` varchar(512) DEFAULT '' AFTER client;

CREATE TABLE IF NOT EXISTS `project_party_contactor` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `type` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `number` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_key_person` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `type` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `number` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20210101 project02 extend
ALTER TABLE project_main  DROP COLUMN client_name;

ALTER TABLE project_main
ADD COLUMN `contractor` varchar(512) DEFAULT '' AFTER client;

-- 20210112 add void id for apply_for_leave
ALTER TABLE apply_for_leave
ADD COLUMN `void_id`  int(11) DEFAULT 0 after re_reject_at;

ALTER TABLE apply_for_leave
ADD COLUMN `void_at`   timestamp NULL DEFAULT NULL after void_id;

-- 20210118 expense_flow
CREATE TABLE IF NOT EXISTS `expense_flow` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `apartment_id` bigint(20) unsigned NOT NULL,
  `flow` bigint(20) default 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';



CREATE TABLE IF NOT EXISTS `apply_for_petty` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `request_no` varchar(10) COLLATE utf8mb4_unicode_ci default '',
  `date_requested` varchar(20) COLLATE utf8mb4_unicode_ci default '',
  `request_type` varchar(10) COLLATE utf8mb4_unicode_ci default '',
  `project_name` varchar(512) COLLATE utf8mb4_unicode_ci default '',
  `payable_to` bigint(20) unsigned NOT NULL,
  `payable_other` varchar(512) COLLATE utf8mb4_unicode_ci default '',
  `remark` varchar(512) COLLATE utf8mb4_unicode_ci default '',
  `approval_id` bigint(20) unsigned default 0,
  `approval_at` timestamp NULL DEFAULT NULL,
  `reject_reason` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `reject_at` timestamp NULL DEFAULT NULL,
  `re_approval_id` bigint(20) unsigned default 0,
  `re_approval_at` timestamp NULL DEFAULT NULL,
  `re_reject_reason` varchar(1024) COLLATE utf8mb4_unicode_ci default '',
  `re_reject_at` timestamp NULL DEFAULT NULL,

  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `petty_list` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `petty_id` bigint(20)  DEFAULT 0 NOT NULL,
  `sn` int(11) DEFAULT 0,
  `payee` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `particulars` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `price` decimal(10, 2) default 0.0,
  `qty` int(11) DEFAULT 0,

  `status` int(11) DEFAULT 0,

  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 2021/02/03 for petty history
CREATE TABLE IF NOT EXISTS `petty_history` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `petty_id` bigint(20)  DEFAULT 0 NOT NULL,
  `actor` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `action` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `reason` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',

  `status` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE apply_for_petty
ADD COLUMN `info_account` varchar(512) DEFAULT '' AFTER remark;
ALTER TABLE apply_for_petty
ADD COLUMN `info_category` varchar(512) DEFAULT '' AFTER info_account;
ALTER TABLE apply_for_petty
ADD COLUMN `info_sub_category` varchar(512) DEFAULT '' AFTER info_category;
ALTER TABLE apply_for_petty
ADD COLUMN `info_remark` varchar(512) DEFAULT '' AFTER info_sub_category;

-- 
ALTER TABLE apply_for_petty
ADD COLUMN `amount_liquidated` decimal(10, 2) default 0.0 AFTER info_remark;

--
ALTER TABLE apply_for_petty
ADD COLUMN `amount_verified` decimal(10, 2) default 0.0 AFTER amount_liquidated;

--
ALTER TABLE apply_for_petty
ADD COLUMN `remark_liquidated` varchar(512) DEFAULT '' AFTER amount_liquidated;


ALTER TABLE apply_for_petty MODIFY COLUMN amount_liquidated decimal(10, 2) NULL DEFAULT NULL;

ALTER TABLE apply_for_petty MODIFY COLUMN amount_verified decimal(10, 2) NULL DEFAULT NULL;

-- apply for petty to price record
ALTER TABLE price_record
ADD COLUMN `gcp_url` varchar(1024)  default '' AFTER pic_url;

-- 
ALTER TABLE apply_for_petty
ADD COLUMN `info_remark_other` varchar(512) DEFAULT '' AFTER info_remark;

-- 2021/3/11 price record add project_name
ALTER TABLE price_record
ADD COLUMN `project_name` varchar(1024)  default '' AFTER sub_category;

-- 2021/03/15 work_calendar_main
ALTER TABLE work_calendar_main
ADD COLUMN `installer_needed_other` varchar(1024)  default '' AFTER installer_needed;

ALTER TABLE work_calendar_main
ADD COLUMN `driver_other` varchar(1024)  default '' AFTER back_up_driver;

ALTER TABLE work_calendar_main
ADD COLUMN `color_other` varchar(100)  default '' AFTER color;

ALTER TABLE work_calendar_main
ADD COLUMN `back_up_driver_other` varchar(1024)  default '' AFTER driver_other;


-- 頁面 和 郵件內容 調整 2021/3/25
ALTER TABLE apply_for_petty
ADD COLUMN `project_name1`  varchar(512) DEFAULT '' AFTER project_name;


-- quotation and payment
ALTER TABLE project_main
ADD COLUMN `final_amount`  decimal(10, 2) null default null AFTER close_reason;

ALTER TABLE project_quotation
ADD COLUMN `final_quotation` int(11) DEFAULT 0 AFTER project_id;

ALTER TABLE project_proof
ADD COLUMN `received_date` varchar(10) DEFAULT '' AFTER status;

ALTER TABLE project_proof
ADD COLUMN `kind` int(11) DEFAULT 0 AFTER status;

ALTER TABLE project_proof
ADD COLUMN `amount` decimal(10, 2) null default null AFTER received_date;

ALTER TABLE project_proof
ADD COLUMN `invoice` varchar(512) DEFAULT '' AFTER amount;

ALTER TABLE project_proof
ADD COLUMN `detail` varchar(512) DEFAULT '' AFTER invoice;

ALTER TABLE project_proof
ADD COLUMN `checked` int(11) DEFAULT 0 AFTER detail;

ALTER TABLE project_proof
ADD COLUMN `checked_id` int(11) DEFAULT 0 AFTER checked;

ALTER TABLE project_proof
ADD COLUMN `checked_at` timestamp null AFTER checked_id;

-- project approve plan
CREATE TABLE IF NOT EXISTS `project_approve` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `batch_id` int(11) DEFAULT 0 NOT NULL,
  `final_approve` int(11) DEFAULT 0 NOT NULL,
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- Meeting Calendar 2021/4/19
ALTER TABLE work_calendar_meetings
ADD COLUMN `project_name` varchar(512) COLLATE utf8mb4_unicode_ci default '' AFTER subject;

-- Task Calendar 2021/4/21
ALTER TABLE project_other_task
ADD COLUMN `due_time` varchar(10) COLLATE utf8mb4_unicode_ci default '' AFTER `due_date`;

-- performance view
CREATE TABLE IF NOT EXISTS `performance_template` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `title_id` bigint(20)  DEFAULT 0 NOT NULL,
  `version`  varchar(512) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `performance_template_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20)  DEFAULT 0 NOT NULL,
  `type` int(11) DEFAULT 0 NOT NULL,
  `order` int(11) DEFAULT 0 NOT NULL,
  `category`  varchar(2048) DEFAULT '',
  `criterion`  varchar(2048) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- performance view
CREATE TABLE IF NOT EXISTS `performance_review` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20)  DEFAULT 0 NOT NULL,
  `user_id` bigint(20)  DEFAULT 0 NOT NULL,
  `review_month`  varchar(20) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `user_complete_at` timestamp NULL,
  `manager_complete_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE performance_review
ADD COLUMN `emp_comment_1` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `review_month`;

ALTER TABLE performance_review
ADD COLUMN `emp_comment_2` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `emp_comment_1`;

ALTER TABLE performance_review
ADD COLUMN `emp_comment_3` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `emp_comment_2`;

ALTER TABLE performance_review
ADD COLUMN `mag_comment_1` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `emp_comment_3`;

ALTER TABLE performance_review
ADD COLUMN `mag_comment_2` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `mag_comment_1`;

ALTER TABLE performance_review
ADD COLUMN `mag_comment_3` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `mag_comment_2`;




CREATE TABLE IF NOT EXISTS `performance_review_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `review_id` bigint(20)  DEFAULT 0 NOT NULL,
  `review_type` int(11) DEFAULT 0 NOT NULL,
  `review_question_id` bigint(20)  DEFAULT 0 NOT NULL,
  `score` int(11) DEFAULT 0 NOT NULL,
  `option`  varchar(2048) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- user improvement
ALTER TABLE user
ADD COLUMN `updated_id`  int(11) DEFAULT 0 after created_at;

ALTER TABLE user
ADD COLUMN `created_id`  int(11) DEFAULT 0 after pic_url;


-- performance view part II
ALTER TABLE performance_review
ADD COLUMN `emp_comment_4` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `emp_comment_3`;

ALTER TABLE performance_review
ADD COLUMN `emp_comment_5` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `emp_comment_4`;

ALTER TABLE performance_review
ADD COLUMN `mag_comment_4` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `mag_comment_3`;

ALTER TABLE performance_review
ADD COLUMN `mag_comment_5` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `mag_comment_4`;


-- template library
CREATE TABLE IF NOT EXISTS `template_library` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `title_id` bigint(20)  DEFAULT 0 NOT NULL,
  `salary`  varchar(512) DEFAULT '',
  `kpi`  varchar(1024) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- forget password 
CREATE TABLE IF NOT EXISTS `password_reset` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `email`  varchar(512) DEFAULT '',
  `token`  varchar(512) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- expense checking 05/21
ALTER TABLE petty_list
ADD COLUMN `check_remark` varchar(512)  COLLATE utf8mb4_unicode_ci default '' AFTER `qty`;



-- 20210528 task_management
CREATE TABLE IF NOT EXISTS `project_other_task_a` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_a` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_a` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 2021/06/07 擴充評論
ALTER TABLE performance_review MODIFY `mag_comment_1` varchar(2048);
ALTER TABLE performance_review MODIFY `mag_comment_2` varchar(2048);
ALTER TABLE performance_review MODIFY `mag_comment_3` varchar(2048);
ALTER TABLE performance_review MODIFY `mag_comment_4` varchar(2048);
ALTER TABLE performance_review MODIFY `mag_comment_5` varchar(2048);

-- 2021/06/08 擴充描述
ALTER TABLE price_record MODIFY `details` varchar(4096);



-- 20210609 task_management for design
CREATE TABLE IF NOT EXISTS `project_other_task_d` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_d` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_d` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- quotation and management v2

ALTER TABLE project_main
ADD COLUMN `tax_withheld` decimal(11, 2) null default null AFTER final_amount;

ALTER TABLE project_main
ADD COLUMN `billing_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER tax_withheld;

CREATE TABLE IF NOT EXISTS `project_client_po` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `batch_id` int(11) DEFAULT 0 NOT NULL,
  `kind` int(11) DEFAULT 0 NOT NULL,
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE project_main
MODIFY  `tax_withheld` decimal(11, 2) null default NULL;

UPDATE project_main SET tax_withheld = NULL WHERE tax_withheld = 0.0;




-- 20210609 task_management for lighting
CREATE TABLE IF NOT EXISTS `project_other_task_l` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_l` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_l` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';




-- 20210609 task_management for office system
CREATE TABLE IF NOT EXISTS `project_other_task_o` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_o` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_o` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20210609 task_management for sales
CREATE TABLE IF NOT EXISTS `project_other_task_sl` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_sl` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_sl` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20210609 task_management for service
CREATE TABLE IF NOT EXISTS `project_other_task_sv` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_sv` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_sv` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- add relevant for scheduled calendar
ALTER TABLE work_calendar_main
ADD COLUMN `project_relevant` varchar(512)  default '' AFTER project_in_charge;

-- performance_review
ALTER TABLE performance_review
ADD COLUMN `period` int(11)  DEFAULT 0 AFTER `review_month`;

-- project 02 scope extend 07/13
ALTER TABLE project_main
ADD COLUMN `scope_other` varchar(256) DEFAULT '' AFTER scope;

-- Meeting Calendar 2021/7/19
ALTER TABLE work_calendar_meetings
ADD COLUMN `location` varchar(256) COLLATE utf8mb4_unicode_ci default '' AFTER end_time;

-- Product Attributes 2021/07/20200704
CREATE TABLE IF NOT EXISTS `product_category_attribute` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `cat_id` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `level` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `category` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `product_category_attribute_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `cat_id` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sn` int(11) DEFAULT 0,
  `option` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

insert into product_category_attribute(cat_id, level, category, create_id) values('10000000', 1, 'Lighting', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20000000', 1, 'Systems Furniture', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010000', 2, 'Indoor', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020000', 2, 'Outdoor', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030000', 2, 'Accessory', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010000', 2, 'Cabinet', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020000', 2, 'Chair', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030000', 2, 'Table', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040000', 2, 'Workstation', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050000', 2, 'Partition', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010001', 3, 'Beam Angle', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010002', 3, 'Lumens', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010003', 3, 'CRI / RA', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010004', 3, 'CCT', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010005', 3, 'Wattage', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010006', 3, 'IP Rating', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010007', 3, 'Life Hours', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010008', 3, 'Color Finish', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010009', 3, 'Body Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010010', 3, 'Trim Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010011', 3, 'Installation', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010012', 3, 'Dimension', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010013', 3, 'Materials', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010014', 3, 'Light Source', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010015', 3, 'Power Input', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010016', 3, 'Switch', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10010017', 3, 'Net Weight', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020001', 3, 'Beam Angle', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020002', 3, 'Lumens', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020003', 3, 'CRI / RA', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020004', 3, 'CCT', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020005', 3, 'Wattage', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020006', 3, 'IP Rating', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020007', 3, 'Life Hours', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020008', 3, 'Color Finish', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020009', 3, 'Body Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020010', 3, 'Trim Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020011', 3, 'Installation', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020012', 3, 'Dimension', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020013', 3, 'Materials', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020014', 3, 'Light Source', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020015', 3, 'Power Input', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020016', 3, 'Switch', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10020017', 3, 'Net Weight', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030001', 3, 'Beam Angle', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030002', 3, 'Lumens', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030003', 3, 'CRI / RA', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030004', 3, 'CCT', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030005', 3, 'Wattage', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030006', 3, 'IP Rating', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030007', 3, 'Life Hours', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030008', 3, 'Color Finish', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030009', 3, 'Body Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030010', 3, 'Trim Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030011', 3, 'Installation', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030012', 3, 'Dimension', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030013', 3, 'Materials', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030014', 3, 'Light Source', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030015', 3, 'Power Input', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030016', 3, 'Switch', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('10030017', 3, 'Net Weight', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010001', 3, 'Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010002', 3, 'Dimension', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010003', 3, 'Finish', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010004', 3, 'Available Color', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010005', 3, 'Handle Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010006', 3, 'Lockset', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010007', 3, 'Capacity', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010008', 3, 'No. of Shelves', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010009', 3, 'No. of Drawers', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010010', 3, 'No. of Doors', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010011', 3, 'Material of Body', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010012', 3, 'Material of Shelf', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010013', 3, 'Material of Door', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010014', 3, 'Material of Drawer', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010015', 3, 'Adjustable Shelves', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010016', 3, 'Center Divider', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20010017', 3, 'Wheel Caster', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020001', 3, 'Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020002', 3, 'Function', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020003', 3, 'Dimension of Overall', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020004', 3, 'Dimension of Seat Height', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020005', 3, 'Dimension of Backrest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020006', 3, 'Dimension of Seat', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020007', 3, 'Available Color of Head Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020008', 3, 'Available Color of Back Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020009', 3, 'Available Color of Frame', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020010', 3, 'Available Color of Arm Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020011', 3, 'Available Color of Foot Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020012', 3, 'Available Color of Seat', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020013', 3, 'Available Color of Base', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020014', 3, 'Materials of Head Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020015', 3, 'Materials of Back Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020016', 3, 'Materials of Arm Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020017', 3, 'Materials of Foot Rest', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020018', 3, 'Materials of Seat', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020019', 3, 'Materials of Base', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020020', 3, 'With Headrest   ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020021', 3, 'Adjustable Headrest     ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020022', 3, 'Seat Height Adjustment  ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020023', 3, 'Adjustable Armrest      ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020024', 3, 'No Armrest Variant      ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020025', 3, 'Back Height Adjustment  ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020026', 3, 'Back Angle Adjustment with Tilt Lock    ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020027', 3, 'Back Upright Position Tilt Lock ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020028', 3, 'Lumbar Support Adjustment       ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020029', 3, 'Sliding Seat Depth Adjustment   ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020030', 3, '2-to-1 Synchro Tilt     ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20020031', 3, 'Wheel Caster    ', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030001', 3, 'Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030002', 3, 'Function', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030003', 3, 'Dimension of Width', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030004', 3, 'Dimension of Depth', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030005', 3, 'Dimension of Height', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030006', 3, 'Materials of Table Top', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030007', 3, 'Materials of Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030008', 3, 'Materials of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030009', 3, 'Materials of Modesty', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030010', 3, 'Finishes of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030011', 3, 'Finishes of Table Top and Bottom', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030012', 3, 'Finishes of Flat PVC Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030013', 3, 'Finishes of Bullnose Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030014', 3, 'Finishes of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030015', 3, 'Finishes of Modesty', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030016', 3, 'Available Color/Pattern of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030017', 3, 'Available Color/Pattern of Table Top and Bottom', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030018', 3, 'Available Color/Pattern of Flat PVC Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030019', 3, 'Available Color/Pattern of Bullnose Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030020', 3, 'Available Color/Pattern of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030021', 3, 'Available Color/Pattern of Modesty', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040001', 3, 'Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040002', 3, 'Configuration', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040003', 3, 'Partition', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040004', 3, 'Dimensions of Table: Width', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040005', 3, 'Dimensions of Table: Depth', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040006', 3, 'Dimensions of Table: Height', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040007', 3, 'Dimensions of Partition: Width', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040008', 3, 'Dimensions of Partition: Thickness', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040009', 3, 'Dimensions of Partition: Height', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040010', 3, 'Materials of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040011', 3, 'Materials of Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040012', 3, 'Materials of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040013', 3, 'Materials of Partition Frame and Capping', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040014', 3, 'Materials of Partition Panel', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040015', 3, 'Finishes of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040016', 3, 'Finishes of Table Top and Bottom', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040017', 3, 'Finishes of Flat PVC Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040018', 3, 'Finishes of Bullnose Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040019', 3, 'Finishes of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040020', 3, 'Finishes of Partition Panel', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040021', 3, 'Available Color/Pattern of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040022', 3, 'Available Color/Pattern of Table Top and Bottom', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040023', 3, 'Available Color/Pattern of Flat PVC Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040024', 3, 'Available Color/Pattern of Bullnose Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040025', 3, 'Available Color/Pattern of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040026', 3, 'Available Color/Pattern of Partition Frame and Capping', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20040027', 3, 'Available Color/Pattern of Partition Panel', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050001', 3, 'Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050002', 3, 'Dimensions of Width', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050003', 3, 'Dimensions of Thickness', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050004', 3, 'Dimensions of Height', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050005', 3, 'Materials of Frame and Capping', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050006', 3, 'Materials of Partition Panel', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050007', 3, 'Finishes of Frame and Capping', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050008', 3, 'Finishes of Partition Panel', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050009', 3, 'Available Color/Pattern of Frame and Capping', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20050010', 3, 'Available Color/Pattern of Partition Panel', 1);

-- 2021/08/10 access control
CREATE TABLE IF NOT EXISTS `access_control` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `payess1` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payess2` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `payess3` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

insert into access_control(`payess1`, `payess2`, `payess3`) values('', '', '');


-- project 01 sls  extend 08/17
ALTER TABLE project_main
ADD COLUMN `last_client_stage_id` bigint(20)  DEFAULT 0 AFTER billing_name;

ALTER TABLE project_main
ADD COLUMN `last_client_created_id` bigint(20)  DEFAULT 0 AFTER last_client_stage_id;

ALTER TABLE project_main
ADD COLUMN `last_client_created_at` timestamp DEFAULT CURRENT_TIMESTAMP AFTER last_client_created_id;

-- 2021/08/18 product
CREATE TABLE IF NOT EXISTS `product_category` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `category` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sub_category` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brand` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `code` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `price_ntd` decimal(10, 2),
  `price` decimal(10, 2),
  `description` TEXT COLLATE utf8mb4_unicode_ci,
  `photo1` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo2` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `accessory_mode` int DEFAULT 0,
  `attributes` TEXT COLLATE utf8mb4_unicode_ci,
  `variation_mode` int DEFAULT 0,
  `variation` TEXT COLLATE utf8mb4_unicode_ci,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `deleted_id` int(11) DEFAULT 0,
  `deleted_time` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `category_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `product_id` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `1st_variation` TEXT COLLATE utf8mb4_unicode_ci,
  `2rd_variation` TEXT COLLATE utf8mb4_unicode_ci,
  `3th_variation` TEXT COLLATE utf8mb4_unicode_ci,
  `code` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `price_ntd` decimal(10, 2),
  `price` decimal(10, 2),
  `enabled` int DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `deleted_id` int(11) DEFAULT 0,
  `deleted_time` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `accessory` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `category_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `product_id` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `accessory_type` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `code` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `accessory_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `price_ntd` decimal(10, 2),
  `price` decimal(10, 2),
  `enabled` int DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `deleted_id` int(11) DEFAULT 0,
  `deleted_time` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `accessory_category_attribute` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `cat_id` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `level` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `category` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


insert into accessory_category_attribute(cat_id, level, category, create_id) values('10000000', 1, 'Lighting', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20000000', 1, 'Systems Furniture', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10010000', 2, 'Indoor', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10020000', 2, 'Outdoor', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10030000', 2, 'Accessory', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20010000', 2, 'Cabinet', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20020000', 2, 'Chair', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030000', 2, 'Table', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040000', 2, 'Workstation', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20050000', 2, 'Partition', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10010001', 3, 'Lens Finish', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10010002', 3, 'Diffuser Finish', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10010003', 3, 'Reflector Color', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10010003', 3, 'Led Driver', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10020001', 3, 'Lens Finish', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10020002', 3, 'Diffuser Finish', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10020003', 3, 'Reflector Color', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10020003', 3, 'Led Driver', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10030001', 3, 'Lens Finish', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10030002', 3, 'Diffuser Finish', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10030003', 3, 'Reflector Color', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('10030003', 3, 'Led Driver', 1);

insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030001', 3, 'Socket Integration Box', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030002', 3, 'Rectangular Grommet with Brush', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030003', 3, 'Circular Grommet', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030004', 3, 'Vertical Cable Management', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030005', 3, 'Metal Wiretray', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030006', 3, 'CPU Holder', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030007', 3, 'Pencil Tray', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20030008', 3, 'Keyboard Tray', 1);

insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040001', 3, 'Socket Integration Box', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040002', 3, 'Rectangular Grommet with Brush', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040003', 3, 'Circular Grommet', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040004', 3, 'Vertical Cable Management', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040005', 3, 'Metal Wiretray', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040006', 3, 'CPU Holder', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040007', 3, 'Pencil Tray', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040008', 3, 'Keyboard Tray', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040009', 3, 'Partition Clamp', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040010', 3, 'Brackets', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20040011', 3, 'Connector', 1);

insert into accessory_category_attribute(cat_id, level, category, create_id) values('20050001', 3, 'Connector', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20050002', 3, 'Brackets', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20050003', 3, 'Partition Clamp', 1);
insert into accessory_category_attribute(cat_id, level, category, create_id) values('20050004', 3, 'Raceway', 1);


-- recent message text
ALTER TABLE project_main
ADD COLUMN `last_client_message` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER last_client_created_id;

-- add project stage
ALTER TABLE project_stage
ADD COLUMN `order` int DEFAULT 0;

INSERT into project_stage(stage) VALUES('Proposal - Mock up');
INSERT into project_stage(stage) VALUES('Proposal - Presentation');
INSERT into project_stage(stage) VALUES('Proposal - Inquiry');

update project_stage set `order`= 1 where stage = 'Client';
update project_stage set `order`= 2 where stage = 'Proposal';
update project_stage set `order`= 3 where stage = 'Proposal - Mock up';
update project_stage set `order`= 4 where stage = 'Proposal - Presentation';
update project_stage set `order`= 5 where stage = 'Proposal - Inquiry';
update project_stage set `order`= 6 where stage = 'A Meeting / Close Deal';
update project_stage set `order`= 7 where stage = 'Order';
update project_stage set `order`= 8 where stage = 'Execution Plan';
update project_stage set `order`= 9 where stage = 'Delivery';
update project_stage set `order`= 10 where stage = 'Installation';
update project_stage set `order`= 11 where stage = 'Client Feedback / After-Sales Service';

-- 08/27
ALTER TABLE product_category
ADD COLUMN `notes` TEXT COLLATE utf8mb4_unicode_ci AFTER description;

ALTER TABLE product_category
ADD COLUMN `price_ntd_change` timestamp NULL AFTER price_ntd;

ALTER TABLE product_category
ADD COLUMN `price_change` timestamp NULL AFTER price;

ALTER TABLE product
ADD COLUMN `price_ntd_change` timestamp NULL AFTER price_ntd;

ALTER TABLE product
ADD COLUMN `price_change` timestamp NULL AFTER price;

ALTER TABLE product MODIFY COLUMN price_ntd decimal(10, 2) NULL;

ALTER TABLE product MODIFY COLUMN price decimal(10, 2) NULL;

ALTER TABLE accessory MODIFY COLUMN price_ntd decimal(10, 2) NULL;

ALTER TABLE accessory MODIFY COLUMN price decimal(10, 2) NULL;


-- salary record 09/08
CREATE TABLE IF NOT EXISTS `price_record_salary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` int default 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `sub_category` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `related_account` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `details` varchar(4096) COLLATE utf8mb4_unicode_ci  default '',
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `gcp_url` varchar(1024)  COLLATE utf8mb4_unicode_ci  default '',
  `payee` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `payee_other` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `staff_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `paid_date` Date NULL DEFAULT NULL,
  `cash_in` decimal(10, 2) default 0.0,
  `cash_out` decimal(10, 2) default 0.0,
  `remarks` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `company_name` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `is_locked` bool default false,
  `is_enabled` bool default false,
  `is_marked` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE access_control
ADD COLUMN `salary` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 2021/09/09 project grouping
ALTER TABLE project_main
ADD COLUMN `group_id` bigint(20)  DEFAULT 0 AFTER last_client_created_id;

CREATE TABLE IF NOT EXISTS `project_group` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_group` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 2021/09/22 add quoted price
ALTER TABLE product_category
ADD COLUMN `quoted_price` decimal(10, 2) AFTER price;

ALTER TABLE product_category
ADD COLUMN `quoted_price_change` timestamp NULL AFTER price_change;

ALTER TABLE product_category
ADD COLUMN `moq` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER price_change;

ALTER TABLE product
ADD COLUMN `quoted_price` decimal(10, 2) AFTER price;

ALTER TABLE product
ADD COLUMN `quoted_price_change` timestamp NULL AFTER price_change;

-- 2021/09/27 add tags
ALTER TABLE product_category
ADD COLUMN `tags` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER brand;

-- 2021/10/07 send email
ALTER TABLE project_main
ADD COLUMN `send_mail` varchar(10)  COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 2021/10/07 project proof 2037
ALTER TABLE project_proof
ADD COLUMN `payment_method` varchar(16) DEFAULT '' AFTER status;

ALTER TABLE project_proof
ADD COLUMN `bank_name` varchar(256) DEFAULT '' AFTER status;

ALTER TABLE project_proof
ADD COLUMN `check_number` varchar(256) DEFAULT '' AFTER status;

ALTER TABLE project_proof
ADD COLUMN `bank_account` varchar(16) DEFAULT '' AFTER status;

-- 2021/10/19 salary mgt
CREATE TABLE IF NOT EXISTS `salary_mgt` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `salary` decimal(10, 2) default 0.0,
  `status` int(11) DEFAULT 0,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 2021/10/20 salary slip mgt
CREATE TABLE IF NOT EXISTS `salary_slip_mgt` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `status` int(11) DEFAULT 0,
  `start_date`  varchar(20) DEFAULT '',
  `end_date`  varchar(20) DEFAULT '',
  `remark` varchar(1024) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `user_complete_at` timestamp NULL,
  `manager_complete_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `salary_slip_mgt_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `salary_slip_id` bigint(20)  DEFAULT 0 NOT NULL,
  `type` int(11) DEFAULT 0 NOT NULL,
  `cust` int(11) DEFAULT 0 NOT NULL,
  `order` int(11) DEFAULT 0 NOT NULL,
  `category`  varchar(256) DEFAULT '',
  `remark`  varchar(1024) DEFAULT '',
  `amount` decimal(10, 2) default 0.0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `salary_slip_mgt_other` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `salary_slip_id` bigint(20)  DEFAULT 0 NOT NULL,
  `type` int(11) DEFAULT 0 NOT NULL,
  `order` int(11) DEFAULT 0 NOT NULL,
  `category`  varchar(256) DEFAULT '',
  `remark`  varchar(1024) DEFAULT '',
  `previous` decimal(10, 2) default 0.0,
  `payment` decimal(10, 2) default 0.0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE salary_slip_mgt
ADD COLUMN `title` varchar(255) DEFAULT '' AFTER `remark`;

ALTER TABLE salary_slip_mgt
ADD COLUMN `department` varchar(255) DEFAULT '' AFTER `remark`;

ALTER TABLE salary_slip_mgt
ADD COLUMN `salary` decimal(10, 2) default 0.0 AFTER `remark`;

ALTER TABLE access_control
ADD COLUMN `salary_mgt` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE access_control
ADD COLUMN `salary_slip_mgt` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 2021/11/30 - store sales record
create table store_sales
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sales_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sales_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `customer_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `discount` decimal(10,2) DEFAULT 0.0,
	`invoice` varchar(64) DEFAULT '',
	`remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`payment_method` varchar(64) DEFAULT '',
	`teminal` varchar(10) DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


create table store_sales_record
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sales_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`qty` int(11) DEFAULT 0,
	`price` decimal(10,2) DEFAULT 0.0,
	`free` varchar(1) DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- store sales report
ALTER TABLE access_control
ADD COLUMN `payess7` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- related_product 12/06
ALTER TABLE product_category
ADD COLUMN `related_product` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER notes;

-- a meeting
CREATE TABLE IF NOT EXISTS `project_a_meeting` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `down_payment_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `account_executive` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `contact_person` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `contact_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_address_within` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_address_outside` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `exact_delivery_address` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `detail_delivery_address` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `attached_layout` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_permit` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `work_permit` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `permit_processing_note` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `other_request` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `date_of_delivery` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_deadline` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_1st` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_1st_items` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_2nd` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_2nd_items` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `os_delivery_only` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `os_delivery_install` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `lt_delivery_only` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `lt_delivery_install` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_install` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `scope_attached_layout` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `timeline_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `timeline` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `data_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `data` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `electrical_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `electrical` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `flooring_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `flooring` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type_and_ceiling` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `painting_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `painting` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ceiling_electrical_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ceiling_electrical` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `manpower_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `manpower` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `materials_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `materials` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `trucking_services` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `purchasing_of_special_products_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `purchasing_of_special_products` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `tools_check` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `tools` varchar(384) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

ALTER TABLE project_a_meeting MODIFY COLUMN date_of_delivery varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE project_a_meeting MODIFY COLUMN client_deadline varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';


-- 2022/01/04 quotation

CREATE TABLE IF NOT EXISTS `quotation` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_category` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_no` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_date` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_third_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS quotation_page
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
	`page` int(11) DEFAULT 0,
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quotation_page_type
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `page_id` bigint(20) unsigned NOT NULL,
  `block_type` varchar(1) DEFAULT '',
  `block_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quotation_page_type_block
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `type_id` bigint(20) unsigned NOT NULL,
  `code` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` decimal(10,2) DEFAULT 0.0,
  `price` decimal(10,2) DEFAULT 0.0,
  `discount` decimal(10,2) DEFAULT 0.0,
  `amount` decimal(10,2) DEFAULT 0.0,
  `description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `listing` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quotation_total
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
	`page` int(11) DEFAULT 0,
  `discount` decimal(10,2) DEFAULT 0.0,
  `vat` varchar(2) DEFAULT '',
  `show_vat` varchar(2) DEFAULT '',
  `valid` varchar(128) DEFAULT '',
  `total` decimal(10,2) DEFAULT 0.0,
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quotation_term
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
	`page` int(11) DEFAULT 0,
  `title` varchar(512) DEFAULT '',
  `brief` varchar(512) DEFAULT '',
  `list` varchar(512) DEFAULT '',
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS quotation_signature
(
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `page` int(11) DEFAULT 0,
  `type` varchar(2) DEFAULT '',
  `photo` varchar(128) DEFAULT '',
  `name` varchar(128) DEFAULT '',
  `position` varchar(128) DEFAULT '',
  `phone` varchar(128) DEFAULT '',
  `email` varchar(128) DEFAULT '',
  `status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE quotation_page_type_block MODIFY COLUMN qty int DEFAULT 0;

ALTER TABLE quotation_page_type_block MODIFY COLUMN discount int DEFAULT 0;

ALTER TABLE quotation_total MODIFY COLUMN discount int DEFAULT 0;

ALTER TABLE quotation_total MODIFY COLUMN total decimal(12,2) DEFAULT 0.0;

ALTER TABLE quotation_page_type_block MODIFY COLUMN amount decimal(12,2) DEFAULT 0.0;

ALTER TABLE quotation_term MODIFY COLUMN brief  varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE quotation_term MODIFY COLUMN list  varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE quotation_page_type_block MODIFY COLUMN `description`  varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE quotation_page_type_block MODIFY COLUMN listing varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';


-- project01 modify
ALTER TABLE project_main
ADD COLUMN `temp_estimate_close_prob` varchar(3) DEFAULT '' AFTER estimate_close_prob;

-- init project_main estimated probility
INSERT INTO project_est_prob(project_id, comment, prob, create_id, created_at) 

select id, 'Project created', estimate_close_prob, create_id, created_at from project_main;

-- 2021/11/30 - lai sales record
create table store_sales_lai
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sales_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`company` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`client` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sales_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total_amount` decimal(10,2) DEFAULT 0.0,
	`po` varchar(128) DEFAULT '',
	`dr` varchar(128) DEFAULT '',
	`note` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


create table store_sales_record_lai
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sales_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
	`qty` int(11) DEFAULT 0,
	`price` decimal(10,2) DEFAULT 0.0,
	`free` varchar(1) DEFAULT '',
	`status` varchar(2) DEFAULT '',
	`crt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`crt_user` varchar(128) DEFAULT '',
	`mdf_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`mdf_user` varchar(128) DEFAULT '',
	`del_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	`del_user` varchar(128) DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- store sales report
ALTER TABLE access_control
ADD COLUMN `payess8` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';


-- 20220221 quotation management
ALTER TABLE quotation
ADD COLUMN `title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER id;

ALTER TABLE quotation
ADD COLUMN `project_id` bigint(20)  DEFAULT 0 AFTER title;

-- 20220324 product related
CREATE TABLE IF NOT EXISTS `product_related` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20)  DEFAULT 0 NOT NULL,
  `code` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20220406 quotation management II
CREATE TABLE IF NOT EXISTS quotation_payment_term
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
	`page` int(11) DEFAULT 0,
  `payment_method` varchar(512) DEFAULT '',
  `brief` varchar(512) DEFAULT '',
  `list` JSON,
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20220418 
ALTER TABLE quotation_page_type_block ADD COLUMN num VARCHAR(10) DEFAULT '';
ALTER TABLE quotation_page_type_block ADD COLUMN pid bigint(20) DEFAULT 0;

-- task with got INT
CREATE TABLE IF NOT EXISTS project_got_it
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) DEFAULT 0,
	`reply_id` bigint(20) DEFAULT 0,
  `kind` varchar(2) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- add phase out
ALTER TABLE product_category
ADD COLUMN `out` VARCHAR(1) DEFAULT '' COLLATE utf8mb4_unicode_ci AFTER description;

-- 20220512 user profile
ALTER TABLE user
ADD COLUMN `address`  varchar(255) DEFAULT '' after pic_url;

ALTER TABLE user
ADD COLUMN `tel`  varchar(255) DEFAULT '' after pic_url;

-- 20220516 page block
ALTER TABLE quotation_page_type
ADD COLUMN `not_show`  varchar(2) DEFAULT '' after block_name;

ALTER TABLE quotation_page_type
ADD COLUMN `real_amount` decimal(10,2) DEFAULT 0.0 after not_show;

-- 20220523
ALTER TABLE work_calendar_main
ADD COLUMN `confirm` varchar(1) COLLATE utf8mb4_unicode_ci  default '' AFTER `notes`;

-- 20220526 quotation by task_id
ALTER TABLE quotation
ADD COLUMN `kind` varchar(10)  DEFAULT '' AFTER title;

-- 202220602 project 03 client
CREATE TABLE IF NOT EXISTS `project_other_task_c` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `stage_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(256) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `due_date` varchar(10) default '',
  `due_time` varchar(10) default '',
  `assignee` varchar(256) default '',
  `collaborator` varchar(256) default '',
  `status` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT '',
  `detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_c` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `project_other_task_message_reply_c` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20)  DEFAULT 0 NOT NULL,
  `reply_id` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 202220608 project special
ALTER TABLE project_main
ADD COLUMN `special` varchar(10) DEFAULT '' AFTER contact_number;

-- 20220616 Mock up
update project_stage set `order` = `order` * 10;

insert into project_stage (stage, status, `order`) values('Proposal - Testing', 0, 35);

-- 20220620 Add currency
ALTER TABLE product_category
ADD COLUMN `currency` varchar(3) DEFAULT 'NTD' AFTER code;

update product_category set `currency` = 'NTD';

-- 20220708
ALTER TABLE quotation_page_type_block ADD COLUMN v1 VARCHAR(255) DEFAULT '';
ALTER TABLE quotation_page_type_block ADD COLUMN v2 VARCHAR(255) DEFAULT '';
ALTER TABLE quotation_page_type_block ADD COLUMN v3 VARCHAR(255) DEFAULT '';

-- 20220711 order system
CREATE TABLE IF NOT EXISTS `od_main` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `od_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `project_type` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `od_item` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `od_id` bigint(20) unsigned NOT NULL,
  `sn` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `confirm` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brand` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brand_other` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo1` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo2` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `code` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `listing` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `srp` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `date_needed` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `od_message` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `od_got_it`
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `od_process`
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `od_id` bigint(20) unsigned NOT NULL,
  `comment` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `action` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `items` JSON,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20220721 quotation ratio
ALTER TABLE quotation_page_type_block
ADD COLUMN `ratio` decimal(12,2) DEFAULT 1.0 after v3;

CREATE TABLE IF NOT EXISTS quotation_export
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `items` JSON,
  `srp` varchar(10) DEFAULT '',
  `qp` varchar(10) DEFAULT '',
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `od_message_a` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `od_got_it_a`
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE od_item
ADD COLUMN `shipping_way` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `shipping_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `eta` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `arrive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `test` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE od_item
ADD COLUMN `final` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `remark_t` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `remark_d` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `check_t` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `check_d` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20220817 access control
ALTER TABLE access_control
ADD COLUMN `access1` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE access_control
ADD COLUMN `access2` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE access_control
ADD COLUMN `access3` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE access_control
ADD COLUMN `access4` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE access_control
ADD COLUMN `access5` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE access_control
ADD COLUMN `access6` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20220829 shipping vendor
ALTER TABLE od_item
ADD COLUMN `shipping_vendor` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE od_item
ADD COLUMN `pid` bigint(20) DEFAULT 0;

ALTER TABLE od_item ADD COLUMN v1 VARCHAR(255) DEFAULT '';
ALTER TABLE od_item ADD COLUMN v2 VARCHAR(255) DEFAULT '';
ALTER TABLE od_item ADD COLUMN v3 VARCHAR(255) DEFAULT '';

-- 20220901 other task add order
drop TABLE od_main;

CREATE TABLE IF NOT EXISTS `od_main` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `od_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `task_id` bigint(20)  DEFAULT 0 NOT NULL,
  `order_type` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `serial_name` varchar(64) COLLATE utf8mb4_unicode_ci default '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 202209012
ALTER TABLE access_control
ADD COLUMN `access7` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 202209013
CREATE INDEX gcp_storage_file_batch_type_idx ON gcp_storage_file (batch_type);

CREATE INDEX on_duty_duty_date_idx ON on_duty (duty_date);

CREATE TABLE `on_duty_archive` (
  `id` bigint(20) unsigned,
  `uid` bigint(20) unsigned NOT NULL,
  `duty_date` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `duty_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `explain` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pos_lat` decimal(24,12) DEFAULT 0.000000000000,
  `pos_lng` decimal(24,12) DEFAULT 0.000000000000,
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_time` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_lat` decimal(24,12) DEFAULT 0.000000000000,
  `pic_lng` decimal(24,12) DEFAULT 0.000000000000,
  `pic_server_time` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic_server_lat` decimal(24,12) DEFAULT 0.000000000000,
  `pic_server_lng` decimal(24,12) DEFAULT 0.000000000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT 0
);

insert into on_duty_archive select * from on_duty where duty_date < '2022/01/01';

delete from on_duty where duty_date < '2022/01/01';

-- 202209012
ALTER TABLE od_main
ADD COLUMN `access7` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE project_stages
ADD COLUMN `stage_title` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20220930
CREATE TABLE `price_comparison` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `kind` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `amount` int(11) DEFAULT 0,
  `first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_category` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_no` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_date` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_third_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_first_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_second_line` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE `price_comparison_option` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `p_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sn` int(11) DEFAULT 0,
  `color` varchar(24) default '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE `price_comparison_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `p_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sn` int(11) DEFAULT 0,
  `color` varchar(24) default '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE `price_comparison_legend` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20)  DEFAULT 0 NOT NULL,
  `title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sn` int(11) DEFAULT 0,
  `color` varchar(24) default '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `price_comparison_item` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `od_id` bigint(20) unsigned NOT NULL,
  `option_id` bigint(20) unsigned NOT NULL,
  `legend_id` bigint(20) unsigned NOT NULL,
  `sn` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo1` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo2` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `code` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `list` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `price` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ratio` decimal(10,2) DEFAULT 0.0,
  `discount` decimal(10,2) DEFAULT 0.0,
  `amount` decimal(10,2) DEFAULT 0.0,
  `desc` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pid` bigint(20) DEFAULT 0,
  `v1` VARCHAR(255) DEFAULT '',
  `v2` VARCHAR(255) DEFAULT '',
  `v3` VARCHAR(255) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20221004
insert into project_stage(stage, `status`, `order`) values('Proposal - Site Visit', 0, 25);

insert into project_stage(stage, `status`, `order`) values('Order - Site Visit', 2, 75);


CREATE TABLE `price_comparison_total` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `price_id` bigint(20) unsigned NOT NULL,
  `page` int(11) DEFAULT 0,
  `discount` int(11) DEFAULT 0,
  `vat` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_vat` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `valid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total1` decimal(12,2) null,
  `total2` decimal(12,2) null,
  `total3` decimal(12,2) null,
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS price_comparison_term
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `price_id` bigint(20) unsigned NOT NULL,
	`page` int(11) DEFAULT 0,
  `title` varchar(512) DEFAULT '',
  `brief` varchar(1024) DEFAULT '',
  `list` varchar(4096) DEFAULT '',
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS price_comparison_signature
(
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `price_id` bigint(20) unsigned NOT NULL,
  `page` int(11) DEFAULT 0,
  `type` varchar(2) DEFAULT '',
  `photo` varchar(128) DEFAULT '',
  `name` varchar(128) DEFAULT '',
  `position` varchar(128) DEFAULT '',
  `phone` varchar(128) DEFAULT '',
  `email` varchar(128) DEFAULT '',
  `status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS price_comparison_payment_term
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `price_id` bigint(20) unsigned NOT NULL,
	`page` int(11) DEFAULT 0,
  `payment_method` varchar(512) DEFAULT '',
  `brief` varchar(512) DEFAULT '',
  `list` JSON,
	`status` varchar(2) DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 20221020 add photo and updated time
ALTER TABLE od_item ADD COLUMN `photo4` VARCHAR(128) DEFAULT '';
ALTER TABLE od_item ADD COLUMN `photo5` VARCHAR(128) DEFAULT '';

ALTER TABLE od_item ADD COLUMN `test_updated_name` varchar(255) DEFAULT '';
ALTER TABLE od_item ADD COLUMN `test_updated_at` timestamp NULL;

ALTER TABLE od_item ADD COLUMN `delivery_updated_name` varchar(255) DEFAULT '';
ALTER TABLE od_item ADD COLUMN `delivery_updated_at` timestamp NULL;

ALTER TABLE od_item ADD COLUMN `photo4_name` VARCHAR(128) DEFAULT '';
ALTER TABLE od_item ADD COLUMN `photo5_name` VARCHAR(128) DEFAULT '';

-- 20221025
ALTER TABLE project_other_task
ADD COLUMN `related_order` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task
ADD COLUMN `related_tab` VARCHAR(64) DEFAULT '';


ALTER TABLE project_other_task_c
ADD COLUMN `related_order` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_c
ADD COLUMN `related_tab` VARCHAR(64) DEFAULT '';

-- 20221031 performance review new comment
ALTER TABLE performance_review
ADD COLUMN `mag_comment_6` varchar(2048)  COLLATE utf8mb4_unicode_ci default '' AFTER `mag_comment_5`;
ALTER TABLE performance_review
ADD COLUMN `comment_done_id` int(11) DEFAULT 0;
ALTER TABLE performance_review
ADD COLUMN `comment_done_at` timestamp null;

-- 20221031 price comparison mgt
ALTER TABLE price_comparison
ADD COLUMN `project_id` bigint(20)  DEFAULT 0 AFTER title;

-- 20221107 leave v2
ALTER TABLE user
ADD COLUMN `leave_level` varchar(24) DEFAULT '';

ALTER TABLE user
ADD COLUMN `sil` int(11) DEFAULT 0;

ALTER TABLE user
ADD COLUMN `vl_sl` int(11) DEFAULT 0;

ALTER TABLE user
ADD COLUMN `vl` int(11) DEFAULT 0;

ALTER TABLE user
ADD COLUMN `sl` int(11) DEFAULT 0;

-- 20221108 apply_for_leave
ALTER TABLE apply_for_leave
ADD COLUMN `leave_level` varchar(24) DEFAULT '';

ALTER TABLE apply_for_leave
ADD COLUMN `sil` decimal(10, 2) default 0.0;

ALTER TABLE apply_for_leave
ADD COLUMN `vl_sl` decimal(10, 2) default 0.0;

ALTER TABLE apply_for_leave
ADD COLUMN `vl` decimal(10, 2) default 0.0;

ALTER TABLE apply_for_leave
ADD COLUMN `sl` decimal(10, 2) default 0.0;

ALTER TABLE apply_for_leave
ADD COLUMN `ul` decimal(10, 2) default 0.0;

-- 20221115 srp qp sort
ALTER TABLE product_category
ADD COLUMN `srp_max` decimal(10, 2) null;

ALTER TABLE product_category
ADD COLUMN `srp_min` decimal(10, 2) null;

ALTER TABLE product_category
ADD COLUMN `qp_max` decimal(10, 2) null;

ALTER TABLE product_category
ADD COLUMN `qp_min` decimal(10, 2) null;

update product_category pc 
set 
srp_max = price, srp_min = price, 
qp_max = quoted_price, qp_min = quoted_price;

update product_category pc set 
srp_max = (select max(p.price) from product p where p.product_id = pc.id ),
srp_min = (select min(p.price) from product p where p.product_id = pc.id ),
qp_max = (select max(p.quoted_price) from product p where p.product_id = pc.id ),
qp_min = (select min(p.quoted_price) from product p where p.product_id = pc.id ) 
 where (select count(*) from product p where p.product_id = pc.id ) > 0;

 -- 20221118
ALTER TABLE project_other_task_l
ADD COLUMN `related_order` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_l
ADD COLUMN `related_tab` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_l
ADD COLUMN `related_kind` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_l
ADD COLUMN `related_category` VARCHAR(64) DEFAULT '';

ALTER TABLE od_main
ADD COLUMN `task_type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE project_other_task_o
ADD COLUMN `related_order` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_o
ADD COLUMN `related_tab` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_o
ADD COLUMN `related_kind` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_o
ADD COLUMN `related_category` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_sl
ADD COLUMN `related_order` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_sl
ADD COLUMN `related_tab` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_sl
ADD COLUMN `related_kind` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_sl
ADD COLUMN `related_category` VARCHAR(64) DEFAULT '';

-- 20221205 inquiry taiwan
CREATE TABLE IF NOT EXISTS `iq_main` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `iq_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `task_id` bigint(20) NOT NULL DEFAULT 0,
  `order_type` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `serial_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_status` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `access7` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `task_type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `iq_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `iq_id` bigint(20) unsigned NOT NULL,
  `sn` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `confirm` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brand` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brand_other` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo1` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo2` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `code` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `listing` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `srp` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `date_needed` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipping_way` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `shipping_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `eta` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `test` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `final` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `arrive` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark_t` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remark_d` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `check_t` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `check_d` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `shipping_vendor` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pid` bigint(20) DEFAULT 0,
  `v1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `v2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `v3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo4` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo5` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `test_updated_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `test_updated_at` timestamp NULL DEFAULT NULL,
  `delivery_updated_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `delivery_updated_at` timestamp NULL DEFAULT NULL,
  `photo4_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo5_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `iq_message` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20)  DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `iq_got_it`
(
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `iq_process`
(
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `iq_id` bigint(20) unsigned NOT NULL,
  `comment` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `action` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20221208 related inquiry
ALTER TABLE project_other_task
ADD COLUMN `related_inquiry` VARCHAR(128) DEFAULT '';


-- 20221219 related inquiry for task management
ALTER TABLE project_other_task_l
ADD COLUMN `related_inquiry` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_o
ADD COLUMN `related_inquiry` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_sl
ADD COLUMN `related_inquiry` VARCHAR(128) DEFAULT '';

-- 20221227 backup qty
ALTER TABLE od_item
ADD COLUMN `backup_qty` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20221230 max and min price change
ALTER TABLE product_category
ADD COLUMN `max_price_change` timestamp NULL;

ALTER TABLE product_category
ADD COLUMN `min_price_change` timestamp NULL;

ALTER TABLE product_category
ADD COLUMN `max_price_ntd_change` timestamp NULL;

ALTER TABLE product_category
ADD COLUMN `min_price_ntd_change` timestamp NULL;

ALTER TABLE product_category
ADD COLUMN `max_quoted_price_change` timestamp NULL;

ALTER TABLE product_category
ADD COLUMN `min_quoted_price_change` timestamp NULL;

-- new
SET SQL_MODE='ALLOW_INVALID_DATES';
update product_category 
INNER JOIN 
(
select pc.id, pc.variation_mode,
max(p.price_change) max_p, min(Coalesce(p.price_change, '1000-01-01 00:00:00')) min_p, 
max(p.price_ntd_change) max_np, min(Coalesce(p.price_ntd_change, '1000-01-01 00:00:00')) min_np,
max(p.quoted_price_change) max_qp, min(Coalesce(p.quoted_price_change, '1000-01-01 00:00:00')) min_qp 
from product_category pc 
left join product p on pc.id = p.product_id 
group by pc.id having pc.variation_mode = 1) 
op ON product_category.id=op.id 
set 
max_price_change = op.max_p,
min_price_change = case when op.min_p = '1000-01-01 00:00:00' then null else op.min_p end,
max_price_ntd_change = op.max_np,
min_price_ntd_change =  case when op.min_np = '1000-01-01 00:00:00' then null else op.min_np end, 
max_quoted_price_change =  op.max_qp,
min_quoted_price_change =   case when op.min_qp = '1000-01-01 00:00:00' then null else op.min_qp end
where op.id = product_category.id;

update product_category 
set 
max_price_change = price_change,
min_price_change = price_change,
max_price_ntd_change = price_ntd_change,
min_price_ntd_change =  price_ntd_change, 
max_quoted_price_change = quoted_price_change,
min_quoted_price_change = quoted_price_change
where variation_mode = 0;

-- 20230103 add column product phased out count
ALTER TABLE product_category
ADD COLUMN `phased_out_cnt`  int(11) DEFAULT 0;


update product_category 
INNER JOIN 
(
    select a_group.id, COALESCE(a_group.cnt, 0) cnt, COALESCE(e_group.cnt, 0) ecnt from
    (
        select pc.id,
        count(p.enabled) cnt
        from product_category pc 
        left join product p on pc.id = p.product_id 
        where pc.variation_mode = 1
        group by pc.id  
    ) a_group
    left join
    (
        select pc.id,
        count(p.enabled) cnt
        from product_category pc 
        left join product p on pc.id = p.product_id 
        where p.enabled = 1 and pc.variation_mode = 1
        group by pc.id
    ) e_group
    on a_group.id = e_group.id
) op 
ON product_category.id=op.id 
set 
phased_out_cnt = op.cnt - op.ecnt
where op.id = product_category.id;

update product_category set phased_out_cnt = 0 where variation_mode = 0;

-- for production
update product_category INNER JOIN ( select a_group.id, COALESCE(a_group.cnt, 0) cnt, COALESCE(e_group.cnt, 0) ecnt from (  select pc.id,  count(p.enabled) cnt  from product_category pc   left join product p on pc.id = p.product_id   where pc.variation_mode = 1  group by pc.id   ) a_group left join (  select pc.id,  count(p.enabled) cnt  from product_category pc   left join product p on pc.id = p.product_id   where p.enabled = 1 and pc.variation_mode = 1  group by pc.id ) e_group on a_group.id = e_group.id) op ON product_category.id=op.id set phased_out_cnt = op.cnt - op.ecnt where op.id = product_category.id;
update product_category set phased_out_cnt = 0 where variation_mode = 0;


-- 20230109 knowledge
CREATE TABLE IF NOT EXISTS `knowledge` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `cover` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `category` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `access` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `link` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `attach` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `watch` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `duration` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `desciption` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `deleted_id` int(11) DEFAULT 0,
  `deleted_time` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `gtag` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `tag` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sn` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- sample data
insert into tags(`gtag`, tag, sn) values('BY INSTALL LOCATION', 'BLDG. FAÇADE', 10);
insert into tags(`gtag`, tag, sn) values('BY INSTALL LOCATION', 'CABINET', 20);
insert into tags(`gtag`, tag, sn) values('BY INSTALL LOCATION', 'CEILING', 30);
insert into tags(`gtag`, tag, sn) values('INSTALL METHOD', 'POLE-MOUNTED', 40);
insert into tags(`gtag`, tag, sn) values('INSTALL METHOD', 'RECESSED', 50);
insert into tags(`gtag`, tag, sn) values('INSTALL METHOD', 'STAND-ALONE', 60);
insert into tags(`gtag`, tag, sn) values('BY TYPE / FUNCTION', 'ALUMINUM PROFILE', 70);
insert into tags(`gtag`, tag, sn) values('BY TYPE / FUNCTION', 'ASSEMBLED', 80);
insert into tags(`gtag`, tag, sn) values('BY TYPE / FUNCTION', 'AUDIO EQUIPMENT', 90);

-- 20230210
ALTER TABLE access_control
ADD COLUMN `knowledge` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20230222

-- voting view
CREATE TABLE IF NOT EXISTS `voting_template` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `topic`  varchar(512) DEFAULT '',
  `access` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `start_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `end_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `rule` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `display` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sort` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `voting_template_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20)  DEFAULT 0 NOT NULL,
  `sn` int(11) DEFAULT 0 NOT NULL,
  `title`  varchar(512) DEFAULT '',
  `pic`  varchar(512) DEFAULT '',
  `description`  varchar(2048) DEFAULT '',
  `link` varchar(512) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- voting view
CREATE TABLE IF NOT EXISTS `voting_review` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20)  DEFAULT 0 NOT NULL,
  `user_id` bigint(20)  DEFAULT 0 NOT NULL,
  `review_month`  varchar(20) DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `user_complete_at` timestamp NULL,
  `manager_complete_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `voting_review_detail` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20)  DEFAULT 0 NOT NULL,
  `review_id` bigint(20)  DEFAULT 0 NOT NULL,
  `review_question_id` bigint(20)  DEFAULT 0 NOT NULL,
  `answer`  varchar(512) DEFAULT '',
  `score` int(11) DEFAULT 0 NOT NULL,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20230302 access
ALTER TABLE access_control
ADD COLUMN `vote1` text COLLATE utf8mb4_unicode_ci;

ALTER TABLE access_control
ADD COLUMN `vote2` text COLLATE utf8mb4_unicode_ci;

-- 20230309 print option
ALTER TABLE product_category
ADD COLUMN `print_option` JSON;

-- 20230314
ALTER TABLE quotation_page_type_block
ADD COLUMN `photo2` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE quotation_page_type_block
ADD COLUMN `photo3` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20230315
ALTER TABLE quotation_export
ADD COLUMN `pid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';

ALTER TABLE quotation_export
ADD COLUMN `brand` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20230317
CREATE TABLE IF NOT EXISTS `signature_codebook` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic_url` varchar(1024) COLLATE utf8mb4_unicode_ci  NULL,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20230322 
ALTER TABLE price_comparison_term ADD COLUMN `pixel` VARCHAR(16) DEFAULT '';
ALTER TABLE price_comparison_signature ADD COLUMN `pixel` VARCHAR(16) DEFAULT '';
ALTER TABLE price_comparison_payment_term ADD COLUMN `pixel` VARCHAR(16) DEFAULT '';

-- 20230410
ALTER TABLE od_item ADD COLUMN `btn2` VARCHAR(5) DEFAULT '';

-- 20230414 duplicate product
INSERT INTO product_category
(category, sub_category, brand, `code`, price_ntd, price, `description`, 
photo1, photo2, photo3, accessory_mode, attributes, variation_mode, variation, notes, price_ntd_change, 
price_change, quoted_price, quoted_price_change, moq, `tags`, related_product, `OUT`, currency, srp_max, 
srp_min, qp_max, qp_min, max_price_change, min_price_change, max_price_ntd_change, min_price_ntd_change, 
max_quoted_price_change, min_quoted_price_change, phased_out_cnt, print_option, create_id)
SELECT category, sub_category, brand, `code`, price_ntd, price, `description`, 
photo1, photo2, photo3, accessory_mode, attributes, variation_mode, variation, notes, price_ntd_change, 
price_change, quoted_price, quoted_price_change, moq, `tags`, related_product, `OUT`, currency, srp_max, 
srp_min, qp_max, qp_min, max_price_change, min_price_change, max_price_ntd_change, min_price_ntd_change, 
max_quoted_price_change, min_quoted_price_change, phased_out_cnt, print_option, create_id FROM 
product_category WHERE id = 1578;

INSERT INTO product (category_id, 1st_variation, 2rd_variation, 3th_variation, 
`code`, photo, price_ntd, price, price_ntd_change, price_change, enabled, 
quoted_price, quoted_price_change, `status`, create_id, product_id)
SELECT category_id, 1st_variation, 2rd_variation, 3th_variation, 
`code`, photo, price_ntd, price, price_ntd_change, price_change, enabled, 
quoted_price, quoted_price_change, `status`, create_id, 1583 FROM product
where product_id = 1578;

update product_category set price_change = '2023-01-20 00:00:00', max_price_change = '2023-01-20 00:00:00', min_price_change = '2023-01-20 00:00:00' where id = 1628;

-- 20230424
delete from product_category_attribute where cat_id like '2003%' and level = 3;

insert into product_category_attribute(cat_id, level, category, create_id) values('20030001', 3, 'Type', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030002', 3, 'Function', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030003', 3, 'Dimension of Main', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030004', 3, 'Dimension of Side', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030005', 3, 'Dimension of Table Top', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030006', 3, 'Dimension of Width', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030007', 3, 'Dimension of Depth', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030008', 3, 'Dimension of Height', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030009', 3, 'Materials of Table Top', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030010', 3, 'Materials of Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030011', 3, 'Materials of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030012', 3, 'Materials of Modesty', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030013', 3, 'Finishes of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030014', 3, 'Finishes of Table Top and Bottom', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030015', 3, 'Finishes of Flat PVC Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030016', 3, 'Finishes of Bullnose Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030017', 3, 'Finishes of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030018', 3, 'Finishes of Modesty', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030019', 3, 'Available Color/Pattern of Table Top Only', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030020', 3, 'Available Color/Pattern of Table Top and Bottom', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030021', 3, 'Available Color/Pattern of Flat PVC Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030022', 3, 'Available Color/Pattern of Bullnose Edging', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030023', 3, 'Available Color/Pattern of Legs', 1);
insert into product_category_attribute(cat_id, level, category, create_id) values('20030024', 3, 'Available Color/Pattern of Modesty', 1);

-- 20230426 PIC1 PIC2
ALTER TABLE project_main ADD COLUMN `pic1` int(11) DEFAULT 0;
ALTER TABLE project_main ADD COLUMN `pic2` int(11) DEFAULT 0;

-- 20230428 quotation notes
ALTER TABLE quotation_page_type_block ADD COLUMN `notes` varchar(512) DEFAULT NULL;

-- 20230502
ALTER TABLE apply_for_petty
ADD COLUMN `rtype` varchar(10) DEFAULT '';

ALTER TABLE apply_for_petty
ADD COLUMN `dept_name` varchar(24) DEFAULT '';

-- 20230504 user address
ALTER TABLE `user` CHANGE `address` `date_start_company` VARCHAR(10) DEFAULT '';

ALTER TABLE `user`
ADD COLUMN `seniority` int(11) DEFAULT 0;

-- 20230508
ALTER TABLE `work_calendar_main` ADD `related_project_id` bigint(20) DEFAULT 0;
ALTER TABLE `work_calendar_main` ADD `related_stage_id` bigint(20) DEFAULT 0;

-- 20230510
ALTER TABLE `project_main` ADD COLUMN `archive` int(11) DEFAULT 0;

-- 20230517
CREATE INDEX work_calendar_details_is_enabled_IDX USING BTREE ON work_calendar_details (main_id, is_enabled);

-- 20230522
ALTER TABLE price_comparison_total ADD COLUMN `show_t` VARCHAR(10) DEFAULT '';

-- 20230524
mysqldump -u root -p feliix product_category > product_category.sql
mysqldump -u root -p feliix product > product.sql

update product_category
set quoted_price = CEIL(price * 1.15), quoted_price_change = '2023-05-26 00:00:10'
where price is not null and category = '10000000';


update product p left join product_category pc on p.product_id = pc.id
set p.quoted_price = CEIL(p.price * 1.15), p.quoted_price_change = '2023-05-26 00:00:10'
where p.price is not null  and pc.category = '10000000';

update product_category pc 
set 
max_quoted_price_change = now(), min_quoted_price_change = now(), 
qp_max = quoted_price, qp_min = quoted_price
where quoted_price_change = '2023-05-26 00:00:10';


update product_category pc set 
max_quoted_price_change = now(), min_quoted_price_change = now(), 
qp_max = (select max(p.quoted_price) from product p where p.product_id = pc.id ),
qp_min = (select min(p.quoted_price) from product p where p.product_id = pc.id ) 
 where (select count(*) from product p where p.product_id = pc.id and quoted_price_change = '2023-05-26 00:00:10' ) > 0;

-- 20230302 access
ALTER TABLE access_control
ADD COLUMN `schedule_confirm` text COLLATE utf8mb4_unicode_ci;

-- 20230601
CREATE TABLE IF NOT EXISTS `product_spec_sheet` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20)  DEFAULT 0 NOT NULL,
  `code` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `legend` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `option` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `category` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `indoor` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `grade` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo1` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo2` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo4` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo5` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo6` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci,
  `variation` text,
  `related_product` JSON,
  `reserved` JSON,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20230602
alter table project_stage_client_task_comment MODIFY COLUMN `message` VARCHAR(1536) DEFAULT '';

-- 20230612
ALTER TABLE quotation_total
ADD COLUMN `pixa` varchar(10)  DEFAULT '';

ALTER TABLE quotation_total
ADD COLUMN `show` varchar(10)  DEFAULT '';

--
ALTER TABLE quotation
ADD COLUMN `pixa_s` varchar(10)  DEFAULT '';

ALTER TABLE quotation
ADD COLUMN `show_s` varchar(10)  DEFAULT '';

ALTER TABLE quotation
ADD COLUMN `pixa_t` varchar(10)  DEFAULT '';

ALTER TABLE quotation
ADD COLUMN `show_t` varchar(10)  DEFAULT '';

ALTER TABLE quotation
ADD COLUMN `pixa_p` varchar(10)  DEFAULT '';

ALTER TABLE quotation
ADD COLUMN `show_p` varchar(10)  DEFAULT '';

-- 20230617
ALTER TABLE `quotation_page_type` ADD `pixa` varchar(10)  DEFAULT '';

-- for calendar color
update work_calendar_main set color_other = color where color_other = '';
update work_calendar_main set color = '1' where color = color_other;

-- 20230620 product_spec_sheet mgt
ALTER TABLE product_spec_sheet
ADD COLUMN `p_id` varchar(10)  DEFAULT '';

-- 20230626 approval_form 
CREATE TABLE `approval_form_quotation` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `q_id` bigint DEFAULT '0',
  `title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `kind` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_id` bigint DEFAULT '0',
  `first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_category` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_no` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_date` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_third_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int DEFAULT '0',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  `pixa_s` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_s` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pixa_t` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_t` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pixa_p` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_p` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pageless` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE `approval_form_quotation_page` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_form_quotation_page_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page_id` bigint unsigned NOT NULL,
  `block_type` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `block_name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `not_show` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `real_amount` decimal(10,2) DEFAULT '0.00',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pixa` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_form_quotation_page_type_block` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `type_id` bigint unsigned NOT NULL,
  `code` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` int DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `discount` int DEFAULT '0',
  `amount` decimal(12,2) DEFAULT '0.00',
  `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `listing` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `num` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pid` bigint DEFAULT '0',
  `v1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `v2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `v3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ratio` decimal(12,2) DEFAULT '1.00',
  `photo2` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `notes` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_form_quotation_total` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `discount` int DEFAULT '0',
  `vat` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_vat` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `valid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total` decimal(12,2) DEFAULT '0.00',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  `pixa` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_form_quotation_term` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `list` varchar(4096) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_form_quotation_signature` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `type` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `position` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_form_quotation_payment_term` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `payment_method` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `list` json DEFAULT NULL,
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE approval_form_quotation ADD COLUMN `project_name` varchar(512) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `project_location` varchar(512) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `po` varchar(64) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `request_by` varchar(128) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `request_date` varchar(24) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `submit_by` varchar(128) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `submit_date` varchar(24) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `signature_page` varchar(24) DEFAULT '';
ALTER TABLE approval_form_quotation ADD COLUMN `signature_pixel` varchar(24) DEFAULT '';

CREATE TABLE IF NOT EXISTS `approval_form_project_approve` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `batch_id` int(11) DEFAULT 0 NOT NULL,
  `final_approve` int(11) DEFAULT 0 NOT NULL,
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20230629 pageless mark
ALTER TABLE quotation
ADD COLUMN `pageless` varchar(10)  DEFAULT '';

-- 20230712 od_item column
ALTER TABLE od_item
modify COLUMN `sn` varchar(4) DEFAULT '';

-- 20230712 access
ALTER TABLE access_control
ADD COLUMN `halfday` text COLLATE utf8mb4_unicode_ci;

ALTER TABLE user
ADD COLUMN `halfday` decimal(10, 1) default 0.0;

ALTER TABLE apply_for_leave
ADD COLUMN `halfday` decimal(10, 2) default 0.0;

-- 20230726 tag management
CREATE TABLE tag_group (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `sn` int(11) DEFAULT 0,
  `group_name` VARCHAR(256) NOT NULL,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

CREATE TABLE tag_item (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `sn` int(11) DEFAULT 0,
  `group_id` bigint(20)  DEFAULT 0 NOT NULL,
  `item_name` VARCHAR(256) NOT NULL,
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';

-- 20230803 project svc
ALTER TABLE project_other_task_sv
ADD COLUMN `related_order` VARCHAR(128) DEFAULT '';

ALTER TABLE project_other_task_sv
ADD COLUMN `related_tab` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_sv
ADD COLUMN `related_kind` VARCHAR(64) DEFAULT '';

ALTER TABLE project_other_task_sv
ADD COLUMN `related_category` VARCHAR(64) DEFAULT '';

-- 20230807 access
ALTER TABLE access_control
ADD COLUMN `tag_management` text COLLATE utf8mb4_unicode_ci;

-- 20230809 notes
ALTER TABLE price_comparison_item
ADD COLUMN `notes` VARCHAR(1024) DEFAULT '';

-- Meeting Calendar 2021/7/19
ALTER TABLE work_calendar_meetings
ADD COLUMN `color` varchar(100) COLLATE utf8mb4_unicode_ci default '';

ALTER TABLE work_calendar_meetings
ADD COLUMN `text_color` varchar(100) COLLATE utf8mb4_unicode_ci default '';

ALTER TABLE work_calendar_meetings
ADD COLUMN `color_other` varchar(100) COLLATE utf8mb4_unicode_ci default '';

-- Modify Length 20230906
ALTER TABLE price_record MODIFY `pic_url` varchar(4096);

-- 20230926
update work_calendar_main set service = 'Innova' where service = '1';
update work_calendar_main set service = 'Avanza Gold' where service = '2';
update work_calendar_main set service = 'Avanza' where service = '3';
update work_calendar_main set service = 'Traviz 2' where service = '4';
update work_calendar_main set service = 'Traviz 1' where service = '5';
update work_calendar_main set service = 'Grab' where service = '6';

ALTER TABLE work_calendar_main
ADD COLUMN `status` int(11) DEFAULT 0;


ALTER TABLE work_calendar_main ADD COLUMN `requestor` text COLLATE utf8mb4_unicode_ci;

-- 20231030 od_item normal
ALTER TABLE od_item add column `normal` int(11) DEFAULT 0;

-- 20231103 individual calendar
CREATE TABLE IF NOT EXISTS `work_calendar_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(128) COLLATE utf8mb4_unicode_ci  default '',
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `attendee` varchar(1024) COLLATE utf8mb4_unicode_ci  default '',
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `is_enabled` bool default false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `project_name` varchar(512) COLLATE utf8mb4_unicode_ci  default '',
  `location` varchar(256) COLLATE utf8mb4_unicode_ci  default '',
  `color` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `text_color` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  `color_other` varchar(100) COLLATE utf8mb4_unicode_ci  default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


-- 20231113 soa
CREATE TABLE `soa_quotation` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `q_id` bigint DEFAULT '0',
  `title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `kind` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_id` bigint DEFAULT '0',
  `first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `project_category` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_no` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `quotation_date` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_for_third_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prepare_by_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `footer_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int DEFAULT '0',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  `pixa_s` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_s` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pixa_t` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_t` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pixa_p` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_p` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pageless` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE `soa_quotation_page` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soa_quotation_page_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page_id` bigint unsigned NOT NULL,
  `block_type` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `block_name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `not_show` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `real_amount` decimal(10,2) DEFAULT '0.00',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pixa` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soa_quotation_page_type_block` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `type_id` bigint unsigned NOT NULL,
  `code` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `qty` int DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `discount` int DEFAULT '0',
  `amount` decimal(12,2) DEFAULT '0.00',
  `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `listing` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `num` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pid` bigint DEFAULT '0',
  `v1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `v2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `v3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ratio` decimal(12,2) DEFAULT '1.00',
  `photo2` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo3` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `notes` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soa_quotation_total` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `discount` int DEFAULT '0',
  `vat` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show_vat` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `valid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `total` decimal(12,2) DEFAULT '0.00',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  `pixa` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `show` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soa_quotation_term` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `title` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `list` varchar(4096) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soa_quotation_signature` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `type` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `position` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soa_quotation_payment_term` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `page` int DEFAULT '0',
  `payment_method` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `brief` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `list` json DEFAULT NULL,
  `status` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `create_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE soa_quotation ADD COLUMN `project_name` varchar(128) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `project_location` varchar(128) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `po` varchar(64) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `request_by` varchar(128) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `request_date` varchar(24) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `submit_by` varchar(128) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `submit_date` varchar(24) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `signature_page` varchar(24) DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `signature_pixel` varchar(24) DEFAULT '';

CREATE TABLE IF NOT EXISTS `soa_project_approve` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `batch_id` int(11) DEFAULT 0 NOT NULL,
  `final_approve` int(11) DEFAULT 0 NOT NULL,
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `status` int(11) DEFAULT 0,
  `create_id` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_id` int(11) DEFAULT 0,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


ALTER TABLE soa_quotation ADD COLUMN `third_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `statement_date` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `mode` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `mode_content` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `caption` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `account_summary` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `caption_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `content_first_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `caption_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `content_second_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE soa_quotation ADD COLUMN `contact` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20231115 access
ALTER TABLE access_control
ADD COLUMN `soa` text COLLATE utf8mb4_unicode_ci;

-- 20231120 project01 optimize
CREATE INDEX idx_project_est_prob_project_id
ON project_est_prob (project_id);
CREATE INDEX idx_project_stages_project_id
ON project_stages (project_id);

-- 20231123ˇ project02 stages
insert into project_stage(stage, status, `order`) values('Client - 80% Pre-Order Meeting', 0, 55);
insert into project_stage(stage, status, `order`) values('Client - 90% Pre-Order Meeting', 0, 56);

ALTER TABLE project_main ADD COLUMN `target_date` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
ALTER TABLE project_main ADD COLUMN `real_date` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';

-- 20231127 project01 cache
CREATE TABLE IF NOT EXISTS `project_main_recent` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `project_name` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';


CREATE TABLE IF NOT EXISTS `project_main_recent_tmp` (
  `id` bigint(20)  NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20)  DEFAULT 0 NOT NULL,
  `project_name` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kind` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='utf8mb4_unicode_ci';