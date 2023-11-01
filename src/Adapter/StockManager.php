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

        $updatePhysicalQuantityQuery = 'UPDATE {table_prefix}stock_available sa';

        if ($idOrder) {
            $updatePhysicalQuantityQuery .= '
                INNER JOIN (
                    SELECT product_id
                    FROM {table_prefix}order_detail
                    WHERE id_order = ' . (int) $idOrder . '
                ) od 
                ON sa.id_product = od.product_id
            ';
        }

        $updatePhysicalQuantityQuery .= '
            SET sa.physical_quantity = sa.quantity + sa.reserved_quantity
            WHERE sa.id_shop = ' . (int) $shopId . '
        ';

        if ($idProduct) {
            $updatePhysicalQuantityQuery .= ' AND sa.id_product = ' . (int) $idProduct;
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
        $updateReservedQuantityQuery = 'UPDATE {table_prefix}stock_available sa';

        if ($idOrder) {
            $updateReservedQuantityQuery .= '
                INNER JOIN (
                    SELECT product_id
                    FROM {table_prefix}order_detail
                    WHERE id_order = :order_id
                ) od2
                ON sa.id_product = od2.product_id
            ';
        }

        $updateReservedQuantityQuery .= '
            SET sa.reserved_quantity = (
                SELECT SUM(od.product_quantity - od.product_quantity_refunded)
                FROM {table_prefix}orders o
                INNER JOIN {table_prefix}order_detail od ON od.id_order = o.id_order
                INNER JOIN {table_prefix}order_state os ON os.id_order_state = o.current_state
                WHERE o.id_shop = :shop_id AND
                os.shipped != 1 AND (
                    o.valid = 1 OR (
                        os.id_order_state != :error_state AND
                        os.id_order_state != :cancellation_state
                    )
                ) AND sa.id_product = od.product_id AND
                sa.id_product_attribute = od.product_attribute_id
                GROUP BY od.product_id, od.product_attribute_id
            )
            WHERE sa.id_shop = :shop_id
        ';

        $strParams = [
            '{table_prefix}' => _DB_PREFIX_,
            ':shop_id' => (int) $shopId,
            ':error_state' => (int) $errorState,
            ':cancellation_state' => (int) $cancellationState,
        ];

        if ($idProduct) {
            $updateReservedQuantityQuery .= ' AND sa.id_product = :product_id';
            $strParams[':product_id'] = (int) $idProduct;
        }

        if ($idOrder) {
            $strParams[':order_id'] = (int) $idOrder;
        }

        $updateReservedQuantityQuery = strtr($updateReservedQuantityQuery, $strParams);

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
     * @return bool True if product is orderable when out of stock
     */
    public function outOfStock($productId, $shopId = null)
    {
        return StockAvailable::outOfStock($productId, $shopId);
    }
}
