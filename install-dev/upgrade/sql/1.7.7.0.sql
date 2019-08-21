SET SESSION sql_mode = '';
SET NAMES 'utf8';

/* PHP:ps_1770_preset_tab_enabled(); */;
ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;
DELETE `PREFIX_configuration` WHERE name = 'PS_PRICE_DISPLAY_PRECISION';
