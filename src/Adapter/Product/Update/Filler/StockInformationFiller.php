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

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Product;

/**
 * Fills product properties related to stock. But just the ones in Product entity and not the ones in StockAvailable.
 * For properties like quantity, out_of_stock and location @see UpdateProductStockAvailableCommand
 */
class StockInformationFiller implements ProductFillerInterface
{
    /**
     * @param Product $product
     * @param UpdateProductCommand $command
     *
     * @return string[]|array<string, int[]>
     */
    public function fillUpdatableProperties(
        Product $product,
        UpdateProductCommand $command
    ): array {
        $updatableProperties = [];

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

        $lowStockThreshold = $command->getLowStockThreshold();
        if (null !== $lowStockThreshold) {
            $product->low_stock_threshold = $lowStockThreshold->getValue();
            $product->low_stock_alert = $lowStockThreshold->isEnabled();
            $updatableProperties[] = 'low_stock_threshold';
            $updatableProperties[] = 'low_stock_alert';
        }

        if (null !== $command->getMinimalQuantity()) {
            $product->minimal_quantity = $command->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }
        if (null !== $command->getPackStockType()) {
            $product->pack_stock_type = $command->getPackStockType()->getValue();
            $updatableProperties[] = 'pack_stock_type';
        }
        if (null !== $command->getAvailableDate()) {
            $product->available_date = $command->getAvailableDate()->format(DateTime::DEFAULT_DATE_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        return $updatableProperties;
    }
}
