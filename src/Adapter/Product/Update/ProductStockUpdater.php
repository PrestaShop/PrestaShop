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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use Pack;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelFiller;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use Product;
use StockAvailable;

class ProductStockUpdater extends AbstractObjectModelFiller
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @param ConfigurationInterface $configuration
     * @param StockManager $stockManager
     * @param ProductRepository $productRepository
     * @param StockAvailableRepository $stockAvailableRepository
     */
    public function __construct(
        ConfigurationInterface $configuration,
        StockManager $stockManager,
        ProductRepository $productRepository,
        StockAvailableRepository $stockAvailableRepository
    ) {
        $this->configuration = $configuration;
        $this->stockManager = $stockManager;
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @throws CoreException
     * @throws ProductStockConstraintException
     */
    public function update(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement = true): void
    {
        $advancedStockEnabled = (bool) $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT');
        if ($advancedStockEnabled) {
            $this->updateAdvancedStock($product, $stockAvailable, $propertiesToUpdate, $addMovement);
        } else {
            $this->updateClassicStock($product, $stockAvailable, $propertiesToUpdate, $addMovement);
        }
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @throws CoreException
     * @throws ProductStockConstraintException
     */
    private function updateClassicStock(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        // Depends on stock is only available in advanced mode
        if (isset($propertiesToUpdate['depends_on_stock']) && $propertiesToUpdate['depends_on_stock']) {
            throw new ProductStockConstraintException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }

        if (isset($propertiesToUpdate['advanced_stock_management']) && $propertiesToUpdate['advanced_stock_management']) {
            throw new ProductStockConstraintException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }

        $propertiesToUpdate['depends_on_stock'] = false;
        $propertiesToUpdate['advanced_stock_management'] = false;

        $this->fillProperties($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        $this->productRepository->partialUpdate($product, $product->getFieldsToUpdate(), CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->stockAvailableRepository->update($stockAvailable);
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @throws CoreException
     * @throws ProductStockConstraintException
     */
    private function updateAdvancedStock(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        $productHasAdvancedStock = $propertiesToUpdate['advanced_stock_management'] ?? $product->advanced_stock_management;

        if (isset($propertiesToUpdate['depends_on_stock'])) {
            if ($propertiesToUpdate['depends_on_stock'] && !$productHasAdvancedStock) {
                throw new ProductStockConstraintException(
                    'You cannot perform this action when advanced_stock_management is disabled on the product',
                    ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED
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

        $this->fillProperties($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        $this->productRepository->partialUpdate($product, $product->getFieldsToUpdate(), CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->stockAvailableRepository->update($stockAvailable);

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
     * @param bool $addMovement
     */
    private function fillProperties(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        $this->fillProperty($product, 'depends_on_stock', $propertiesToUpdate);
        $this->fillProperty($product, 'advanced_stock_management', $propertiesToUpdate);
        $this->fillProperty($product, 'pack_stock_type', $propertiesToUpdate);
        $this->fillProperty($product, 'out_of_stock', $propertiesToUpdate);
        $this->fillProperty($product, 'minimal_quantity', $propertiesToUpdate);
        $this->fillProperty($product, 'location', $propertiesToUpdate);
        $this->fillProperty($product, 'low_stock_threshold', $propertiesToUpdate);
        $this->fillProperty($product, 'low_stock_alert', $propertiesToUpdate);
        $this->fillProperty($product, 'available_date', $propertiesToUpdate);
        $this->fillLocalizedProperty($product, 'available_now', $propertiesToUpdate);
        $this->fillLocalizedProperty($product, 'available_later', $propertiesToUpdate);
        $this->fillProperty($stockAvailable, 'depends_on_stock', $propertiesToUpdate);
        $this->fillProperty($stockAvailable, 'out_of_stock', $propertiesToUpdate);
        $this->fillProperty($stockAvailable, 'location', $propertiesToUpdate);

        // Quantity is handled separately as it is also related to Stock movements
        $this->updateQuantity($product, $stockAvailable, $propertiesToUpdate, $addMovement);
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     */
    private function updateQuantity(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        if (!isset($propertiesToUpdate['quantity'])) {
            return;
        }

        $deltaQuantity = (int) $propertiesToUpdate['quantity'] - $stockAvailable->quantity;

        $this->fillProperty($product, 'quantity', $propertiesToUpdate);
        $this->fillProperty($stockAvailable, 'quantity', $propertiesToUpdate);

        if ($addMovement && 0 !== $deltaQuantity) {
            $this->stockManager->saveMovement($stockAvailable->id_product, $stockAvailable->id_product_attribute, $deltaQuantity);
        }
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     *
     * @throws ProductPackConstraintException
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

        throw new ProductPackConstraintException(
            'You cannot link your pack to product stock because one of them has no advanced stock enabled',
            ProductPackConstraintException::INCOMPATIBLE_STOCK_TYPE
        );
    }
}
