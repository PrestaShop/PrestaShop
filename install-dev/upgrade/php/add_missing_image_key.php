<?php

function add_missing_image_key()
{
	$res = true;
	$key_exists = Db::getInstance()->executeS('SHOW INDEX
		FROM `'._DB_PREFIX_.'image`
		WHERE Key_name = \'idx_product_image\'');
	if ($key_exists)
		$res &= Db::getInstance()->execute('ALTER TABLE
		`'._DB_PREFIX_.'image`
		DROP KEY `idx_product_image`');
	$res &= Db::getInstance()->execute('ALTER TABLE
	`'._DB_PREFIX_.'image`
	ADD UNIQUE `idx_product_image` (`id_image`, `id_product`, `cover`)');

	return $res;
}
