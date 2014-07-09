SET NAMES 'utf8';

CREATE TABLE `PREFIX_smarty_cache` (
  `id_smarty_cache` char(40) NOT NULL,
  `name` char(40),
  `cache_id` varchar(254) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id_smarty_cache`),
  KEY `name` (`name`),
  KEY `cache_id` (`cache_id`),
  KEY `modified` (`modified`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_configuration` (`name` , `value` , `date_add` , `date_upd`) VALUES
('PS_SMARTY_CACHING_TYPE', 'filesystem', NOW(), NOW()),
('PS_SMARTY_CLEAR_CACHE', 'everytime', NOW(), NOW());
