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
*  @version  Release: $Revision: 7484 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductSaleCore
{
	/*
	** Fill the `product_sale` SQL table with data from `order_detail`
	** @return bool True on success
	*/
	public static function fillProductSales()
	{
		$sql = 'REPLACE INTO '._DB_PREFIX_.'product_sale
				(`id_product`, `quantity`, `sale_nbr`, `date_upd`)
				SELECT od.product_id, COUNT(od.product_id), SUM(od.product_quantity), NOW()
							FROM '._DB_PREFIX_.'order_detail od GROUP BY od.product_id';
		return Db::getInstance()->execute($sql);
	}

	/*
	** Get number of actives products sold
	** @return int number of actives products listed in product_sales
	*/
	public static function getNbSales()
	{
		$sql = 'SELECT COUNT(ps.`id_product`) AS nb
				FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = ps.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				WHERE product_shop.`active` = 1';
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	/*
	** Get required informations on best sales products
	**
	** @param integer $id_lang Language id
	** @param integer $page_number Start from (optional)
	** @param integer $nb_products Number of products to return (optional)
	** @return array from Product::getProductProperties
	*/
	public static function getBestSales($id_lang, $page_number = 0, $nb_products = 10, $order_by = null, $order_way = null)
	{
		if ($page_number < 0) $page_number = 0;
		if ($nb_products < 1) $nb_products = 10;

		$final_order_by = $order_by;
		if (empty($order_by) || $order_by == 'position' || $order_by = 'price') $order_by = 'sales';
		if (empty($order_way)) $order_way = 'DESC';

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
		$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

		$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
					pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
					pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
					m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
					image_shop.`id_image`, il.`legend`,
					ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.$interval.' DAY)) > 0 AS new
				FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'
				WHERE product_shop.`active` = 1
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)
					AND ((image_shop.id_image IS NOT NULL OR i.id_image IS NULL) OR (image_shop.id_image IS NULL AND i.cover=1))
				ORDER BY `'.pSQL($order_by).'` '.pSQL($order_way).'
				LIMIT '.(int)($page_number * $nb_products).', '.(int)$nb_products;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if ($final_order_by == 'price')
			Tools::orderbyPrice($result, $order_way);
		if (!$result)
			return false;
		return Product::getProductsProperties($id_lang, $result);
	}

	/*
	** Get required informations on best sales products
	**
	** @param integer $id_lang Language id
	** @param integer $page_number Start from (optional)
	** @param integer $nb_products Number of products to return (optional)
	** @return array keys : id_product, link_rewrite, name, id_image, legend, sales, ean13, upc, link
	*/
	public static function getBestSalesLight($id_lang, $page_number = 0, $nb_products = 10, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		if ($page_number < 0) $page_number = 0;
		if ($nb_products < 1) $nb_products = 10;

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		$sql = 'SELECT p.id_product, pl.`link_rewrite`, pl.`name`, pl.`description_short`, image_shop.`id_image`, il.`legend`,
					ps.`quantity` AS sales, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category
				FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON cl.`id_category` = product_shop.`id_category_default`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
				WHERE product_shop.`active` = 1
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)
					AND ((image_shop.id_image IS NOT NULL OR i.id_image IS NULL) OR (image_shop.id_image IS NULL AND i.cover=1))
				ORDER BY sales DESC
				LIMIT '.(int)($page_number * $nb_products).', '.(int)$nb_products;
		if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
			return false;

		foreach ($result as &$row)
		{
		 	$row['link'] = $context->link->getProductLink($row['id_product'], $row['link_rewrite'], $row['category'], $row['ean13']);
		 	$row['id_image'] = Product::defineProductImage($row, $id_lang);
		}
		return $result;
	}

	public static function addProductSale($product_id, $qty = 1)
	{
		return Db::getInstance()->execute('
			INSERT INTO '._DB_PREFIX_.'product_sale
			(`id_product`, `quantity`, `sale_nbr`, `date_upd`)
			VALUES ('.(int)$product_id.', '.(int)$qty.', 1, NOW())
			ON DUPLICATE KEY UPDATE `quantity` = `quantity` + '.(int)$qty.', `sale_nbr` = `sale_nbr` + 1, `date_upd` = NOW()');
	}

	public static function getNbrSales($id_product)
	{
		$result = Db::getInstance()->getRow('SELECT `sale_nbr` FROM '._DB_PREFIX_.'product_sale WHERE `id_product` = '.(int)$id_product);
		if (!$result || empty($result) || !key_exists('sale_nbr', $result))
			return -1;
		return (int)$result['sale_nbr'];
	}

	public static function removeProductSale($id_product, $qty = 1)
	{
		$total_sales = ProductSale::getNbrSales($id_product);
		if ($total_sales > 1)
			return Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'product_sale
				SET `quantity` = `quantity` - '.(int)$qty.', `sale_nbr` = `sale_nbr` - 1, `date_upd` = NOW()
				WHERE `id_product` = '.(int)$id_product
			);
		elseif ($total_sales == 1)
			return Db::getInstance()->delete('product_sale', 'id_product = '.(int)$id_product);
		return true;
	}
}

