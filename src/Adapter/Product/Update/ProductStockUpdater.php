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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
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
     * @throws ProductConstraintException
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     */
    public function update(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement = true): void
    {
        $this->productRepository->partialUpdate($product, $propertiesToUpdate, CannotUpdateProductException::FAILED_UPDATE_STOCK);

        // It is very important to update StockAvailable after product, because the validation is performed in ProductRepository::partialUpdate
        $this->updateStockAvailable($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        $advancedStockEnabled = (bool) $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT');
        if ($advancedStockEnabled && $product->depends_on_stock) {
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
        $stockUpdateRequired |= $this->updateQuantity($product, $stockAvailable, $propertiesToUpdate, $addMovement);

        if ($stockUpdateRequired) {
            $this->stockAvailableRepository->update($stockAvailable);
        }
    }

    /**
     * @param Product $product
     * @param StockAvailable $stockAvailable
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @return bool
     */
    private function updateQuantity(Product $product, StockAvailable $stockAvailable, array $propertiesToUpdate, bool $addMovement): bool
    {
        if (!in_array('quantity', $propertiesToUpdate)) {
            return false;
        }

        $deltaQuantity = (int) (int) $product->quantity - $stockAvailable->quantity;
        $stockAvailable->quantity = (int) $product->quantity;

        if ($addMovement && 0 !== $deltaQuantity) {
            $this->stockManager->saveMovement($stockAvailable->id_product, $stockAvailable->id_product_attribute, $deltaQuantity);
        }

        return true;
    }
}
