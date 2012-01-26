<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function update_mailalerts_add_column_idshop()
{
	$id_mailalerts = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = "mailalerts"');

	if($id_mailalerts)
	{
		$res = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'mailalert_customer_oos` ADD COLUMN `id_shop` int(11) NOT NULL default "0" AFTER `id_customer`');
		if ($res)
			return true;
		else
			return array('error' => 1, 'msg' => sprintf('unable to create column id_shop (%s)', Db::getInstance ()->getMsgError()));
	}
	return true;
}
