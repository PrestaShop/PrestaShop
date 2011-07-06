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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PackCore extends Product
{
	protected static $cachePackItems = array();
	protected static $cacheIsPack = array();
	protected static $cacheIsPacked = array();

	public static function isPack($id_product)
	{
		if (!array_key_exists($id_product, self::$cacheIsPack))
		{
			$result = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'pack WHERE id_product_pack = '.(int)($id_product));
			self::$cacheIsPack[$id_product] = ($result > 0);
		}
		return self::$cacheIsPack[$id_product];
	}

	public static function isPacked($id_product)
	{
		if (!array_key_exists($id_product, self::$cacheIsPacked))
		{
			$result = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'pack WHERE id_product_item = '.(int)($id_product));
			self::$cacheIsPacked[$id_product] = ($result > 0);
		}
		return self::$cacheIsPacked[$id_product];
	}

	public static function noPackPrice($id_product)
	{
		global $cookie;

		$sum = 0;

		$price_display_method = !self::$_taxCalculationMethod;
		$items = self::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
		foreach ($items as $item)
			$sum += $item->getPrice($price_display_method) * $item->pack_quantity;
		return $sum;
	}

	public static function getItems($id_product, $id_lang)
	{
		if (array_key_exists($id_product, self::$cachePackItems))
			return self::$cachePackItems[$id_product];
		$result = Db::getInstance()->ExecuteS('SELECT id_product_item, quantity FROM '._DB_PREFIX_.'pack where id_product_pack = '.(int)($id_product));
		$arrayResult = array();
		foreach ($result AS $row)
		{
			$p = new Product($row['id_product_item'], false, (int)($id_lang));
			$p->pack_quantity = $row['quantity'];
			$arrayResult[] = $p;
		}
		self::$cachePackItems[$id_product] = $arrayResult;
		return self::$cachePackItems[$id_product];
	}

	public static function isInStock($id_product)
	{
		$items = self::getItems((int)($id_product), Configuration::get('PS_LANG_DEFAULT'));
		foreach ($items AS $item)
			if ($item->quantity < $item->pack_quantity AND !$item->isAvailableWhenOutOfStock((int)($item->out_of_stock)))
				return false;
		return true;
	}

	public static function getItemTable($id_product, $id_lang, $full = false)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.*, pl.*, i.`id_image`, il.`legend`, t.`rate`, cl.`name` AS category_default, a.quantity AS pack_quantity
		FROM `'._DB_PREFIX_.'pack` a
		LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = a.id_product_item
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product = pl.id_product AND pl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
		WHERE a.`id_product_pack` = '.(int)($id_product)
		);
		if (!$full)
			return $result;

		$arrayResult = array();
		foreach ($result as $row)
			if (!Pack::isPack($row['id_product']))
				$arrayResult[] = Product::getProductProperties($id_lang, $row);
		return $arrayResult;
	}

	public static function getPacksTable($id_product, $id_lang, $full = false, $limit = NULL)
	{
		$packs = Db::getInstance()->getValue('
		SELECT GROUP_CONCAT(a.`id_product_pack`)
		FROM `'._DB_PREFIX_.'pack` a
		WHERE a.`id_product_item` = '.(int)$id_product);

		if (!(int)$packs)
			return array();

		$sql = '
		SELECT p.*, pl.*, i.`id_image`, il.`legend`, t.`rate`
		FROM `'._DB_PREFIX_.'product` p
		NATURAL LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)$id_lang.')
		WHERE pl.`id_lang` = '.(int)$id_lang.'
		AND p.`id_product` IN ('.$packs.')';
		if ($limit)
			$sql .= ' LIMIT '.(int)$limit;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if (!$full)
			return $result;

		$arrayResult = array();
		foreach ($result as $row)
			if (!Pack::isPacked($row['id_product']))
				$arrayResult[] = Product::getProductProperties($id_lang, $row);
		return $arrayResult;
	}

	public static function deleteItems($id_product)
	{
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET cache_is_pack = 0 WHERE id_product = '.(int)($id_product).' LIMIT 1');
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pack` WHERE `id_product_pack` = '.(int)($id_product));
	}

	/**
	 * @deprecated
	 */
	public static function addItems($id_product, $ids)
	{
		Tools::displayAsDeprecated();

		array_pop($ids);
		foreach ($ids as $id_product_item)
		{
			$idQty = explode('x', $id_product_item);
			if (!self::addItem($id_product, $idQty[1], $idQty[0]))
				return false;
		}
		return true;
	}

	/**
	* Add an item to the pack
	*
	* @param integer $id_product
	* @param integer $id_item
	* @param integer $qty
	* @return boolean true if everything was fine
	*/
	public static function addItem($id_product, $id_item, $qty)
	{
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET cache_is_pack = 1 WHERE id_product = '.(int)($id_product).' LIMIT 1');
		return Db::getInstance()->AutoExecute(_DB_PREFIX_.'pack', array('id_product_pack' => (int)($id_product), 'id_product_item' => (int)($id_item), 'quantity' => (int)($qty)), 'INSERT');
	}

	public static function duplicate($id_product_old, $id_product_new)
	{
		Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'pack (id_product_pack, id_product_item, quantity)
		(SELECT '.(int)($id_product_new).', id_product_item, quantity FROM '._DB_PREFIX_.'pack WHERE id_product_pack = '.(int)($id_product_old).')');

		// If return query result, a non-pack product will return false
		return true;
	}
}

