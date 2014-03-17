SET NAMES 'utf8';

ALTER TABLE `PREFIX_image_shop` CHANGE `cover` `cover` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE  `PREFIX_order_detail_tax` ADD PRIMARY KEY (`id_order_detail`);
ALTER TABLE  `PREFIX_order_detail_tax` ADD INDEX `id_tax` ( `id_tax`);

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 0 FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h WHERE m.name IN ('dashgoals') AND h.name IN ('dashboardData')
);

/* PHP:add_new_tab(AdminDashgoals, fr:Dashgoals|en:Dashgoals, -1, false, null, dashgoals); */;
