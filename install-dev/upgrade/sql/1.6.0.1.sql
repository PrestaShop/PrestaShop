SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_DASHBOARD_USE_PUSH', '0', NOW(), NOW());

ALTER TABLE `PREFIX_employee` CHANGE `bo_show_screencast` `bo_menu` TINYINT(1) NOT NULL DEFAULT '0';
UPDATE `PREFIX_employee` SET bo_menu = 0;
