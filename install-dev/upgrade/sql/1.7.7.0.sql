SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER DATABASE
    DB_NAME
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;

/* PHP:ps_1770_preset_tab_enabled(); */;
