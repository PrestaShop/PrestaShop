SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_search_index` DROP KEY `id_product`;
ALTER TABLE `PREFIX_search_index` ADD KEY `id_product` (`id_product`,`weight`);
