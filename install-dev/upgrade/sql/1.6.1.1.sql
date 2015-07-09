SET NAMES 'utf8';

UPDATE `PREFIX_tax_rules_group` SET `date_add` = NOW(), `date_upd` = NOW() WHERE `date_add` = '0000-00-00 00:00:00';
