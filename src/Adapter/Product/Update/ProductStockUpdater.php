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

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelFiller;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
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
     * @var ProductValidator
     */
    private $productValidator;

    /**
     * @param ConfigurationInterface $configuration
     * @param StockManager $stockManager
     * @param ProductRepository $productRepository
     * @param StockAvailableRepository $stockAvailableRepository
     * @param ProductValidator $productValidator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        StockManager $stockManager,
        ProductRepository $productRepository,
        StockAvailableRepository $stockAvailableRepository,
        ProductValidator $productValidator
    ) {
        $this->configuration = $configuration;
        $this->stockManager = $stockManager;
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->productValidator = $productValidator;
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
     */
    private function updateClassicStock(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        $this->fillProperties($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        $this->productRepository->partialUpdate($product, $product->getFieldsToUpdate() ?: [], CannotUpdateProductException::FAILED_UPDATE_STOCK);
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
        $this->fillProperties($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        $this->productRepository->partialUpdate($product, $product->getFieldsToUpdate() ?: [], CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->stockAvailableRepository->update($stockAvailable);

        if ($product->depends_on_stock) {
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
        $this->fillProperty($product, 'pack_stock_type', $propertiesToUpdate);
        $this->fillProperty($product, 'out_of_stock', $propertiesToUpdate);
        $this->fillProperty($product, 'minimal_quantity', $propertiesToUpdate);
        $this->fillProperty($product, 'location', $propertiesToUpdate);
        $this->fillProperty($product, 'low_stock_threshold', $propertiesToUpdate);
        $this->fillProperty($product, 'low_stock_alert', $propertiesToUpdate);
        $this->fillProperty($product, 'available_date', $propertiesToUpdate);
        $this->fillLocalizedProperty($product, 'available_now', $propertiesToUpdate);
        $this->fillLocalizedProperty($product, 'available_later', $propertiesToUpdate);
        $stockAvailable->depends_on_stock = (bool) $product->depends_on_stock;
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
}
