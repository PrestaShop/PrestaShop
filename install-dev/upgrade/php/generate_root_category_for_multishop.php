<?php

require_once('generate_ntree.php');

function generate_root_category_for_multishop()
{
	Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'category` SET `level_depth`=`level_depth`+1
	');

	Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'category` (`id_parent`, `level_depth`, `active`, `date_add`, `date_upd`, `is_root_category`) VALUES
		(0, 0, 1, NOW(), NOW(), 0)
	');
	$id = Db::getInstance()->insert_id();
	// set vars config
	Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
	(\'PS_ROOT_CATEGORY\', '.(int)$id.', NOW(), NOW()),
	(\'PS_HOME_CATEGORY\', 1, NOW(), NOW())
	');

	$langs = Db::getInstance()->executeS('
		SELECT `id_lang`
		FROM `'._DB_PREFIX_.'lang`
	');

	$shops = Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'shop`
	');

	$data = array();
	foreach ($langs as $lang)
		foreach ($shops as $shop)
			$data[] = array(
				'id_lang' => $lang['id_lang'],
				'id_shop' => $shop['id_shop'],
				'id_category' => $id,
				'name' => 'Root',
				'link_rewrite' => '',
			);
	Db::getInstance()->insert('category_lang', $data);

	$categories = Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
	');
	$data = array();
	foreach ($categories as $category)
		foreach ($shops as $shop)
			$data[] = array(
				'id_category' => $category['id_category'],
				'id_shop' => $shop['id_shop']
			);
	Db::getInstance()->insert('category_shop', $data);

	Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'category`
		SET `id_parent` = '.(int)$id.'
		WHERE `id_parent` = 0 AND `id_category` <> '.(int)$id.'
	');
	Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'shop`
		SET `id_category` = 1
		WHERE `id_shop` = 1
	');
	
	generate_ntree();
}
