<?php
function set_product_suppliers()
{
	$ps_currency_default = Db::getInstance()->getValue('SELECT value 
	FROM `'._DB_PREFIX_.'configuration` WHERE name="PS_CURRENCY_DEFAULT"');

	//Get all products with positive quantity
	$resource = Db::getInstance()->query('
		SELECT id_supplier, id_product, supplier_reference, wholesale_price
		FROM `'._DB_PREFIX_.'product`
		WHERE `id_supplier` > 0
	');

	while ($row = Db::getInstance()->nextRow($resource))
	{
		//Set default supplier for product
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_supplier`
			(`id_product`, `id_product_attribute`, `id_supplier`, 
				`product_supplier_reference`, `product_supplier_price_te`, 
				`id_currency`)
			VALUES
			("'.(int)$row['id_product'].'", "0", "'.(int)$row['id_supplier'].'", 
			"'.(int)$row['supplier_reference'].'", "'.(int)$row['wholesale_price'].'", 
				"'.(int)$ps_currency_default.'"
		');

		//Try to get product attribues
		$attributes = Db::getInstance()->executeS('
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
				(`id_product`, `id_product_attribute`, 
				`id_supplier`, `product_supplier_reference`, 
				`product_supplier_price_te`, `id_currency`)
				VALUES
				("'.(int)$row['id_product'].'", "'.(int)$attribute['id_product_attribute'].'", 
				"'.(int)$row['id_supplier'].'", "'.(int)$attribute['supplier_reference'].'", 
				"'.(int)$attribute['wholesale_price'].'", "'.(int)$ps_currency_default.'")
			');
		}
	}
}
