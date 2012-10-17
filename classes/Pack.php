<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PackCore extends Product
{
	protected static $cachePackItems = array();
	protected static $cacheIsPack = array();
	protected static $cacheIsPacked = array();

	/**
	 * Is product a pack?
	 *
	 * @static
	 * @param $id_product
	 * @return bool
	 */
	public static function isPack($id_product)
	{
		if (!Pack::isFeatureActive())
			return false;

		if (!$id_product)
			return false;

		if (!array_key_exists($id_product, self::$cacheIsPack))
		{
			$result = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'pack WHERE id_product_pack = '.(int)$id_product);
			self::$cacheIsPack[$id_product] = ($result > 0);
		}
		return self::$cacheIsPack[$id_product];
	}

	/**
	 * Is product in a pack?
	 *
	 * @static
	 * @param $id_product
	 * @return bool
	 */
	public static function isPacked($id_product)
	{
		if (!Pack::isFeatureActive())
			return false;

		if (!array_key_exists($id_product, self::$cacheIsPacked))
		{
			$result = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'pack WHERE id_product_item = '.(int)$id_product);
			self::$cacheIsPacked[$id_product] = ($result > 0);
		}
		return self::$cacheIsPacked[$id_product];
	}

	public static function noPackPrice($id_product)
	{
		$sum = 0;
		$price_display_method = !self::$_taxCalculationMethod;
		$items = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
		foreach ($items as $item)
			$sum += $item->getPrice($price_display_method) * $item->pack_quantity;
		return $sum;
	}

	public static function getItems($id_product, $id_lang)
	{
		if (!Pack::isFeatureActive())
			return array();

		if (array_key_exists($id_product, self::$cachePackItems))
			return self::$cachePackItems[$id_product];
		$result = Db::getInstance()->executeS('SELECT id_product_item, quantity FROM '._DB_PREFIX_.'pack where id_product_pack = '.(int)$id_product);
		$array_result = array();
		foreach ($result as $row)
		{
			$p = new Product($row['id_product_item'], false, $id_lang);
			$p->loadStockData();
			$p->pack_quantity = $row['quantity'];
			$array_result[] = $p;
		}
		self::$cachePackItems[$id_product] = $array_result;
		return self::$cachePackItems[$id_product];
	}

	public static function isInStock($id_product)
	{
		if (!Pack::isFeatureActive())
			return true;

		$items = Pack::getItems((int)$id_product, Configuration::get('PS_LANG_DEFAULT'));

		foreach ($items as $item)
		{
			// Updated for 1.5.0
			if (Product::getQuantity($item->id) < $item->pack_quantity
				|| (Product::getQuantity($item->id) < $item->pack_quantity && !$item->isAvailableWhenOutOfStock((int)$item->out_of_stock)))
				return false;
		}
		return true;
	}

	public static function getItemTable($id_product, $id_lang, $full = false)
	{
		if (!Pack::isFeatureActive())
			return array();

		$sql = 'SELECT p.*, product_shop.*, pl.*, image_shop.`id_image`, il.`legend`, t.`rate`, cl.`name` AS category_default, a.quantity AS pack_quantity, product_shop.`id_category_default`, a.id_product_pack
				FROM `'._DB_PREFIX_.'pack` a
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = a.id_product_item
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.id_product = pl.id_product
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)$id_lang.')
				WHERE product_shop.`id_shop` = '.(int)Context::getContext()->shop->id.'
				AND ((image_shop.id_image IS NOT NULL OR i.id_image IS NULL) OR (image_shop.id_image IS NULL AND i.cover=1))
				AND a.`id_product_pack` = '.(int)$id_product;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if (!$full)
			return $result;

		$array_result = array();
		foreach ($result as $row)
			if (!Pack::isPack($row['id_product']))
				$array_result[] = Product::getProductProperties($id_lang, $row);
		return $array_result;
	}

	public static function getPacksTable($id_product, $id_lang, $full = false, $limit = null)
	{
		if (!Pack::isFeatureActive())
			return array();

		$packs = Db::getInstance()->getValue('
		SELECT GROUP_CONCAT(a.`id_product_pack`)
		FROM `'._DB_PREFIX_.'pack` a
		WHERE a.`id_product_item` = '.(int)$id_product);

		if (!(int)$packs)
			return array();

		$sql = '
		SELECT p.*, product_shop.*, pl.*, image_shop.`id_image`, il.`legend`, t.`rate`
		FROM `'._DB_PREFIX_.'product` p
		NATURAL LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
		Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)$id_lang.')
		WHERE pl.`id_lang` = '.(int)$id_lang.'
			'.Shop::addSqlRestrictionOnLang('pl').'
			AND p.`id_product` IN ('.$packs.')
			AND ((image_shop.id_image IS NOT NULL OR i.id_image IS NULL) OR (image_shop.id_image IS NULL AND i.cover=1))';
		if ($limit)
			$sql .= ' LIMIT '.(int)$limit;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if (!$full)
			return $result;

		$array_result = array();
		foreach ($result as $row)
			if (!Pack::isPacked($row['id_product']))
				$array_result[] = Product::getProductProperties($id_lang, $row);
		return $array_result;
	}

	public static function deleteItems($id_product)
	{
		return Db::getInstance()->update('product', array('cache_is_pack' => 0), 'id_product = '.(int)$id_product) &&
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'pack` WHERE `id_product_pack` = '.(int)$id_product) &&
			Configuration::updateGlobalValue('PS_PACK_FEATURE_ACTIVE', Pack::isCurrentlyUsed());
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
		return Db::getInstance()->update('product', array('cache_is_pack' => 1), 'id_product = '.(int)$id_product) &&
			Db::getInstance()->insert('pack', array('id_product_pack' => (int)$id_product, 'id_product_item' => (int)$id_item, 'quantity' => (int)$qty)) &&
			Configuration::updateGlobalValue('PS_PACK_FEATURE_ACTIVE', '1');
	}

	public static function duplicate($id_product_old, $id_product_new)
	{
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pack (id_product_pack, id_product_item, quantity)
		(SELECT '.(int)$id_product_new.', id_product_item, quantity FROM '._DB_PREFIX_.'pack WHERE id_product_pack = '.(int)$id_product_old.')');

		// If return query result, a non-pack product will return false
		return true;
	}

	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		return Configuration::get('PS_PACK_FEATURE_ACTIVE');
	}

	/**
	 * This method is allow to know if a Pack entity is currently used
	 * @since 1.5.0
	 * @param $table
	 * @param $has_active_column
	 * @return bool
	 */
	public static function isCurrentlyUsed($table = null, $has_active_column = false)
	{
		// We dont't use the parent method because the identifier isn't id_pack
		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_product_pack`
			FROM `'._DB_PREFIX_.'pack`
		');
	}

	/**
	 * For a given pack, tells if it has at least one product using the advanced stock management
	 *
	 * @param int $id_product id_pack
	 * @return bool
	 */
	public static function usesAdvancedStockManagement($id_product)
	{
		if (!Pack::isPack($id_product))
			return false;

		$products = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
		foreach ($products as $product)
		{
			// if one product uses the advanced stock management
			if ($product->advanced_stock_management == 1)
				return true;
		}
		// not used
		return false;
	}
}

