SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` CHANGE  `outstanding_allow_amount` `outstanding_allow_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_product_download` ADD `id_product_attribute` INT( 10 ) NOT NULL AFTER `id_product`;
/* PHP:blocknewsletter1530(); */;

/* PHP:block_category_1521(); */;