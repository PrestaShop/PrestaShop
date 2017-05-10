<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use Db;

/**
 * Data provider for new Architecture, about Product stocks.
 *
 * This class will provide data from DB / ORM about Product stocks.
 */
class StockManager implements \PrestaShopBundle\Service\DataProvider\StockInterface
{
    /**
     * Gets available stock for a given product / combination / shop.
     *
     * @param object $product
     * @param null $id_product_attribute
     * @param null $id_shop
     * @return StockAvailable
     */
    public function getStockAvailableByProduct($product, $id_product_attribute = null, $id_shop = null)
    {
        return new \StockAvailableCore(\StockAvailableCore::getStockAvailableIdByProductId($product->id, $id_product_attribute, $id_shop));
    }

    /**
     * Returns True if Stocks are managed by a module (or by legacy ASM)
     *
     * @return boolean True if Stocks are managed by a module (or by legacy ASM)
     */
    public function isAsmGloballyActivated()
    {
        return (bool)\Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
    }

    /**
     * @param $shopId
     * @param $errorState
     * @param $cancellationState
     * @return bool
     */
    public function updatePhysicalProductQuantity($shopId, $errorState, $cancellationState)
    {
        $this->updateReservedProductQuantity($shopId, $errorState, $cancellationState);

        $updatePhysicalQuantityQuery = '
            UPDATE {table_prefix}stock_available sa
            SET sa.physical_quantity = sa.quantity - sa.reserved_quantity
        ';
        $updatePhysicalQuantityQuery = str_replace('{table_prefix}', _DB_PREFIX_, $updatePhysicalQuantityQuery);

        return Db::getInstance()->execute($updatePhysicalQuantityQuery);
    }

    /**
     * @param $shopId
     * @param $errorState
     * @param $cancellationState
     * @return bool
     */
    private function updateReservedProductQuantity($shopId, $errorState, $cancellationState)
    {
        $updateReservedQuantityQuery = '
            UPDATE {table_prefix}stock_available sa
            SET sa.reserved_quantity = (
                SELECT SUM(od.product_quantity - od.product_quantity_refunded)
                FROM {table_prefix}orders o,
                {table_prefix}order_history oh,
                {table_prefix}order_state os,
                {table_prefix}order_detail od
                WHERE
                o.id_order = oh.id_order AND
                o.current_state = oh.id_order_state AND
                oh.id_order_state = os.id_order_state AND
                o.id_order = od.id_order AND
                od.id_shop = :shop_id AND
                os.shipped != 1 AND (
                    o.valid = 1 OR (
                        os.id_order_state != :error_state AND
                        os.id_order_state != :cancellation_state
                    )
                ) AND sa.id_product = od.product_id AND
                sa.id_product_attribute = od.product_attribute_id
                GROUP BY sa.id_product, sa.id_product_attribute
            )
        ';

        $updateReservedQuantityQuery = str_replace(
            array(
                '{table_prefix}',
                ':shop_id',
                ':error_state',
                ':cancellation_state',
            ),
            array(
                _DB_PREFIX_,
                (int)$shopId,
                (int)$errorState,
                (int)$cancellationState
            ),
            $updateReservedQuantityQuery
        );

        return Db::getInstance()->execute($updateReservedQuantityQuery);
    }

    public function newStockMvt()
    {
        return new \StockMvt();
    }
}
