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
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
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
     * @param null $id_product_attribute
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
     */
    public function isAsmGloballyActivated()
    {
        return (bool) (new ConfigurationAdapter())->get('PS_ADVANCED_STOCK_MANAGEMENT');
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
            UPDATE {_DB_PREFIX_}stock_available sa
                JOIN {_DB_PREFIX_}shop s ON sa.id_shop = s.id_shop {ID_SHOP_ASSOCIATION_CLAUSE}
                JOIN {_DB_PREFIX_}order_detail od ON sa.id_product = od.product_id {ID_PRODUCT_ASSOCIATION_CLAUSE} {ID_ORDER_ASSOCIATION_CLAUSE}
            SET
                sa.physical_quantity = sa.quantity + sa.reserved_quantity';

        $updatePhysicalQuantityQuery = str_replace('{_DB_PREFIX_}', _DB_PREFIX_, $updatePhysicalQuantityQuery);

        $id_shop_association_clause = '';
        if ($shopId) {
            $id_shop_association_clause = ' AND s.id_shop = ' . $shopId;
        }
        $updatePhysicalQuantityQuery = str_replace('{ID_SHOP_ASSOCIATION_CLAUSE}', $id_shop_association_clause, $updatePhysicalQuantityQuery);

        $id_product_association_clause = '';
        if ($idProduct) {
            $id_product_association_clause = ' AND sa.id_product = ' . $idProduct;
        }
        $updatePhysicalQuantityQuery = str_replace('{ID_PRODUCT_ASSOCIATION_CLAUSE}', $id_product_association_clause, $updatePhysicalQuantityQuery);

        $id_order_association_clause = '';
        if ($idOrder) {
            $id_order_association_clause = ' AND od.id_order = ' . $idOrder;
        }
        $updatePhysicalQuantityQuery = str_replace('{ID_ORDER_ASSOCIATION_CLAUSE}', $id_order_association_clause, $updatePhysicalQuantityQuery);

        $updatePhysicalQuantityQuery = str_replace('{_DB_PREFIX_}', _DB_PREFIX_, $updatePhysicalQuantityQuery);

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
        $updateReservedQuantityQuery = '
            UPDATE {_DB_PREFIX_}stock_available sa
                JOIN {_DB_PREFIX_}shop s ON (
                    s.id_shop = sa.id_shop {ID_SHOP_ASSOCIATION_CLAUSE}
                )
                JOIN {_DB_PREFIX_}order_detail od ON (
                    od.product_id = sa.id_product AND od.product_attribute_id = sa.id_product_attribute {ID_PRODUCT_ASSOCIATION_CLAUSE}
                )
                JOIN {_DB_PREFIX_}orders o ON (
                    o.id_order = od.id_order {ID_ORDER_ASSOCIATION_CLAUSE}
                )
            SET sa.reserved_quantity = (
                    SELECT
                        SUM(
                            od.product_quantity - od.product_quantity_refunded
                        )
                    FROM {_DB_PREFIX_}orders o
                        JOIN {_DB_PREFIX_}shop s ON s.id_shop = o.id_shop {ID_SHOP_ASSOCIATION_CLAUSE}
                        JOIN {_DB_PREFIX_}order_detail od ON (
                            od.id_order = o.id_order {ID_ORDER_ASSOCIATION_CLAUSE}
                        )
                        JOIN {_DB_PREFIX_}order_state os ON (
                            os.id_order_state = o.current_state AND os.shipped != 1
                        )
                    WHERE (
                            o.valid = 1
                            OR (
                                os.id_order_state != :error_state AND
                                os.id_order_state != :cancellation_state
                            )
                        )
                        AND sa.id_product = od.product_id
                        AND sa.id_product_attribute = od.product_attribute_id
                        {ID_PRODUCT_ASSOCIATION_CLAUSE}
                    GROUP BY
                        od.product_id,
                        od.product_attribute_id
                )
        ';

        $updateReservedQuantityQuery = str_replace('{_DB_PREFIX_}', _DB_PREFIX_, $updateReservedQuantityQuery);

        $id_shop_association_clause = '';
        if ($shopId) {
            $id_shop_association_clause = ' AND s.id_shop = ' . $shopId;
        }
        $updateReservedQuantityQuery = str_replace('{ID_SHOP_ASSOCIATION_CLAUSE}', $id_shop_association_clause, $updateReservedQuantityQuery);

        $id_product_association_clause = '';
        if ($idProduct) {
            $id_product_association_clause = ' AND sa.id_product = ' . $idProduct;
        }
        $updateReservedQuantityQuery = str_replace('{ID_PRODUCT_ASSOCIATION_CLAUSE}', $id_product_association_clause, $updateReservedQuantityQuery);

        $id_order_association_clause = '';
        if ($idOrder) {
            $id_order_association_clause = ' AND od.id_order = ' . $idOrder;
        }
        $updateReservedQuantityQuery = str_replace('{ID_ORDER_ASSOCIATION_CLAUSE}', $id_order_association_clause, $updateReservedQuantityQuery);

        $updateReservedQuantityQuery = str_replace(':error_state', $errorState, $updateReservedQuantityQuery);

        $updateReservedQuantityQuery = str_replace(':cancellation_state', $cancellationState, $updateReservedQuantityQuery);

        return Db::getInstance()->execute($updateReservedQuantityQuery);
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
     * @return bool : depends on stock @see $depends_on_stock
     */
    public function outOfStock($productId, $shopId = null)
    {
        return StockAvailable::outOfStock($productId, $shopId);
    }
}
