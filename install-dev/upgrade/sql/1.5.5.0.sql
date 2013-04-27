SET NAMES 'utf8';

ALTER TABLE  `PREFIX_store` CHANGE  `latitude`  `latitude` DECIMAL( 13, 8 ) NULL DEFAULT NULL , CHANGE  `longitude`  `longitude` DECIMAL( 13, 8 ) NULL DEFAULT NULL ;