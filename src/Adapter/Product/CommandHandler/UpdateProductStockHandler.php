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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Adapter\Product\Converter\OutOfStockTypeConverter;
use PrestaShop\PrestaShop\Adapter\Product\Converter\PackStockTypeConverter;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductStockHandlerInterface;
use Product;

/**
 * @internal
 */
class UpdateProductStockHandler extends AbstractProductHandler implements UpdateProductStockHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var ProductStockUpdater
     */
    private $productStockUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param StockAvailableRepository $stockAvailableRepository
     * @param ProductStockUpdater $productStockUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        StockAvailableRepository $stockAvailableRepository,
        ProductStockUpdater $productStockUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->productStockUpdater = $productStockUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductStockCommand $command): void
    {
        $product = $this->productRepository->get($command->getProductId());
        $stockAvailable = $this->stockAvailableRepository->getOrCreate($command->getProductId());

        $propertiesToUpdate = $this->fillUpdatableProperties($product, $command);
        $this->productStockUpdater->update($product, $stockAvailable, $propertiesToUpdate, $command->addMovement());
    }

    /**
     * @param Product $product
     * @param UpdateProductStockCommand $command
     *
     * @return array
     */
    private function fillUpdatableProperties(
        Product $product,
        UpdateProductStockCommand $command
    ): array {
        $updatableProperties = [];

        if (null !== $command->getLocation()) {
            $product->location = $command->getLocation();
            $updatableProperties[] = 'location';
        }
        if (null !== $command->getLowStockAlert()) {
            $product->low_stock_alert = $command->getLowStockAlert();
            $updatableProperties[] = 'low_stock_alert';
        }
        if (null !== $command->getLowStockThreshold()) {
            $product->low_stock_threshold = $command->getLowStockThreshold();
            $updatableProperties[] = 'low_stock_threshold';
        }
        if (null !== $command->getMinimalQuantity()) {
            $product->minimal_quantity = $command->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }
        if (null !== $command->getOutOfStockType()) {
            $product->out_of_stock = OutOfStockTypeConverter::convertToLegacy($command->getOutOfStockType()->getValue());
            $updatableProperties[] = 'out_of_stock';
        }
        if (null !== $command->getPackStockType()) {
            $product->pack_stock_type = PackStockTypeConverter::convertToLegacy($command->getPackStockType()->getValue());
            $updatableProperties[] = 'pack_stock_type';
        }
        if (null !== $command->getQuantity()) {
            $product->quantity = $command->getQuantity();
            $updatableProperties[] = 'quantity';
        }
        if (null !== $command->dependsOnStock()) {
            $product->depends_on_stock = $command->dependsOnStock();
            $updatableProperties[] = 'depends_on_stock';
        }
        if (null !== $command->getAvailableDate()) {
            $product->available_date = $command->getAvailableDate()->format('Y-m-d');
            $updatableProperties[] = 'available_date';
        }

        $localizedLaterLabels = $command->getLocalizedAvailableLaterLabels();
        if (null !== $localizedLaterLabels) {
            $product->available_later = $localizedLaterLabels;
            $updatableProperties['available_later'] = array_keys($localizedLaterLabels);
        }
        $localizedNowLabels = $command->getLocalizedAvailableNowLabels();
        if (null !== $localizedNowLabels) {
            $product->available_now = $localizedNowLabels;
            $updatableProperties['available_now'] = array_keys($localizedNowLabels);
        }

        if (null !== $command->useAdvancedStockManagement()) {
            $product->advanced_stock_management = $command->useAdvancedStockManagement();
            $updatableProperties[] = 'advanced_stock_management';
            // When advanced stock is disabled depend_on_stock must be disabled automatically
            if (!$product->advanced_stock_management && !in_array('depends_on_stock', $updatableProperties)) {
                $product->depends_on_stock = false;
                $updatableProperties[] = 'depends_on_stock';
            }
        }

        return $updatableProperties;
    }
}
