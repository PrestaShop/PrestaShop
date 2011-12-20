<?php

function generate_order_reference()
{
	// Get all orders
	$orders = Db::getInstance()->executeS('SELECT id_order FROM '._DB_PREFIX_.'orders');
	foreach ($orders as $order)
	{
		Db::getInstance()->execute('
			UPDATE '._DB_PREFIX_.'orders
			SET reference = \''.Order::generateReference().'\'
			WHERE id_order = '.$order['id_order']);
	}
}