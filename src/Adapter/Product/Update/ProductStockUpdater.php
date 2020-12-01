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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use Product;
use StockAvailable;

/**
 * Updates settings related to Product stock
 */
class ProductStockUpdater
{
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
     * @var bool
     */
    private $advancedStockEnabled;

    /**
     * @param StockManager $stockManager
     * @param ProductRepository $productRepository
     * @param StockAvailableRepository $stockAvailableRepository
     * @param bool $advancedStockEnabled
     */
    public function __construct(
        StockManager $stockManager,
        ProductRepository $productRepository,
        StockAvailableRepository $stockAvailableRepository,
        bool $advancedStockEnabled
    ) {
        $this->stockManager = $stockManager;
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->advancedStockEnabled = $advancedStockEnabled;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @throws CoreException
     * @throws ProductConstraintException
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     * @throws ProductStockException
     */
    public function update(Product $product, array $propertiesToUpdate, bool $addMovement = true): void
    {
        // When advanced stock is disabled depend_on_stock must be disabled automatically
        if (in_array('advanced_stock_management', $propertiesToUpdate)
            && !in_array('depends_on_stock', $propertiesToUpdate)
            && false === (bool) $product->advanced_stock_management) {
            $product->depends_on_stock = false;
            $propertiesToUpdate[] = 'depends_on_stock';
        }

        $stockAvailable = $this->getStockAvailable($product);
        $this->productRepository->partialUpdate($product, $propertiesToUpdate, CannotUpdateProductException::FAILED_UPDATE_STOCK);

        // It is very important to update StockAvailable after product, because the validation is performed in ProductRepository::partialUpdate
        $this->updateStockAvailable($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        if ($this->advancedStockEnabled && $product->depends_on_stock) {
            StockAvailable::synchronize($product->id);
        }
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    private function updateStockAvailable(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        $stockUpdateRequired = false;
        if (in_array('depends_on_stock', $propertiesToUpdate)) {
            $stockAvailable->depends_on_stock = (bool) $product->depends_on_stock;
            $stockUpdateRequired = true;
        }
        if (in_array('out_of_stock', $propertiesToUpdate)) {
            $stockAvailable->out_of_stock = (int) $product->out_of_stock;
            $stockUpdateRequired = true;
        }
        if (in_array('location', $propertiesToUpdate)) {
            $stockAvailable->location = $product->location;
            $stockUpdateRequired = true;
        }

        // Quantity is handled separately as it is also related to Stock movements
        if (in_array('quantity', $propertiesToUpdate)) {
            $this->updateQuantity($product, $stockAvailable, $propertiesToUpdate, $addMovement);
            $stockUpdateRequired = true;
        }

        if ($stockUpdateRequired) {
            $this->stockAvailableRepository->update($stockAvailable);
        }
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     */
    private function updateQuantity(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): void
    {
        $deltaQuantity = (int) $product->quantity - (int) $stockAvailable->quantity;
        $stockAvailable->quantity = (int) $product->quantity;

        if ($addMovement && 0 !== $deltaQuantity) {
            $this->stockManager->saveMovement($stockAvailable->id_product, $stockAvailable->id_product_attribute, $deltaQuantity);
        }
    }

    /**
     * @param Product $product
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    private function getStockAvailable(Product $product): StockAvailable
    {
        $productId = new ProductId($product->id);
        try {
            return $this->stockAvailableRepository->getForProduct($productId);
        } catch (StockAvailableNotFoundException $e) {
            return $this->stockAvailableRepository->create($productId);
        }
    }
}
