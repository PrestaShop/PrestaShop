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

        $this->productStockUpdater->update($product, $stockAvailable, $this->formatCommandToArray($command), $command->addMovement());
    }

    /**
     * @param UpdateProductStockCommand $command
     *
     * @return array
     */
    private function formatCommandToArray(UpdateProductStockCommand $command): array
    {
        $formattedCommand = [];

        if (null !== $command->getAvailableDate()) {
            $formattedCommand['available_date'] = $command->getAvailableDate()->format('Y-m-d');
        }
        if (null !== $command->getLocalizedAvailableLaterLabels()) {
            $formattedCommand['available_later'] = $command->getLocalizedAvailableLaterLabels();
        }
        if (null !== $command->getLocalizedAvailableNowLabels()) {
            $formattedCommand['available_now'] = $command->getLocalizedAvailableNowLabels();
        }
        if (null !== $command->getLocation()) {
            $formattedCommand['location'] = $command->getLocation();
        }
        if (null !== $command->getLowStockAlert()) {
            $formattedCommand['low_stock_alert'] = $command->getLowStockAlert();
        }
        if (null !== $command->getLowStockThreshold()) {
            $formattedCommand['low_stock_threshold'] = $command->getLowStockThreshold();
        }
        if (null !== $command->getMinimalQuantity()) {
            $formattedCommand['minimal_quantity'] = $command->getMinimalQuantity();
        }
        if (null !== $command->getOutOfStockType()) {
            $formattedCommand['out_of_stock'] = OutOfStockTypeConverter::convertToLegacy($command->getOutOfStockType()->getValue());
        }
        if (null !== $command->getPackStockType()) {
            $formattedCommand['pack_stock_type'] = PackStockTypeConverter::convertToLegacy($command->getPackStockType()->getValue());
        }
        if (null !== $command->getQuantity()) {
            $formattedCommand['quantity'] = $command->getQuantity();
        }
        if (null !== $command->dependsOnStock()) {
            $formattedCommand['depends_on_stock'] = $command->dependsOnStock();
        }
        if (null !== $command->useAdvancedStockManagement()) {
            $formattedCommand['advanced_stock_management'] = $command->useAdvancedStockManagement();
        }

        return $formattedCommand;
    }
}
