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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Update;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
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
     * @var Configuration
     */
    private $configuration;

    /**
     * @var bool
     */
    private $advancedStockEnabled;

    /**
     * @param StockManager $stockManager
     * @param ProductRepository $productRepository
     * @param StockAvailableRepository $stockAvailableRepository
     * @param Configuration $configuration
     */
    public function __construct(
        StockManager $stockManager,
        ProductRepository $productRepository,
        StockAvailableRepository $stockAvailableRepository,
        Configuration $configuration
    ) {
        $this->stockManager = $stockManager;
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->configuration = $configuration;
        $this->advancedStockEnabled = $this->configuration->getBoolean('PS_ADVANCED_STOCK_MANAGEMENT');
    }

    /**
     * @param ProductId $productId
     * @param ProductStockProperties $properties
     */
    public function update(ProductId $productId, ProductStockProperties $properties)
    {
        $product = $this->productRepository->get($productId);
        $stockAvailable = $this->getStockAvailable($product);

        $this->productRepository->partialUpdate(
            $product,
            $this->fillUpdatableProperties($product, $stockAvailable, $properties),
            CannotUpdateProductException::FAILED_UPDATE_STOCK
        );

        $this->updateStockAvailable($stockAvailable, $properties);

        if ($this->advancedStockEnabled && $product->depends_on_stock) {
            StockAvailable::synchronize($product->id);
        }
    }

    /**
     * @param Product $product
     * @param ProductStockProperties $properties
     *
     * @return string[]|array<string, int[]>
     */
    private function fillUpdatableProperties(
        Product $product,
        StockAvailable $stockAvailable,
        ProductStockProperties $properties
    ): array {
        $updatableProperties = [];

        $localizedLaterLabels = $properties->getLocalizedAvailableLaterLabels();
        if (null !== $localizedLaterLabels) {
            $product->available_later = $localizedLaterLabels;
            $updatableProperties['available_later'] = array_keys($localizedLaterLabels);
        }

        $localizedNowLabels = $properties->getLocalizedAvailableNowLabels();
        if (null !== $localizedNowLabels) {
            $product->available_now = $localizedNowLabels;
            $updatableProperties['available_now'] = array_keys($localizedNowLabels);
        }
        if (null !== $properties->getLocation()) {
            $product->location = $properties->getLocation();
            $updatableProperties[] = 'location';
        }
        if (null !== $properties->isLowStockAlertEnabled()) {
            $product->low_stock_alert = $properties->isLowStockAlertEnabled();
            $updatableProperties[] = 'low_stock_alert';
        }
        if (null !== $properties->getLowStockThreshold()) {
            $product->low_stock_threshold = $properties->getLowStockThreshold();
            $updatableProperties[] = 'low_stock_threshold';
        }
        if (null !== $properties->getMinimalQuantity()) {
            $product->minimal_quantity = $properties->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }
        if (null !== $properties->getOutOfStockType()) {
            $product->out_of_stock = $properties->getOutOfStockType()->getValue();
            $updatableProperties[] = 'out_of_stock';
        }
        if (null !== $properties->getPackStockType()) {
            $product->pack_stock_type = $properties->getPackStockType()->getValue();
            $updatableProperties[] = 'pack_stock_type';
        }
        if (null !== $properties->getDeltaQuantity()) {
            $product->quantity = $stockAvailable->quantity + $properties->getDeltaQuantity();
            $updatableProperties[] = 'quantity';
        }
        if (null !== $properties->getAvailableDate()) {
            $product->available_date = $properties->getAvailableDate()->format(DateTime::DEFAULT_DATE_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        return $updatableProperties;
    }

    /**
     * @param StockAvailable $stockAvailable
     * @param ProductStockProperties $properties
     */
    private function updateStockAvailable(StockAvailable $stockAvailable, ProductStockProperties $properties)
    {
        $stockUpdateRequired = false;

        if (null !== $properties->getOutOfStockType()) {
            $stockAvailable->out_of_stock = $properties->getOutOfStockType()->getValue();
            $stockUpdateRequired = true;
        }
        if (null !== $properties->getLocation()) {
            $stockAvailable->location = $properties->getLocation();
            $stockUpdateRequired = true;
        }

        if (null !== $properties->getDeltaQuantity()) {
            $this->updateQuantity($stockAvailable, $properties->getDeltaQuantity());
            $stockUpdateRequired = true;
        }

        if ($stockUpdateRequired) {
            $this->stockAvailableRepository->update($stockAvailable);
        }
    }

    /**
     * @param StockAvailable $stockAvailable
     * @param int $deltaQuantity
     */
    private function updateQuantity(StockAvailable $stockAvailable, int $deltaQuantity): void
    {
        $stockAvailable->quantity += $deltaQuantity;

        if (0 !== $deltaQuantity) {
            if ($deltaQuantity > 0) {
                $movementReasonId = $this->configuration->getInt('PS_STOCK_MVT_INC_EMPLOYEE_EDITION');
            } else {
                $movementReasonId = $this->configuration->getInt('PS_STOCK_MVT_DEC_EMPLOYEE_EDITION');
            }

            $this->stockManager->saveMovement(
                $stockAvailable->id_product,
                $stockAvailable->id_product_attribute,
                $deltaQuantity,
                [
                    'id_stock_mvt_reason' => $movementReasonId,
                ]
            );
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
