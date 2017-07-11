SET NAMES 'utf8';

ALTER TABLE `PREFIX_feature_product` DROP PRIMARY KEY ,ADD PRIMARY KEY (`id_feature`, `id_product`, `id_feature_value`);
