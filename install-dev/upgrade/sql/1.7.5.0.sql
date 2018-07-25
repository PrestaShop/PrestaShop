SET SESSION sql_mode = '';
SET NAMES 'utf8';

/* PHP:ps_1750_update_module_tabs(); */;
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminAddonsCatalog');

DELETE FROM `PREFIX_tab` where `class_name` = 'AdminAddonsCatalog';
