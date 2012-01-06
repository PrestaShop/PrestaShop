<?php

function generate_order_reference()
{
	// Get all orders
	$orders = Db::getInstance()->executeS('SELECT id_order FROM '._DB_PREFIX_.'orders');
	foreach ($orders as $order)
	{
		$random_ref = '';
		for ($i = 0, $passwd = ''; $i < 9; $i++)
			$random_ref .= substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(0,25), 1);
		Db::getInstance()->execute('
			UPDATE '._DB_PREFIX_.'orders
			SET reference = \''.$random_ref.'\'
			WHERE id_order = '.(int)$order['id_order']);
	}
}
