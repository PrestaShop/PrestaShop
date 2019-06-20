SET SESSION sql_mode='';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;
DELETE FROM `PREFIX_tab` WHERE `class_name` = 'AdminLogin';
DELETE FROM `PREFIX_hook` WHERE `name` = 'actionAdminLoginControllerSetMedia';
