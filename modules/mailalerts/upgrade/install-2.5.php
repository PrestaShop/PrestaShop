<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_5($object)
{
	return Db::getInstance()->execute('
		ALTER TABLE `'._DB_PREFIX_.'mailalert_customer_oos` 
		ADD `id_lang` INT( 10 ) UNSIGNED NOT NULL , 
		DROP PRIMARY KEY , 
		ADD PRIMARY KEY (`id_customer` , `customer_email` , `id_product` , `id_product_attribute` , `id_shop`)
	');
}