SET NAMES 'utf8';

ALTER TABLE `PREFIX_state` CHANGE `iso_code` `iso_code` varchar(7) NOT NULL;

/* PHP:move_translations_module_file(); */;

