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

namespace PrestaShop\PrestaShop\Core\Stock;

use Access;
use Combination;
use Configuration;
use Context;
use DateTime;
use Employee;
use Mail;
use Pack;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Entity\StockMvt;
use Product;
use StockAvailable;

/**
 * Class StockManager Refactored features about product stocks.
 */
class StockManager
{
    /**
     * This will update a Pack quantity and will decrease the quantity of containing Products if needed.
     *
     * @param Product $product A product pack object to update its quantity
     * @param StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param int $delta_quantity The movement of the stock (negative for a decrease)
     * @param int|null $id_shop Optional shop ID
     */
    public function updatePackQuantity($product, $stock_available, $delta_quantity, $id_shop = null)
    {
        /** @TODO We should call the needed classes with the Symfony dependency injection instead of the Homemade Service Locator */
        $serviceLocator = new ServiceLocator();
        $configuration = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');

        if ($product->pack_stock_type == Pack::STOCK_TYPE_PRODUCTS_ONLY
            || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
            || ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                && $configuration->get('PS_PACK_STOCK_TYPE') > 0)
        ) {
            $packItemsManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager');
            $stockManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
            $cacheManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\CacheManager');

            $products_pack = $packItemsManager->getPackItems($product);
            foreach ($products_pack as $product_pack) {
                $productStockAvailable = $stockManager->getStockAvailableByProduct($product_pack, $product_pack->id_pack_product_attribute, $id_shop);
                $productStockAvailable->quantity = $productStockAvailable->quantity + ($delta_quantity * $product_pack->pack_quantity);
                $productStockAvailable->update();

                $cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_' . (int) $product_pack->id . '*');
            }
        }

        $stock_available->quantity = $stock_available->quantity + $delta_quantity;

        if ($product->pack_stock_type == Pack::STOCK_TYPE_PACK_ONLY
            || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
            || (
                $product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                && ($configuration->get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_ONLY
                    || $configuration->get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
            )
        ) {
            $stock_available->update();
        }
    }

    /**
     * This will decrease (if needed) Packs containing this product
     * (with the right declination) if there is not enough product in stocks.
     *
     * @param Product $product A product object to update its quantity
     * @param int $id_product_attribute The product attribute to update
     * @param StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param int|null $id_shop Optional shop ID
     */
    public function updatePacksQuantityContainingProduct($product, $id_product_attribute, $stock_available, $id_shop = null)
    {
        /** @TODO We should call the needed classes with the Symfony dependency injection instead of the Homemade Service Locator */
        $serviceLocator = new ServiceLocator();

        $configuration = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
        $packItemsManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PackItemsManager');
        $stockManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
        $cacheManager = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\CacheManager');

        $packs = $packItemsManager->getPacksContainingItem($product, $id_product_attribute);
        foreach ($packs as $pack) {
            // Decrease stocks of the pack only if pack is in linked stock mode (option called 'Decrement both')
            if (!((int) $pack->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH)
                && !((int) $pack->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                    && $configuration->get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
            ) {
                continue;
            }

            // Decrease stocks of the pack only if there is not enough items to make the actual pack stocks.

            // How many packs can be made with the remaining product stocks
            $quantity_by_pack = $pack->pack_item_quantity;
            $max_pack_quantity = max([0, floor($stock_available->quantity / $quantity_by_pack)]);

            $stock_available_pack = $stockManager->getStockAvailableByProduct($pack, null, $id_shop);
            if ($stock_available_pack->quantity > $max_pack_quantity) {
                $stock_available_pack->quantity = $max_pack_quantity;
                $stock_available_pack->update();

                $cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_' . (int) $pack->id . '*');
            }
        }
    }

    /**
     * Will update Product available stock int he given declinaison. If product is a Pack, could decrease the sub products.
     * If Product is contained in a Pack, Pack could be decreased or not (only if sub product stocks become not sufficient).
     *
     * @param Product $product The product to update its stockAvailable
     * @param int $id_product_attribute The declinaison to update (null if not)
     * @param int $delta_quantity The quantity change (positive or negative)
     * @param int|null $id_shop Optional
     * @param bool $add_movement Optional
     * @param array $params Optional
     */
    public function updateQuantity($product, $id_product_attribute, $delta_quantity, $id_shop = null, $add_movement = false, $params = [])
    {
        /** @TODO We should call the needed classes with the Symfony dependency injection instead of the Homemade Service Locator */
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

        $hookManager->exec(
            'actionUpdateQuantity',
            [
                'id_product' => $product->id,
                'id_product_attribute' => $id_product_attribute,
                'quantity' => $stockAvailable->quantity,
            ]
        );

        if ($this->checkIfMustSendLowStockAlert($product, $id_product_attribute, $stockAvailable->quantity)) {
            $this->sendLowStockAlert($product, $id_product_attribute, $stockAvailable->quantity);
        }

        $cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_' . (int) $product->id . '*');
    }

    /**
     * @param Product $product
     * @param int $id_product_attribute
     * @param int $newQuantity
     *
     * @return bool
     */
    protected function checkIfMustSendLowStockAlert($product, $id_product_attribute, $newQuantity)
    {
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            return false;
        }

        // Do not send mail if multiples product are created / imported.
        if (defined('PS_MASS_PRODUCT_CREATION')) {
            return false;
        }

        $productHasAttributes = $product->hasAttributes();
        if ($productHasAttributes && $id_product_attribute) {
            $combination = new Combination($id_product_attribute);

            return $this->isCombinationQuantityUnderAlertThreshold($combination, $newQuantity);
        } elseif (!$productHasAttributes && !$id_product_attribute) {
            return $this->isProductQuantityUnderAlertThreshold($product, $newQuantity);
        }

        return false;
    }

    /**
     * @param Product $product
     * @param int $newQuantity
     *
     * @return bool
     */
    protected function isProductQuantityUnderAlertThreshold($product, $newQuantity)
    {
        // low_stock_threshold empty to disable (can be negative, null or zero)
        if ($product->low_stock_alert
            && $product->low_stock_threshold !== ''
            && $product->low_stock_threshold !== null
            && $newQuantity <= (int) $product->low_stock_threshold
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Combination $combination
     * @param int $newQuantity
     *
     * @return bool
     */
    protected function isCombinationQuantityUnderAlertThreshold(Combination $combination, $newQuantity)
    {
        // low_stock_threshold empty to disable (can be negative, null or zero)
        if ($combination->low_stock_alert
            && $combination->low_stock_threshold !== ''
            && $combination->low_stock_threshold !== null
            && $newQuantity <= (int) $combination->low_stock_threshold
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Product $product
     * @param int $id_product_attribute
     * @param int $newQuantity
     *
     * @throws \Exception
     * @throws \PrestaShopException
     */
    protected function sendLowStockAlert($product, $id_product_attribute, $newQuantity)
    {
        $context = Context::getContext();
        $idShop = (int) $context->shop->id;
        $idLang = (int) $context->language->id;
        $configuration = Configuration::getMultiple(
            [
                'MA_LAST_QTIES',
                'PS_STOCK_MANAGEMENT',
                'PS_SHOP_EMAIL',
                'PS_SHOP_NAME',
            ],
            null,
            null,
            $idShop
        );
        $productName = Product::getProductName($product->id, $id_product_attribute, $idLang);
        if ($id_product_attribute) {
            $combination = new Combination($id_product_attribute);
            $lowStockThreshold = $combination->low_stock_threshold;
        } else {
            $lowStockThreshold = $product->low_stock_threshold;
        }
        $templateVars = [
            '{qty}' => $newQuantity,
            '{last_qty}' => $lowStockThreshold,
            '{product}' => $productName,
        ];

        // send email to every employee who have permission for this
        foreach (Employee::getEmployees() as $employeeData) {
            $employee = new Employee($employeeData['id_employee']);

            if (Access::isGranted('ROLE_MOD_TAB_ADMINSTOCKMANAGEMENT_READ', $employee->id_profile)) {
                $templateVars['{firstname}'] = $employee->firstname;
                $templateVars['{lastname}'] = $employee->lastname;

                Mail::Send(
                    $idLang,
                    'productoutofstock',
                    Mail::l('Product out of stock', $idLang),
                    $templateVars,
                    $employee->email,
                    null,
                    (string) $configuration['PS_SHOP_EMAIL'],
                    (string) $configuration['PS_SHOP_NAME'],
                    null,
                    null,
                    __DIR__ . '/mails/',
                    false,
                    $idShop
                );
            }
        }
    }

    /**
     * Public method to save a Movement.
     *
     * @param $productId
     * @param $productAttributeId
     * @param $deltaQuantity
     * @param array $params
     *
     * @return bool
     */
    public function saveMovement($productId, $productAttributeId, $deltaQuantity, $params = [])
    {
        if ($deltaQuantity != 0) {
            $stockMvt = $this->prepareMovement($productId, $productAttributeId, $deltaQuantity, $params);

            if ($stockMvt) {
                $sfContainer = SymfonyContainer::getInstance();
                if (null !== $sfContainer) {
                    $stockMvtRepository = $sfContainer->get('prestashop.core.api.stock_movement.repository');

                    return $stockMvtRepository->saveStockMvt($stockMvt);
                }
            }
        }

        return false;
    }

    /**
     * Prepare a Movement for registration.
     *
     * @param $productId
     * @param $productAttributeId
     * @param $deltaQuantity
     * @param array $params
     *
     * @return bool|StockMvt
     */
    private function prepareMovement($productId, $productAttributeId, $deltaQuantity, $params = [])
    {
        $product = (new ProductDataProvider())->getProductInstance($productId);

        if ($product->id) {
            $stockManager = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
            $stockAvailable = $stockManager->getStockAvailableByProduct($product, $productAttributeId);

            if ($stockAvailable->id) {
                $stockMvt = new StockMvt();

                $stockMvt->setIdStock((int) $stockAvailable->id);

                if (!empty($params['id_order'])) {
                    $stockMvt->setIdOrder((int) $params['id_order']);
                }

                if (!empty($params['id_stock_mvt_reason'])) {
                    $stockMvt->setIdStockMvtReason((int) $params['id_stock_mvt_reason']);
                }

                if (!empty($params['id_supply_order'])) {
                    $stockMvt->setIdSupplyOrder((int) $params['id_supply_order']);
                }

                $stockMvt->setSign($deltaQuantity >= 1 ? 1 : -1);
                $stockMvt->setPhysicalQuantity(abs($deltaQuantity));

                $stockMvt->setDateAdd(new DateTime());

                $employee = (new ContextAdapter())->getContext()->employee;
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
