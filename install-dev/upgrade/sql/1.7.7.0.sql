SET SESSION sql_mode = '';
SET NAMES 'utf8';

DELETE FROM `PREFIX_tab` WHERE `class_name` = 'AdminLogin';
DELETE FROM `PREFIX_hook` WHERE `name` = 'actionAdminLoginControllerSetMedia';
