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

final class ShippingCommandBuilder implements ProductCommandBuilderInterface
{
    /**
     * @param ProductId $productId
     * @param array $formData
     *
     * @return UpdateProductShippingCommand|null
     */
    public function buildCommand(ProductId $productId, array $formData)
    {
        if (!isset($formData['shipping'])) {
            return null;
        }

        $shippingData = $formData['shipping'];
        $command = new UpdateProductShippingCommand($productId->getValue());

        if (isset($shippingData['width'])) {
            $command->setWidth($shippingData['width']);
        }

        if (isset($shippingData['height'])) {
            $command->setHeight($shippingData['height']);
        }

        if (isset($shippingData['depth'])) {
            $command->setDepth($shippingData['depth']);
        }

        if (isset($shippingData['weight'])) {
            $command->setWeight($shippingData['weight']);
        }

        if (isset($shippingData['delivery_time_note_type'])) {
            $command->setDeliveryTimeNoteType((int) $shippingData['delivery_time_note_type']);
        }

        if (isset($shippingData['delivery_time_in_stock_note'])) {
            $command->setLocalizedDeliveryTimeInStockNotes($shippingData['delivery_time_in_stock_note']);
        }

        if (isset($shippingData['delivery_time_out_stock_note'])) {
            $command->setLocalizedDeliveryTimeOutOfStockNotes($shippingData['delivery_time_out_stock_note']);
        }

        if (isset($shippingData['additional_shipping_cost'])) {
            $command->setAdditionalShippingCost((string) $shippingData['additional_shipping_cost']);
        }

        if (isset($shippingData['carriers'])) {
            $command->setCarrierReferences($shippingData['carriers']);
        }

        return $command;
    }
}
