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

namespace PrestaShop\PrestaShop\Adapter;

use Db;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopAdapter;
use PrestaShopBundle\Service\DataProvider\StockInterface;
use StockAvailable;

/**
 * Data provider for new Architecture, about Product stocks.
 *
 * This class will provide data from DB / ORM about Product stocks.
 */
class StockManager implements StockInterface
{
    /**
     * Gets available stock for a given product / combination / shop.
     *
     * @param object $product
     * @param int|null $id_product_attribute
     * @param int|null $id_shop
     *
     * @return StockAvailable
     */
    public function getStockAvailableByProduct($product, $id_product_attribute = null, $id_shop = null)
    {
        $stockAvailable = $this->newStockAvailable($this->getStockAvailableIdByProductId($product->id, $id_product_attribute, $id_shop));

        if (!$stockAvailable->id) {
            $shopAdapter = new ShopAdapter();
            $stockAvailable->id_product = (int) $product->id;
            $stockAvailable->id_product_attribute = (int) $id_product_attribute;

            $outOfStock = $this->outOfStock((int) $product->id, $id_shop);
            $stockAvailable->out_of_stock = (int) $outOfStock;

            if ($id_shop === null) {
                $shop_group = $shopAdapter->getContextShopGroup();
            } else {
                $shop_group = $shopAdapter->ShopGroup((int) $shopAdapter->getGroupFromShop((int) $id_shop));
            }

            // if quantities are shared between shops of the group
            if ($shop_group->share_stock) {
                $stockAvailable->id_shop = 0;
                $stockAvailable->id_shop_group = (int) $shop_group->id;
            } else {
                $stockAvailable->id_shop = (int) $id_shop;
                $stockAvailable->id_shop_group = 0;
            }
            $stockAvailable->add();
        }

        return $stockAvailable;
    }

    /**
     * Returns True if Stocks are managed by a module (or by legacy ASM).
     *
     * @return bool True if Stocks are managed by a module (or by legacy ASM)
     *
     * @deprecated Since 9.0 and will be removed in 10.0
     */
    public function isAsmGloballyActivated()
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return false;
    }

    /**
     * @param int $shopId
     * @param int $errorState
     * @param int $cancellationState
     * @param int|null $idProduct
     * @param int|null $idOrder
     *
     * @return bool
     */
    public function updatePhysicalProductQuantity($shopId, $errorState, $cancellationState, $idProduct = null, $idOrder = null)
    {
        $this->updateReservedProductQuantity($shopId, $errorState, $cancellationState, $idProduct, $idOrder);

        $updatePhysicalQuantityQuery = '
            UPDATE {table_prefix}stock_available sa
            SET sa.physical_quantity = sa.quantity + sa.reserved_quantity
            WHERE sa.id_shop = ' . (int) $shopId . '
        ';

        if ($idProduct) {
            $updatePhysicalQuantityQuery .= ' AND sa.id_product = ' . (int) $idProduct;
        }

        // Separating the extraction of the list of idProducts on separate queries prior to the UPDATE query
        // as the query plan with the database provides worse performance
        if ($idOrder) {
            $productsList = [];
            $getProductsToUpdateQuery = 'SELECT product_id FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order = ' . (int) $idOrder;
            $productsToUpdate = Db::getInstance()->executeS($getProductsToUpdateQuery);
            foreach ($productsToUpdate as $productToUpdate)
                array_push($productsList, $productToUpdate['product_id']);
            $getProductsToUpdateQuery = 'SELECT id_product_item FROM ' . _DB_PREFIX_ . 'pack pp 
                                         JOIN ' . _DB_PREFIX_ . 'order_detail od ON (od.product_id = pp.id_product_pack) 
                                         WHERE od.id_order = ' . (int) $idOrder;
            $productsToUpdate = Db::getInstance()->executeS($getProductsToUpdateQuery);
            foreach ($productsToUpdate as $productToUpdate)
                array_push($productsList, $productToUpdate['id_product_item']);
            $updatePhysicalQuantityQuery .= ' AND sa.id_product IN (' . implode(', ', $productsList) . ')';
        }

        $updatePhysicalQuantityQuery = str_replace('{table_prefix}', _DB_PREFIX_, $updatePhysicalQuantityQuery);

        return Db::getInstance()->execute($updatePhysicalQuantityQuery);
    }

    /**
     * @param int $shopId
     * @param int $errorState
     * @param int $cancellationState
     * @param int|null $idProduct
     * @param int|null $idOrder
     *
     * @return bool
     */
    private function updateReservedProductQuantity($shopId, $errorState, $cancellationState, $idProduct = null, $idOrder = null)
    {
        $strParams = [
            '{table_prefix}' => _DB_PREFIX_,
            ':shop_id' => (int) $shopId,
            ':error_state' => (int) $errorState,
            ':cancellation_state' => (int) $cancellationState,
        ];

        $getProductsToUpdateQuery = 'SELECT od.product_id as id_product, od.product_attribute_id as id_product_attribute, SUM(od.product_quantity - od.product_quantity_refunded) as net_quantity
            FROM {table_prefix}orders o
            INNER JOIN {table_prefix}order_detail od ON od.id_order = o.id_order
            INNER JOIN {table_prefix}order_state os ON os.id_order_state = o.current_state
            WHERE o.id_shop = :shop_id AND
            os.shipped != 1 AND (
                o.valid = 1 OR (
                    os.id_order_state != :error_state AND
                    os.id_order_state != :cancellation_state
                )
            )
            ';

        $getProductsFromPacksToUpdateQuery = 'SELECT pp.id_product_item as id_product, pp.id_product_attribute_item as id_product_attribute, SUM((od.product_quantity - od.product_quantity_refunded)*pp.quantity) as net_quantity
            FROM {table_prefix}orders o
            INNER JOIN {table_prefix}order_detail od ON od.id_order = o.id_order
            INNER JOIN {table_prefix}order_state os ON os.id_order_state = o.current_state
            JOIN {table_prefix}pack pp ON pp.id_product_pack = od.product_id
            WHERE o.id_shop = :shop_id AND
            os.shipped != 1 AND (
                o.valid = 1 OR (
                    os.id_order_state != :error_state AND
                    os.id_order_state != :cancellation_state
                )
            )
            ';

        if ($idProduct) {
            $getProductsToUpdateQuery .= ' AND od.product_id = ' . (int) $idProduct;

            $getProductsFromPacksToUpdateQuery .= ' AND pp.id_product_item IN (SELECT id_product_item from ps_pack pp join ps_order_detail od on (od.product_id = pp.id_product_pack AND pp.id_product_item = ' . (int) $idProduct . '))';
        }

        if ($idOrder) {
            $getProductsToUpdateQuery .= ' AND od.product_id IN (SELECT product_id FROM {table_prefix}order_detail WHERE id_order = ' . (int) $idOrder . ')';

            $getProductsFromPacksToUpdateQuery .= 'AND pp.id_product_item IN (SELECT id_product_item from ps_pack pp join ps_order_detail od on (od.product_id = pp.id_product_pack) where od.id_order = ' . (int) $idOrder . ')';
        }

        $getProductsToUpdateQuery .= ' GROUP BY od.product_id, od.product_attribute_id';
        $getProductsFromPacksToUpdateQuery .= ' GROUP BY pp.id_product_item, pp.id_product_attribute_item';

        $getProductsToUpdateQuery = strtr($getProductsToUpdateQuery, $strParams);

        $productsToUpdateStock = Db::getInstance()->executeS($getProductsToUpdateQuery);

        $getProductsFromPacksToUpdateQuery = strtr($getProductsFromPacksToUpdateQuery, $strParams);

        $productsToUpdateStockFromPacks = Db::getInstance()->executeS($getProductsFromPacksToUpdateQuery);

        if ($productsToUpdateStock AND $productsToUpdateStockFromPacks) {
            foreach ($productsToUpdateStock as $key1 => $pToUpdate) {
                foreach ($productsToUpdateStockFromPacks as $key2 => $pToUpdateFromPack) {

                    if (($pToUpdate['id_product'] == $pToUpdateFromPack['id_product'])
                             AND ($pToUpdate['id_product_attribute'] == $pToUpdateFromPack['id_product_attribute'])) 
                    {
                        $productsToUpdateStock[$key1]['net_quantity'] = $pToUpdate['net_quantity'] + $pToUpdateFromPack['net_quantity'];
                        unset($productsToUpdateStockFromPacks[$key2]);
                    }
                }
            }
            $productsToUpdateStock = array_merge($productsToUpdateStock, $productsToUpdateStockFromPacks);
        }

        // The following is only required to consider cases where there isn't any order in "not shipped state" with such product. The reserved value to be updated
        // should be 0 in these cases but the initial INNER JOIN query doesn't return any aggregated value for that idProduct as there is no order
        // in "not shipped state" so the reserved quantity would not be updated on the Available Stock.
        if ($idProduct) {
            $found = false;
            foreach ($productsToUpdateStock as $pToUpdate) {
                if ($pToUpdate['id_product'] == $idProduct)
                    $found = true;
            }    
            if ($found == false) {
                $productsAttributes = Db::getInstance()->executeS('SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . (int) $idProduct);
                foreach ($productsAttributes as $productAttribute) {
                    array_push($productsToUpdateStock, array('id_product' => $idProduct, 'id_product_attribute' => $productAttribute['id_product_attribute'], 'net_quantity' => 0));
                }
            }
        }

        // The following is only required to consider cases where there isn't any order in "not shipped state" for a product in the order. The reserved value to be updated
        // should be 0 in these cases but the initial INNER JOIN query doesn't return any aggregated value for that idProduct as there is no order
        // in "not shipped state" so the reserved quantity would not be updated on the Available Stock.
        if ($idOrder) {
            $productsAttributesQuery = '
                SELECT od.product_id as id_product, od.product_attribute_id as id_product_attribute
                FROM ' . _DB_PREFIX_ . 'orders o
                INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order = o.id_order
                WHERE o.id_shop = ' . (int) $shopId . '
                AND od.product_id IN (SELECT product_id FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order = ' . (int) $idOrder .')';
            
            $productsAttributes = Db::getInstance()->executeS($productsAttributesQuery);

            $productsAttributesQuery = '
                SELECT pp.id_product_item as id_product, pp.id_product_attribute_item as id_product_attribute
                FROM ' . _DB_PREFIX_ . 'orders o
                INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order = o.id_order
                JOIN ' . _DB_PREFIX_ . 'pack pp ON pp.id_product_pack = od.product_id
                WHERE o.id_shop = ' . (int) $shopId . '
                AND pp.id_product_item IN (SELECT id_product_item from ps_pack pp join ps_order_detail od on (od.product_id = pp.id_product_pack) where od.id_order = ' . (int) $idOrder . ')';

            $productsAttributes = array_merge($productsAttributes, Db::getInstance()->executeS($productsAttributesQuery));

            foreach ($productsAttributes as $productAttribute) {
                $found = false;
                foreach ($productsToUpdateStock as $pToUpdate) {
                    if ($pToUpdate['id_product'] == $productAttribute['id_product'] AND $pToUpdate['id_product_attribute'] == $productAttribute['id_product_attribute']) {
                        $found = true;
                    }
                }    
                if ($found == false) {
                    foreach ($productsAttributes as $productAttribute) {
                        array_push($productsToUpdateStock, array('id_product' => $productAttribute['id_product'], 'id_product_attribute' => $productAttribute['id_product_attribute'], 'net_quantity' => 0));
                    } 
                }
            }
        }

        foreach ($productsToUpdateStock as $pToUpdate) {
            $updateQuery = 'UPDATE ' . _DB_PREFIX_ . 'stock_available sa
                            SET sa.reserved_quantity = ' . (int) $pToUpdate['net_quantity'] . '
                            WHERE sa.id_shop = '. (int) $shopId . '
                            AND sa.id_product = ' . (int) $pToUpdate['id_product'] . '
                            AND sa.id_product_attribute = ' . (int) $pToUpdate['id_product_attribute'];

            Db::getInstance()->execute($updateQuery);
        }

        return true;
    }

    /**
     * Instance a new StockAvailable.
     *
     * @param bool|int|null $stockAvailableId
     *
     * @return StockAvailable
     */
    public function newStockAvailable($stockAvailableId = null)
    {
        if (is_int($stockAvailableId)) {
            return new StockAvailable($stockAvailableId);
        }

        return new StockAvailable();
    }

    /**
     * Use legacy getStockAvailableIdByProductId.
     *
     * @param int $productId
     * @param int|null $productAttributeId
     * @param int|null $shopId
     *
     * @return bool|int
     */
    public function getStockAvailableIdByProductId($productId, $productAttributeId = null, $shopId = null)
    {
        return StockAvailable::getStockAvailableIdByProductId($productId, $productAttributeId, $shopId);
    }

    /**
     * For a given product, get its "out of stock" flag.
     *
     * @param int $productId
     * @param int $shopId Optional : gets context if null @see Context::getContext()
     *
     * @return bool True if product is orderable when out of stock
     */
    public function outOfStock($productId, $shopId = null)
    {
        return StockAvailable::outOfStock($productId, $shopId);
    }
}
