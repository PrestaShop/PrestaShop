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
use Product;

/**
 * Fills product properties which are related to product shipping
 */
class ShippingFiller implements ProductFillerInterface
{
    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getWidth()) {
            $product->width = (string) $command->getWidth()->getDecimalValue();
            $updatableProperties[] = 'width';
        }

        if (null !== $command->getHeight()) {
            $product->height = (string) $command->getHeight()->getDecimalValue();
            $updatableProperties[] = 'height';
        }

        if (null !== $command->getDepth()) {
            $product->depth = (string) $command->getDepth()->getDecimalValue();
            $updatableProperties[] = 'depth';
        }

        if (null !== $command->getWeight()) {
            $product->weight = (string) $command->getWeight()->getDecimalValue();
            $updatableProperties[] = 'weight';
        }

        if (null !== $command->getAdditionalShippingCost()) {
            $product->additional_shipping_cost = (float) (string) $command->getAdditionalShippingCost();
            $updatableProperties[] = 'additional_shipping_cost';
        }

        if (null !== $command->getDeliveryTimeNoteType()) {
            $product->additional_delivery_times = $command->getDeliveryTimeNoteType()->getValue();
            $updatableProperties[] = 'additional_delivery_times';
        }

        if (null !== $command->getLocalizedDeliveryTimeInStockNotes()) {
            $product->delivery_in_stock = $command->getLocalizedDeliveryTimeInStockNotes();
            $updatableProperties['delivery_in_stock'] = array_keys($command->getLocalizedDeliveryTimeInStockNotes());
        }

        if (null !== $command->getLocalizedDeliveryTimeOutOfStockNotes()) {
            $product->delivery_out_stock = $command->getLocalizedDeliveryTimeOutOfStockNotes();
            $updatableProperties['delivery_out_stock'] = array_keys($command->getLocalizedDeliveryTimeOutOfStockNotes());
        }

        return $updatableProperties;
    }
}
