<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Adapter_PackStockUpdater
{
    /**
     * This will update a Pack quantity and will decrease the quantity of containing Products if needed.
     *
     * @param Product $product A product pack object to update its quantity
     * @param StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param integer $delta_quantity The movement of the stock (negative for a decrease)
     * @param integer|null $id_shop Opional shop ID
     */
    public function updatePackQuantity($product, $stock_available, $delta_quantity, $id_shop = null)
    {
        $configuration = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        if ($product->pack_stock_type == 1 || $product->pack_stock_type == 2 || ($product->pack_stock_type == 3 && $configuration->get('PS_PACK_STOCK_TYPE') > 0)) {
            $packItemsManager = Adapter_ServiceLocator::get('Adapter_PackItemsManager');
            $products_pack = $packItemsManager->getPackItems($product);
            $stockAvailable = new \Core_Business_Stock_StockAvailable();
            foreach ($products_pack as $product_pack) {
                $stockManager = Adapter_ServiceLocator::get('Adapter_StockManager');
                $productStockAvailable = $stockManager->getStockAvailableByProduct($product_pack, $product_pack->id_pack_product_attribute, $id_shop);
                $productStockAvailable->quantity = $productStockAvailable->quantity + ($delta_quantity * $product_pack->pack_quantity);
                $productStockAvailable->update();
                
                Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$product_pack->id.'*');
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
     * (with the right declinaison) if there is not enough product in stocks.
     *
     * @param Product $product A product object to update its quantity
     * @param integer $id_product_attribute The product attribute to update
     * @param StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param integer|null $id_shop Opional shop ID
     */
    public function updatePacksQuantityContainingProduct($product, $id_product_attribute, $stock_available, $id_shop = null)
    {
        $configuration = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        $packItemsManager = Adapter_ServiceLocator::get('Adapter_PackItemsManager');
        $stockManager = Adapter_ServiceLocator::get('Adapter_StockManager');
        $packs = $packItemsManager->getPacksContainingItem($product, $id_product_attribute);
        foreach($packs as $pack) {
            // Decrease stocks of the pack only if pack is in linked stock mode (option called 'Decrement both')
            if ($pack->pack_stock_type !== 2) {
                continue;
            }

            // Decrease stocks of the pack only if there is not enough items to constituate the actual pack stocks.
            
            // How many packs can be constituated with the remaining product stocks
            $quantity_by_pack = $pack->pack_item_quantity;
            $max_pack_quantity = max(array(0, floor($stock_available->quantity / $quantity_by_pack)));

            $stock_available_pack = $stockManager->getStockAvailableByProduct($pack, null, $id_shop);
            if ($stock_available_pack->quantity > $max_pack_quantity) {
                $stock_available_pack->quantity = $max_pack_quantity;
                $stock_available_pack->update();

                Cache::clean('StockAvailable::getQuantityAvailableByProduct_'.(int)$pack->id.'*');
            }
        }
    }
}
