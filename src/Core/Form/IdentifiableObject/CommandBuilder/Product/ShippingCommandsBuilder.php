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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

final class ShippingCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['shipping'])) {
            return [];
        }

        $shippingData = $formData['shipping'];
        $dimensionsData = $shippingData['dimensions'] ?? [];
        $command = new UpdateProductShippingCommand($productId->getValue());

        if (isset($dimensionsData['width'])) {
            $command->setWidth((string) $dimensionsData['width']);
        }

        if (isset($dimensionsData['height'])) {
            $command->setHeight((string) $dimensionsData['height']);
        }

        if (isset($dimensionsData['depth'])) {
            $command->setDepth((string) $dimensionsData['depth']);
        }

        if (isset($dimensionsData['weight'])) {
            $command->setWeight((string) $dimensionsData['weight']);
        }

        if (isset($shippingData['delivery_time_note_type'])) {
            $command->setDeliveryTimeNoteType((int) $shippingData['delivery_time_note_type']);
        }

        if (isset($shippingData['delivery_time_notes']['in_stock'])) {
            $command->setLocalizedDeliveryTimeInStockNotes($shippingData['delivery_time_notes']['in_stock']);
        }

        if (isset($shippingData['delivery_time_notes']['out_of_stock'])) {
            $command->setLocalizedDeliveryTimeOutOfStockNotes($shippingData['delivery_time_notes']['out_of_stock']);
        }

        if (isset($shippingData['additional_shipping_cost'])) {
            $command->setAdditionalShippingCost((string) $shippingData['additional_shipping_cost']);
        }

        if (isset($shippingData['carriers'])) {
            $command->setCarrierReferences($shippingData['carriers']);
        }

        return [$command];
    }
}
