<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_5($object)
{
	return Db::getInstance()->execute('
		ALTER TABLE '._DB_PREFIX_.'mailalert_customer_oos 
		ADD '.mailalerts_stripslashes_field('id_lang').' INT( 10 ) UNSIGNED NOT NULL , 
		DROP PRIMARY KEY , 
		ADD PRIMARY KEY ( 
			'.mailalerts_stripslashes_field('id_customer').' , 
			'.mailalerts_stripslashes_field('customer_email').' , 
			'.mailalerts_stripslashes_field('id_product').' , 
			'.mailalerts_stripslashes_field('id_product_attribute').' , 
			'.mailalerts_stripslashes_field('id_shop').' 
		)'
	);
}

function mailalerts_stripslashes_field($field)
{
	$quotes = array('"\\\'"', '"\'"');
	$dquotes = array('\'\\\\"\'', '\'"\'');
	$backslashes = array('"\\\\\\\\"', '"\\\\"');

	return '`'.bqSQL($field).'` = replace(replace(replace(`'.bqSQL($field).'`, '.$quotes[0].', '.$quotes[1].'), '.$dquotes[0].', '.$dquotes[1].'), '.$backslashes[0].', '.$backslashes[1].')';
}