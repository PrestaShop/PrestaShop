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
 * StockManager : implementation of StockManagerInterface.
 *
 * @since 1.5.0
 */
class StockManagerCore implements StockManagerInterface
{
    /**
     * @see StockManagerInterface::isAvailable()
     */
    public static function isAvailable()
    {
        // Default Manager : always available
        return true;
    }

    /**
     * @see StockManagerInterface::addProduct()
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param Warehouse $warehouse
     * @param int $quantity
     * @param int|null $id_stock_mvt_reason
     * @param float $price_te
     * @param bool $is_usable
     * @param int|null $id_supply_order
     * @param Employee|null $employee
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function addProduct(
        $id_product,
        $id_product_attribute,
        $quantity,
        $id_stock_mvt_reason,
        $price_te,
        $is_usable = true,
        $id_supply_order = null,
        $employee = null
    ) {
        return false;
    }

    /**
     * @see StockManagerInterface::removeProduct()
     *
     * @param int $id_product
     * @param int|null $id_product_attribute
     * @param Warehouse $warehouse
     * @param int $quantity
     * @param int $id_stock_mvt_reason
     * @param bool $is_usable
     * @param int|null $id_order
     * @param int $ignore_pack
     * @param Employee|null $employee
     *
     * @return array
     *
     * @throws PrestaShopException
     */
    public function removeProduct(
        $id_product,
        $id_product_attribute,
        $quantity,
        $id_stock_mvt_reason,
        $is_usable = true,
        $id_order = null,
        $ignore_pack = 0,
        $employee = null
    ) {
        return false;
    }

    /**
     * @see StockManagerInterface::getProductPhysicalQuantities()
     */
    public function getProductPhysicalQuantities($id_product, $id_product_attribute, $ids_warehouse = null, $usable = false)
    {
        $query = new DbQuery();
        $query->select('SUM(' . ($usable ? 's.usable_quantity' : 's.physical_quantity') . ')');
        $query->from('stock', 's');
        $query->where('s.id_product = ' . (int) $id_product);
        if (0 != $id_product_attribute) {
            $query->where('s.id_product_attribute = ' . (int) $id_product_attribute);
        }

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @see StockManagerInterface::getProductRealQuantities()
     */
    public function getProductRealQuantities($id_product, $id_product_attribute, $ids_warehouse = null, $usable = false)
    {
        $client_orders_qty = 0;

        // check if product is present in a pack
        if (!Pack::isPack($id_product) && $in_pack = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT id_product_pack, quantity FROM ' . _DB_PREFIX_ . 'pack
			WHERE id_product_item = ' . (int) $id_product . '
			AND id_product_attribute_item = ' . ($id_product_attribute ? (int) $id_product_attribute : '0')
        )) {
            foreach ($in_pack as $value) {
                if (Validate::isLoadedObject($product = new Product((int) $value['id_product_pack'])) &&
                    ($product->pack_stock_type == Pack::STOCK_TYPE_PRODUCTS_ONLY || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH || ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT && Configuration::get('PS_PACK_STOCK_TYPE') > 0))) {
                    $query = new DbQuery();
                    $query->select('od.product_quantity, od.product_quantity_refunded, pk.quantity');
                    $query->from('order_detail', 'od');
                    $query->leftjoin('orders', 'o', 'o.id_order = od.id_order');
                    $query->where('od.product_id = ' . (int) $value['id_product_pack']);
                    $query->leftJoin('order_history', 'oh', 'oh.id_order = o.id_order AND oh.id_order_state = o.current_state');
                    $query->leftJoin('order_state', 'os', 'os.id_order_state = oh.id_order_state');
                    $query->leftJoin('pack', 'pk', 'pk.id_product_item = ' . (int) $id_product . ' AND pk.id_product_attribute_item = ' . ($id_product_attribute ? (int) $id_product_attribute : '0') . ' AND id_product_pack = od.product_id');
                    $query->where('os.shipped != 1');
                    $query->where('o.valid = 1 OR (os.id_order_state != ' . (int) Configuration::get('PS_OS_ERROR') . '
								   AND os.id_order_state != ' . (int) Configuration::get('PS_OS_CANCELED') . ')');
                    $query->groupBy('od.id_order_detail');
                    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    if (count($res)) {
                        foreach ($res as $row) {
                            $client_orders_qty += ($row['product_quantity'] - $row['product_quantity_refunded']) * $row['quantity'];
                        }
                    }
                }
            }
        }

        // skip if product is a pack without
        if (!Pack::isPack($id_product) || (Pack::isPack($id_product) && Validate::isLoadedObject($product = new Product((int) $id_product))
            && $product->pack_stock_type == Pack::STOCK_TYPE_PACK_ONLY || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH ||
                    ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_ONLY || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)))) {
            // Gets client_orders_qty
            $query = new DbQuery();
            $query->select('od.product_quantity, od.product_quantity_refunded');
            $query->from('order_detail', 'od');
            $query->leftjoin('orders', 'o', 'o.id_order = od.id_order');
            $query->where('od.product_id = ' . (int) $id_product);
            if (0 != $id_product_attribute) {
                $query->where('od.product_attribute_id = ' . (int) $id_product_attribute);
            }
            $query->leftJoin('order_history', 'oh', 'oh.id_order = o.id_order AND oh.id_order_state = o.current_state');
            $query->leftJoin('order_state', 'os', 'os.id_order_state = oh.id_order_state');
            $query->where('os.shipped != 1');
            $query->where('o.valid = 1 OR (os.id_order_state != ' . (int) Configuration::get('PS_OS_ERROR') . '
						   AND os.id_order_state != ' . (int) Configuration::get('PS_OS_CANCELED') . ')');
            $query->groupBy('od.id_order_detail');
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            if (count($res)) {
                foreach ($res as $row) {
                    $client_orders_qty += ($row['product_quantity'] - $row['product_quantity_refunded']);
                }
            }
        }
        // Gets supply_orders_qty
        $query = new DbQuery();

        $query->select('sod.quantity_expected, sod.quantity_received');
        $query->from('supply_order', 'so');
        $query->leftjoin('supply_order_detail', 'sod', 'sod.id_supply_order = so.id_supply_order');
        $query->leftjoin('supply_order_state', 'sos', 'sos.id_supply_order_state = so.id_supply_order_state');
        $query->where('sos.pending_receipt = 1');
        $query->where('sod.id_product = ' . (int) $id_product . ' AND sod.id_product_attribute = ' . (int) $id_product_attribute);

        $supply_orders_qties = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $supply_orders_qty = 0;
        foreach ($supply_orders_qties as $qty) {
            if ($qty['quantity_expected'] > $qty['quantity_received']) {
                $supply_orders_qty += ($qty['quantity_expected'] - $qty['quantity_received']);
            }
        }

        // Gets {physical OR usable}_qty
        $qty = $this->getProductPhysicalQuantities($id_product, $id_product_attribute, $ids_warehouse, $usable);

        //real qty = actual qty in stock - current client orders + current supply orders
        return $qty - $client_orders_qty + $supply_orders_qty;
    }

    /**
     * @see StockManagerInterface::getProductCoverage()
     * Here, $coverage is a number of days
     *
     * @return int number of days left (-1 if infinite)
     */
    public function getProductCoverage($id_product, $id_product_attribute, $coverage, $id_warehouse = null)
    {
        if (!$id_product_attribute) {
            $id_product_attribute = 0;
        }

        if ($coverage == 0 || !$coverage) {
            $coverage = 7;
        } // Week by default

        // gets all stock_mvt for the given coverage period
        $query = '
			SELECT SUM(view.quantity) as quantity_out
			FROM
			(	SELECT sm.`physical_quantity` as quantity
				FROM `' . _DB_PREFIX_ . 'stock_mvt` sm
				LEFT JOIN `' . _DB_PREFIX_ . 'stock` s ON (sm.`id_stock` = s.`id_stock`)
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = s.`id_product`)
				' . Shop::addSqlAssociation('product', 'p') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				' . Shop::addSqlAssociation('product_attribute', 'pa', false) . '
				WHERE sm.`sign` = -1
				AND sm.`id_stock_mvt_reason` != ' . Configuration::get('PS_STOCK_MVT_TRANSFER_FROM') . '
				AND TO_DAYS("' . date('Y-m-d') . ' 00:00:00") - TO_DAYS(sm.`date_add`) <= ' . (int) $coverage . '
				AND s.`id_product` = ' . (int) $id_product . '
				AND s.`id_product_attribute` = ' . (int) $id_product_attribute . '
				GROUP BY sm.`id_stock_mvt`
			) as view';

        $quantity_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        if (!$quantity_out) {
            return -1;
        }

        $quantity_per_day = Tools::ps_round($quantity_out / $coverage);
        $physical_quantity = $this->getProductPhysicalQuantities($id_product, $id_product_attribute, null, true);
        $time_left = ($quantity_per_day == 0) ? (-1) : Tools::ps_round($physical_quantity / $quantity_per_day);

        return $time_left;
    }

    /**
     * For a given stock, calculates its new WA(Weighted Average) price based on the new quantities and price
     * Formula : (physicalStock * lastCump + quantityToAdd * unitPrice) / (physicalStock + quantityToAdd).
     *
     * @param Stock|PrestaShopCollection $stock
     * @param int $quantity
     * @param float $price_te
     *
     * @return int WA
     */
    protected function calculateWA(Stock $stock, $quantity, $price_te)
    {
        return (float) Tools::ps_round(((($stock->physical_quantity * $stock->price_te) + ($quantity * $price_te)) / ($stock->physical_quantity + $quantity)), 6);
    }

    /**
     * For a given product, retrieves the stock collection.
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_warehouse [no longer used]
     * @param int $price_te Optional
     *
     * @return PrestaShopCollection Collection of Stock
     */
    protected function getStockCollection($id_product, $id_product_attribute, $id_warehouse = null, $price_te = null)
    {
        $stocks = new PrestaShopCollection('Stock');
        $stocks->where('id_product', '=', $id_product);
        $stocks->where('id_product_attribute', '=', $id_product_attribute);
        if ($price_te) {
            $stocks->where('price_te', '=', $price_te);
        }

        return $stocks;
    }

    /**
     * For a given product, retrieves the stock in function of the delivery option.
     *
     * @deprecated Since 8.0, will be removed in 9.0
     *
     * @param int $id_product
     * @param int $id_product_attribute optional
     * @param array $delivery_option
     *
     * @return int quantity
     */
    public static function getStockByCarrier($id_product = 0, $id_product_attribute = 0, $delivery_option = null)
    {
        @trigger_error(__FUNCTION__ . 'is deprecated since version 8.0 and will be removed in 9.0.', E_USER_DEPRECATED);

        if (!(int) $id_product || !is_array($delivery_option) || !is_int($id_product_attribute)) {
            return false;
        }

        return 0;
    }
}
