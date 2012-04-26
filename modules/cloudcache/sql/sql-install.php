<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cloudcache_zone` (
		`id_zone` int(10) unsigned NOT NULL,
		`id_shop` int(10) unsigned NOT NULL,
		`id_group_shop` int(10) unsigned NOT NULL,
		`name` varchar(255) NOT NULL,
		`origin` varchar(255) NULL,
		`compress` tinyint NULL,
		`label` varchar(255) NULL,
		`cdn_url` varchar(255) NOT NULL,
		`bw_yesterday` int(10) NOT NULL,
		`bw_last_week` int(10) NOT NULL,
		`bw_last_month` int(10) NOT NULL,
		`file_type` varchar(16) NOT NULL,
		`zone_type` varchar(16) NOT NULL,
		PRIMARY KEY (`id_zone`, `id_shop`),
		UNIQUE (`id_zone`, `name`, `cdn_url`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
