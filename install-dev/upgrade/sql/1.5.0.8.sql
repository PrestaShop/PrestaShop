SET NAMES 'utf8';

ALTER TABLE `PREFIX_product` ADD `visibility` ENUM('both', 'catalog', 'search', 'none') NOT NULL default 'both' AFTER `indexed`;

  