SET NAMES 'utf8';

/* PHP:p15014_copy_missing_images_tab_from_installer(); */;

/* PHP:p15014_add_missing_columns(); */;

UPDATE `PREFIX_orders` SET `reference` = LPAD(reference, 9 , '0');

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('displayMyAccountBlock', 'myAccountBlock');