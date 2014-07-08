SET NAMES 'utf8';

CREATE TABLE `PREFIX_smarty_cache` (
  `id_smarty_cache` char(40) NOT NULL,
  `name` varchar(250) NOT NULL,
  `cache_id` varchar(250) DEFAULT NULL,
  `compile_id` varchar(250) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `cache_id` (`cache_id`),
  KEY `compile_id` (`compile_id`),
  KEY `modified` (`modified`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_configuration` (`name` , `value` , `date_add` , `date_upd`)
VALUES ('PS_SMARTY_CACHING_TYPE', 'filesystem', NOW(), NOW());
