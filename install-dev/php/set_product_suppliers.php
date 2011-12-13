<?php
function set_product_suppliers()
{
	//Get all products with positive quantity
	$resource = Db::getInstance(_PS_USE_SQL_SLAVE_)->query('
		SELECT id_supplier, id_product, supplier_reference, wholesale_price
		FROM `'._DB_PREFIX_.'product`
		WHERE `id_supplier` > 0
	');

	while ($row = Db::getInstance()->nextRow($resource))
	{
		//Set default supplier for product
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_supplier`
			(`id_product`, `id_product_attribute`, `id_supplier`, `product_supplier_reference`, `product_supplier_price_te`, `id_currency`)
			VALUES
			("'.(int)$row['id_product'].'", "0", "'.(int)$row['id_supplier'].'", "'.(int)$row['supplier_reference'].'", "'.(int)$row['wholesale_price'].'", "'.(int)Configuration::get('PS_CURRENCY_DEFAULT').'")
		');

		//Try to get product attribues
		$attributes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_product_attribute, supplier_reference, wholesale_price
			FROM `'._DB_PREFIX_.'product_attribute`
			WHERE `id_product` = '.(int)$row['id_product']
		);

		//Add each attribute to stock_available
		foreach ($attributes as $attribute)
		{
			// set supplier for attribute
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'product_supplier`
				(`id_product`, `id_product_attribute`, `id_supplier`, `product_supplier_reference`, `product_supplier_price_te`, `id_currency`)
				VALUES
				("'.(int)$row['id_product'].'", "'.(int)$attribute['id_product_attribute'].'", "'.(int)$row['id_supplier'].'", "'.(int)$attribute['supplier_reference'].'", "'.(int)$attribute['wholesale_price'].'", "'.(int)Configuration::get('PS_CURRENCY_DEFAULT').'")
			');
		}
	}
}