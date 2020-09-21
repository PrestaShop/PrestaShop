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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Updater;

use Pack;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelPersister;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductStockException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;
use StockAvailable;

class ProductStockUpdater extends AbstractObjectModelPersister
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    public function update(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate): void
    {
        $advancedStockEnabled = (bool) $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT');
        if ($advancedStockEnabled) {
            $this->updateAdvancedStock($product, $stockAvailable, $propertiesToUpdate);
        } else {
            $this->updateClassicStock($product, $stockAvailable, $propertiesToUpdate);
        }
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    private function updateClassicStock(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate): void
    {
        // Depends on stock is only available in advanced mode
        if (isset($propertiesToUpdate['depends_on_stock']) && $propertiesToUpdate['depends_on_stock']) {
            throw new ProductStockException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }

        if (isset($propertiesToUpdate['advanced_stock_management']) && $propertiesToUpdate['advanced_stock_management']) {
            throw new ProductStockException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }

        $propertiesToUpdate['depends_on_stock'] = false;
        $propertiesToUpdate['advanced_stock_management'] = false;

        $this->fillProperties($product, $stockAvailable, $propertiesToUpdate);

        $this->updateObjectModel($product, CannotUpdateProductException::class, CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->updateObjectModel($stockAvailable, ProductStockException::class, ProductStockException::CANNOT_SAVE_STOCK_AVAILABLE);
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    private function updateAdvancedStock(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate): void
    {
        $productHasAdvancedStock = isset($propertiesToUpdate['advanced_stock_management']) ? $propertiesToUpdate['advanced_stock_management'] : $product->advanced_stock_management;

        if (isset($propertiesToUpdate['depends_on_stock'])) {
            if ($propertiesToUpdate['depends_on_stock'] && !$productHasAdvancedStock) {
                throw new ProductStockException(
                    'You cannot perform this action when advanced_stock_management is disabled on the product',
                    ProductStockException::ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED
                );
            }

            $this->checkPackStockType($product, $propertiesToUpdate);
        }
        if (!$productHasAdvancedStock) {
            $propertiesToUpdate['depends_on_stock'] = false;
        }
        if (isset($propertiesToUpdate['pack_stock_type'])) {
            $this->checkPackStockType($product, $propertiesToUpdate);
        }

        $this->fillProperties($product, $stockAvailable, $propertiesToUpdate);

        $this->updateObjectModel($product, CannotUpdateProductException::class, CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->updateObjectModel($stockAvailable, ProductStockException::class, ProductStockException::CANNOT_SAVE_STOCK_AVAILABLE);

        if (isset($propertiesToUpdate['depends_on_stock']) && $propertiesToUpdate['depends_on_stock']) {
            StockAvailable::synchronize($product->id);
        }
    }

    /**
     * Filling the object is the same for classic and advanced use cases
     *
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     */
    private function fillProperties(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate): void
    {
        $this->fillProperty($product, 'depends_on_stock', $propertiesToUpdate);
        $this->fillProperty($product, 'advanced_stock_management', $propertiesToUpdate);
        $this->fillProperty($product, 'pack_stock_type', $propertiesToUpdate);
        $this->fillProperty($product, 'out_of_stock', $propertiesToUpdate);
        $this->fillProperty($stockAvailable, 'depends_on_stock', $propertiesToUpdate);
        $this->fillProperty($stockAvailable, 'out_of_stock', $propertiesToUpdate);
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     *
     * @throws ProductStockException
     */
    private function checkPackStockType(Product $product, array $propertiesToUpdate): void
    {
        $dependsOnStock = isset($propertiesToUpdate['depends_on_stock']) ? $propertiesToUpdate['depends_on_stock'] : $product->depends_on_stock;
        // If the product doesn't depend on stock or is not a Pack no problem
        if (!$dependsOnStock || !Pack::isPack($product->id)) {
            return;
        }

        // Get pack stock type (or default configuration if needed)
        $packStockType = $product->pack_stock_type;
        if (isset($propertiesToUpdate['pack_stock_type'])) {
            $packStockType = $propertiesToUpdate['pack_stock_type'];
        }
        if ($packStockType === Pack::STOCK_TYPE_DEFAULT) {
            $packStockType = (int) $this->configuration->get('PS_PACK_STOCK_TYPE');
        }

        // Either the pack has its own stock, or else ALL products from the pack must depend on the stock as well
        if ($packStockType === Pack::STOCK_TYPE_PACK_ONLY || Pack::allUsesAdvancedStockManagement($product->id)) {
            return;
        }

        throw new ProductStockException(
            'You cannot link your pack to product stock because one of them has no advanced stock enabled',
            ProductStockException::INCOMPATIBLE_PACK_STOCK_TYPE
        );
    }
}
