SET SESSION sql_mode = '';
SET NAMES 'utf8';

DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminAddonsCatalog');

DELETE FROM `PREFIX_tab` where `class_name` = 'AdminAddonsCatalog';
