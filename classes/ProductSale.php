<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class ProductSaleCore.
 */
class ProductSaleCore
{
    /**
     * Fill the `product_sale` SQL table with data from `order_detail`.
     *
     * @return bool True on success
     */
    public static function fillProductSales()
    {
        $sql = 'REPLACE INTO ' . _DB_PREFIX_ . 'product_sale
				(`id_product`, `quantity`, `sale_nbr`, `date_upd`)
				SELECT od.product_id, SUM(od.product_quantity), COUNT(od.product_id), NOW()
							FROM ' . _DB_PREFIX_ . 'order_detail od GROUP BY od.product_id';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Get number of actives products sold.
     *
     * @return int number of actives products listed in product_sales
     */
    public static function getNbSales()
    {
        $sql = 'SELECT COUNT(ps.`id_product`) AS nb
				FROM `' . _DB_PREFIX_ . 'product_sale` ps
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = ps.`id_product`
				' . Shop::addSqlAssociation('product', 'p', false) . '
				WHERE product_shop.`active` = 1';

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Get required informations on best sales products.
     *
     * @param int $idLang Language id
     * @param int $pageNumber Start from (optional)
     * @param int $nbProducts Number of products to return (optional)
     *
     * @return array|bool from Product::getProductProperties
     *                    `false` if failure
     */
    public static function getBestSales($idLang, $pageNumber = 0, $nbProducts = 10, $orderBy = null, $orderWay = null)
    {
        $context = Context::getContext();
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        if ($nbProducts < 1) {
            $nbProducts = 10;
        }
        $finalOrderBy = $orderBy;
        $orderTable = '';

        $invalidOrderBy = !Validate::isOrderBy($orderBy);
        if ($invalidOrderBy || null === $orderBy) {
            $orderBy = 'quantity';
            $orderTable = 'ps';
        }

        if ($orderBy == 'date_add' || $orderBy == 'date_upd') {
            $orderTable = 'product_shop';
        }

        $invalidOrderWay = !Validate::isOrderWay($orderWay);
        if ($invalidOrderWay || null === $orderWay || $orderBy == 'sales') {
            $orderWay = 'DESC';
        }

        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        // no group by needed : there's only one attribute with default_on=1 for a given id_product + shop
        // same for image with cover=1
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
					' . (Combination::isFeatureActive() ? 'product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity,IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute,' : '') . '
					pl.`name`,
					m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
					ps.`quantity` AS sales,
					DATEDIFF(p.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int) $interval . ' DAY)) > 0 AS new'
            . ' FROM `' . _DB_PREFIX_ . 'product_sale` ps
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`
				' . Shop::addSqlAssociation('product', 'p', false);
        if (Combination::isFeatureActive()) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
							ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')';
        }

        $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
					AND tr.`id_country` = ' . (int) $context->country->id . '
					AND tr.`id_state` = 0
				' . Product::sqlStock('p', 0);

        $sql .= '
				WHERE product_shop.`active` = 1
					AND product_shop.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)';
        }

        if ($finalOrderBy != 'price') {
            $sql .= '
					ORDER BY ' . (!empty($orderTable) ? '`' . pSQL($orderTable) . '`.' : '') . '`' . pSQL($orderBy) . '` ' . pSQL($orderWay) . '
					LIMIT ' . (int) (($pageNumber - 1) * $nbProducts) . ', ' . (int) $nbProducts;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($finalOrderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
            $result = array_slice($result, (int) (($pageNumber - 1) * $nbProducts), (int) $nbProducts);
        }
        if (!$result) {
            return false;
        }

        return $result;
    }

    /**
     * Get required informations on best sales products.
     *
     * @param int $idLang Language id
     * @param int $pageNumber Start from (optional)
     * @param int $nbProducts Number of products to return (optional)
     *
     * @return bool|array keys : id_product, link_rewrite, name, id_image, legend, sales, ean13, upc, link
     */
    public static function getBestSalesLight($idLang, $pageNumber = 0, $nbProducts = 10, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($pageNumber < 0) {
            $pageNumber = 0;
        }
        if ($nbProducts < 1) {
            $nbProducts = 10;
        }

        // no group by needed : there's only one attribute with default_on=1 for a given id_product + shop
        // same for image with cover=1
        $sql = '
		SELECT
			p.id_product, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, pl.`name`, product_shop.`id_category_default`,
			ps.`quantity` AS sales, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
			IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
			product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY')) . '" as new,
			product_shop.`on_sale`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity
		FROM `' . _DB_PREFIX_ . 'product_sale` ps
		LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`
		' . Shop::addSqlAssociation('product', 'p') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
			ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (product_attribute_shop.id_product_attribute=pa.id_product_attribute)
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
			ON p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
			ON cl.`id_category` = product_shop.`id_category_default`
			AND cl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . Product::sqlStock('p', 0);

        $sql .= '
		WHERE product_shop.`active` = 1
		AND p.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
				JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
				WHERE cp.`id_product` = p.`id_product`)';
        }

        $sql .= '
		ORDER BY ps.quantity DESC
		LIMIT ' . (int) ($pageNumber * $nbProducts) . ', ' . (int) $nbProducts;

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return false;
        }

        return $result;
    }

    /**
     * Add Product sale.
     *
     * @param int $productId Product ID
     * @param int $qty Quantity
     *
     * @return bool Indicates whether the sale was successfully added
     */
    public static function addProductSale($productId, $qty = 1)
    {
        return Db::getInstance()->execute('
			INSERT INTO ' . _DB_PREFIX_ . 'product_sale
			(`id_product`, `quantity`, `sale_nbr`, `date_upd`)
			VALUES (' . (int) $productId . ', ' . (int) $qty . ', 1, NOW())
			ON DUPLICATE KEY UPDATE `quantity` = `quantity` + ' . (int) $qty . ', `sale_nbr` = `sale_nbr` + 1, `date_upd` = NOW()');
    }

    /**
     * Get number of sales.
     *
     * @param int $idProduct Product ID
     *
     * @return int Number of sales for the given Product
     */
    public static function getNbrSales($idProduct)
    {
        $result = Db::getInstance()->getRow('SELECT `sale_nbr` FROM ' . _DB_PREFIX_ . 'product_sale WHERE `id_product` = ' . (int) $idProduct);
        if (empty($result) || !array_key_exists('sale_nbr', $result)) {
            return -1;
        }

        return (int) $result['sale_nbr'];
    }

    /**
     * Remove a Product sale.
     *
     * @param int $idProduct Product ID
     * @param int $qty Quantity
     *
     * @return bool Indicates whether the product sale has been successfully removed
     */
    public static function removeProductSale($idProduct, $qty = 1)
    {
        $totalSales = ProductSale::getNbrSales($idProduct);
        if ($totalSales > 1) {
            return Db::getInstance()->execute(
                '
				UPDATE ' . _DB_PREFIX_ . 'product_sale
				SET `quantity` = CAST(`quantity` AS SIGNED) - ' . (int) $qty . ', `sale_nbr` = CAST(`sale_nbr` AS SIGNED) - 1, `date_upd` = NOW()
				WHERE `id_product` = ' . (int) $idProduct
            );
        } elseif ($totalSales == 1) {
            return Db::getInstance()->delete('product_sale', 'id_product = ' . (int) $idProduct);
        }

        return true;
    }
}
