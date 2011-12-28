SET NAMES 'utf8';

ALTER TABLE PREFIX_theme ADD COLUMN directory varchar(64) NOT NULL;

/* Supply Order modification as of 1.5.0.3 */
ALTER TABLE `PREFIX_supply_order` DROP INDEX `reference`;
ALTER TABLE `PREFIX_supply_order` ADD UNIQUE (`reference`);

