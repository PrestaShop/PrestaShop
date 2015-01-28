SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET `value` = CONCAT('#', `value`) WHERE `name` LIKE 'PS_%_PREFIX';

UPDATE `PREFIX_configuration_lang` SET `value` = CONCAT('#', `value`) WHERE `id_configuration` IN (SELECT `id_configuration` FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_%_PREFIX');

INSERT INTO `PREFIX_hook` (`id_hook` , `name` , `title` , `description` , `position` , `live_edit`)
VALUES (NULL , 'displayAfterThemeInstallation', 'Place where you can display additional information after theme installation', 'This hook displays additional information after theme installation', '1', '0');
