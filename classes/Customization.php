<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CustomizationCore
{

	static public function getReturnedCustomizations($id_order)
	{
		if (($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT ore.`id_order_return`, ord.`id_order_detail`, ord.`id_customization`, ord.`product_quantity`
			FROM `'._DB_PREFIX_.'order_return` ore
			INNER JOIN `'._DB_PREFIX_.'order_return_detail` ord ON (ord.`id_order_return` = ore.`id_order_return`)
			WHERE ore.`id_order` = '.(int)($id_order).' AND ord.`id_customization` != 0')) === false)
			return false;
		$customizations = array();
		foreach ($result AS $row)
			$customizations[(int)($row['id_customization'])] = $row;
		return $customizations;
	}

	static public function getOrderedCustomizations($id_cart)
	{
		if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT `id_customization`, `quantity` FROM `'._DB_PREFIX_.'customization` WHERE `id_cart` = '.(int)($id_cart)))
			return false;
		$customizations = array();
		foreach ($result AS $row)
			$customizations[(int)($row['id_customization'])] = $row;
		return $customizations;
	}

	static public function countCustomizationQuantityByProduct($customizations)
	{
		$total = array();
		foreach ($customizations AS $customization)
			$total[(int)($customization['id_order_detail'])] = !isset($total[(int)($customization['id_order_detail'])]) ? (int)($customization['quantity']) : $total[(int)($customization['id_order_detail'])] + (int)($customization['quantity']);
		return $total;
	}

	static public function getLabel($id_customization, $id_lang)
	{
		if (!$id_customization || !$id_lang)
			return false;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `name`
		FROM `'._DB_PREFIX_.'customization_field_lang`
		WHERE `id_customization_field` = '.(int)($id_customization).'
		AND `id_lang` = '.(int)($id_lang)
		);

		return $result['name'];
	}

	public static function retrieveQuantitiesFromIds(array $ids_customizations)
	{
		$quantities = array();

		$in_values  = '';
		foreach($ids_customizations as $key => $id_customization)
		{
			if ($key > 0) $in_values += ',';
			$in_values += (int)($id_customization);
		}

		if (!empty($in_values))
		{
			$results =  Db::getInstance()->ExecuteS(
							'SELECT `id_customization`, `id_product`, `quantity`, `quantity_refunded`, `quantity_returned`
							 FROM `'._DB_PREFIX_.'customization`
							 WHERE `id_customization` IN ('.$in_values.')');

			foreach($results as $row)
			{
				$quantities[$row['id_customization']] = $row;
			}
		}

		return $quantities;
	}

	public static function countQuantityByCart($id_cart)
	{
		$quantity = array();

		$results =  Db::getInstance()->executeS('
					SELECT `id_product`, `id_product_attribute`, SUM(`quantity`) AS quantity
					FROM `'._DB_PREFIX_.'customization`
					WHERE `id_cart` = '.(int)($id_cart).'
					GROUP BY `id_cart`, `id_product`, `id_product_attribute`'
					);

		foreach($results as $row)
		{
			$quantity[$row['id_product']][$row['product_attribute_id']] = $row['quantity'];
		}

		return $quantity;
	}

}

