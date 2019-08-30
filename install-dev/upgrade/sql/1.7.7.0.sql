SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;

/* PHP:ps_1770_preset_tab_enabled(); */;

/* localize carrier name */
ALTER TABLE `PREFIX_carrier_lang` ADD `name` varchar(64) NOT NULL AFTER `delay`;

/* PHP:ps_1760_copy_data_from_currency_to_currency_lang(); */;
