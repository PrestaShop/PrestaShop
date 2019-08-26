SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;

/* ps_1770_copy_name_from_carrier_to_carrier_lang */
ALTER TABLE `PREFIX_carrier_lang` ADD `name` varchar(64) NOT NULL AFTER `delay`;
