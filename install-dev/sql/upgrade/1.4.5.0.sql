SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_RESTRICT_DELIVERED_COUNTRIES', '0', NOW(), NOW());

UPDATE `PREFIX_country_lang`
SET `name` = 'United States'
WHERE `name` = 'United State'
AND `id_lang` = (
	SELECT `id_lang`
	FROM `PREFIX_lang`
	WHERE `iso_code` = 'en'
	LIMIT 1
);