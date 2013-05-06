SET NAMES 'utf8';

ALTER TABLE  `PREFIX_store` CHANGE  `latitude`  `latitude` DECIMAL( 13, 8 ) NULL DEFAULT NULL , CHANGE  `longitude`  `longitude` DECIMAL( 13, 8 ) NULL DEFAULT NULL ;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_CUSTOMER_CREATION_EMAIL', 1, NOW(), NOW());