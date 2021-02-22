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
