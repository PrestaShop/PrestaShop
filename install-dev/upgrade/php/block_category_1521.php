<?php 



function block_category_1521()
{
	if (!Db::getInstance()->ExecuteS('SELECT `'._DB_PREFIX_.'configuration` WHERE `name`=\'BLOCK_CATEG_MAX_DEPTH\' '))
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'configuration` 
			(`id_configuration` ,`id_shop_group` ,`id_shop` ,`name` ,`value` ,`date_add` ,`date_upd`)
			VALUES (NULL, NULL, NULL, \'BLOCK_CATEG_MAX_DEPTH\', 2, NOW(), NOW())');
	else if (Db::getInstance()->ExecuteS('SELECT `'._DB_PREFIX_.'configuration` WHERE and `value` IS NOT NULL AND `value` <> 0'))
		Db::getInstance()->Execute('UPDATE  `'._DB_PREFIX_.'configuration` SET `value` = 2 WHERE `name`=\'BLOCK_CATEG_MAX_DEPTH\' ');

}