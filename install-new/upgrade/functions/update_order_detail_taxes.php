<?php

function update_order_detail_taxes()
{
	$order_detail_taxes = Db::getInstance()->executeS('
	SELECT `id_order_detail`, `tax_name`, `tax_rate` FROM `'._DB_PREFIX_.'order_detail`
	');

	foreach ($order_detail_taxes as $order_detail_tax)
	{
		if ($order_detail_tax['tax_rate'] == '0.000')
			continue;

		$alternative_tax_name = 'Tax '.$order_detail_tax['tax_rate'];
		$create_tax = true;

		if ($id_tax = (int)Tax::getTaxIdByName($order_detail_tax['tax_name'], false) || $id_tax = (int)Tax::getTaxIdByName($alternative_tax_name, false))
		{
			$tax = new Tax($id_tax);
			if (Validate::isLoadedObject($tax))
				if ((string)$tax->rate == (string)$order_detail_tax['tax_rate'])
					$create_tax = false;
				else
					echo (string)$tax->rate.'!='.(string)$order_detail_tax['tax_rate'];
		}

		if ($create_tax)
		{
			$tax = new Tax();
			$tax->rate = (float)$order_detail_tax['tax_rate'];
			$tax->name[Configuration::get('PS_LANG_DEFAULT')] = (isset($order_detail_tax['tax_name']) ? $order_detail_tax['tax_name'] : $alternative_tax_name);
			$tax->deleted = true;
			$tax->active = false;
			$tax->save();

			$id_tax = $tax->id;
		}

		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (`id_order_detail`, `id_tax`)
		VALUES ('.(int)$order_detail_tax['id_order_detail'].','.$id_tax.')
		');

	}
}