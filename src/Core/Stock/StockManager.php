<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Stock;

use DateTime;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Entity\StockMvt;

/**
 * Class StockManager Refactored features about product stocks.
 *
 * @package PrestaShop\PrestaShop\Core\Stock
 */
class StockManager
{
    /**
     * This will update a Pack quantity and will decrease the quantity of containing Products if needed.
     *
     * @param \Product $product A product pack object to update its quantity
     * @param \StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param integer $delta_quantity The movement of the stock (negative for a decrease)
     * @param integer|null $id_shop Optional shop ID
     */
    public function updatePackQuantity($product, $stock_available, $delta_quantity, $id_shop = null)
    {
        // @TODO We should call the needed classes with the Symfony dependency injection instead of the Homemade Service Locator
        $serviceLocator = new ServiceLocator();

        $configuration = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
        if ($product->pack_stock_type == 1 || $product->pack_stock_type == 2 || ($product->pack_stock_type == 3 && $configuration->get('PS_PACK_STOCK_TYPE') > 0)) {

            $packItemsManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager');
            $stockManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
            $cacheManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\CacheManager');

            $products_pack = $packItemsManager->getPackItems($product);
            foreach ($products_pack as $product_pack) {
                $productStockAvailable = $stockManager->getStockAvailableByProduct($product_pack, $product_pack->id_pack_product_attribute, $id_shop);
                $productStockAvailable->quantity = $productStockAvailable->quantity + ($delta_quantity * $product_pack->pack_quantity);
                $productStockAvailable->update();

                $cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$product_pack->id.'*');
            }
        }

        $stock_available->quantity = $stock_available->quantity + $delta_quantity;

        if ($product->pack_stock_type == 0 || $product->pack_stock_type == 2 ||
            ($product->pack_stock_type == 3 && ($configuration->get('PS_PACK_STOCK_TYPE') == 0 || $configuration->get('PS_PACK_STOCK_TYPE') == 2))) {
            $stock_available->update();
        }
    }

    /**
     * This will decrease (if needed) Packs containing this product
     * (with the right declination) if there is not enough product in stocks.
     *
     * @param \Product $product A product object to update its quantity
     * @param integer $id_product_attribute The product attribute to update
     * @param \StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param integer|null $id_shop Optional shop ID
     */
    public function updatePacksQuantityContainingProduct($product, $id_product_attribute, $stock_available, $id_shop = null)
    {
        // @TODO We should call the needed classes with the Symfony dependency injection instead of the Homemade Service Locator
        $serviceLocator = new ServiceLocator();

        $configuration = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
        $packItemsManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager');
        $stockManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
        $cacheManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\CacheManager');

        $packs = $packItemsManager->getPacksContainingItem($product, $id_product_attribute);
        foreach ($packs as $pack) {
            // Decrease stocks of the pack only if pack is in linked stock mode (option called 'Decrement both')
            if (!((int)$pack->pack_stock_type == 2) &&
                !((int)$pack->pack_stock_type == 3 && $configuration->get('PS_PACK_STOCK_TYPE') == 2)
                ) {
                continue;
            }

            // Decrease stocks of the pack only if there is not enough items to make the actual pack stocks.

            // How many packs can be made with the remaining product stocks
            $quantity_by_pack = $pack->pack_item_quantity;
            $max_pack_quantity = max(array(0, floor($stock_available->quantity / $quantity_by_pack)));

            $stock_available_pack = $stockManager->getStockAvailableByProduct($pack, null, $id_shop);
            if ($stock_available_pack->quantity > $max_pack_quantity) {
                $stock_available_pack->quantity = $max_pack_quantity;
                $stock_available_pack->update();

                $cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$pack->id.'*');
            }
        }
    }

    /**
     * Will update Product available stock int he given declinaison. If product is a Pack, could decrease the sub products.
     * If Product is contained in a Pack, Pack could be decreased or not (only if sub product stocks become not sufficient).
     *
     * @param \Product $product The product to update its stockAvailable
     * @param integer $id_product_attribute The declinaison to update (null if not)
     * @param integer $delta_quantity The quantity change (positive or negative)
     * @param integer|null $id_shop Optional
     * @param boolean $add_movement Optional
     * @param array $params Optional
     */
    public function updateQuantity($product, $id_product_attribute, $delta_quantity, $id_shop = null, $add_movement = false, $params = array())
    {
        // @TODO We should call the needed classes with the Symfony dependency injection instead of the Homemade Service Locator
        $serviceLocator = new ServiceLocator();
        $stockManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
        $packItemsManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager');
        $cacheManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\CacheManager');
        $hookManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\HookManager');

        $stockAvailable = $stockManager->getStockAvailableByProduct($product, $id_product_attribute, $id_shop);

        // Update quantity of the pack products
        if ($packItemsManager->isPack($product)) {
            // The product is a pack
            $this->updatePackQuantity($product, $stockAvailable, $delta_quantity, $id_shop);
        } else {
            // The product is not a pack
            $stockAvailable->quantity = $stockAvailable->quantity + $delta_quantity;
            $stockAvailable->update();

            // Decrease case only: the stock of linked packs should be decreased too.
            if ($delta_quantity < 0) {
                // The product is not a pack, but the product combination is part of a pack (use of isPacked, not isPack)
                if ($packItemsManager->isPacked($product, $id_product_attribute)) {
                    $this->updatePacksQuantityContainingProduct($product, $id_product_attribute, $stockAvailable, $id_shop);
                }
            }
        }

        // Prepare movement and save it
        if (true === $add_movement && 0 != $delta_quantity) {
            $this->saveMovement($product->id, $id_product_attribute, $delta_quantity, $params);
        }

        $hookManager->exec('actionUpdateQuantity',
            array(
                'id_product' => $product->id,
                'id_product_attribute' => $id_product_attribute,
                'quantity' => $stockAvailable->quantity
            )
        );

        $cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$product->id.'*');
    }

    /**
     * Public method to save a Movement
     *
     * @param $productId
     * @param $productAttributeId
     * @param $deltaQuantity
     * @param array $params
     * @return bool
     */
    public function saveMovement($productId, $productAttributeId, $deltaQuantity, $params = array())
    {
        if ($deltaQuantity != 0) {
            $stockMvt = $this->prepareMovement($productId, $productAttributeId, $deltaQuantity, $params);

            if ($stockMvt) {
                global $kernel;
                if (!is_null($kernel) && $kernel instanceof \Symfony\Component\HttpKernel\HttpKernelInterface) {
                    $stockMvtRepository = $kernel->getContainer()->get('prestashop.core.api.stockMovement.repository');
                    return $stockMvtRepository->saveStockMvt($stockMvt);
                }
            }
        }

        return false;
    }

    /**
     * Prepare a Movement for registration
     *
     * @param $productId
     * @param $productAttributeId
     * @param $deltaQuantity
     * @param array $params
     * @return bool|StockMvt
     */
    private function prepareMovement($productId, $productAttributeId, $deltaQuantity, $params = array())
    {
        $product = (new ProductDataProvider)->getProductInstance($productId);

        if ($product->id) {

            $stockManager = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
            $stockAvailable = $stockManager->getStockAvailableByProduct($product, $productAttributeId);

            if ($stockAvailable->id) {

                $stockMvt = new StockMvt();

                $stockMvt->setIdStock((int)$stockAvailable->id);

                if (!empty($params['id_order'])) {
                    $stockMvt->setIdOrder((int)$params['id_order']);
                }

                if (!empty($params['id_stock_mvt_reason'])) {
                    $stockMvt->setIdStockMvtReason((int)$params['id_stock_mvt_reason']);
                }

                if (!empty($params['id_supply_order'])) {
                    $stockMvt->setIdSupplyOrder((int)$params['id_supply_order']);
                }

                $stockMvt->setSign($deltaQuantity >= 1 ? 1 : -1);
                $stockMvt->setPhysicalQuantity(abs($deltaQuantity));

                $stockMvt->setDateAdd(new DateTime());

                $employee = (new ContextAdapter)->getContext()->employee;
                if (!empty($employee)) {
                    $stockMvt->setIdEmployee($employee->id);
                    $stockMvt->setEmployeeFirstname($employee->firstname);
                    $stockMvt->setEmployeeLastname($employee->lastname);
                }

                return $stockMvt;
            }
        }

        return false;
    }
}
