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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

final class OptionsCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['options']) && !isset($formData['manufacturer']['manufacturer_id'])) {
            return [];
        }

        $options = $formData['options'] ?? [];
        $manufacturer = $formData['manufacturer'] ?? [];
        $command = new UpdateProductOptionsCommand($productId->getValue());

        if (isset($options['active'])) {
            $command->setActive((bool) $options['active']);
        }
        if (isset($options['visibility'])) {
            $command->setVisibility($options['visibility']);
        }
        if (isset($options['available_for_order'])) {
            $command->setAvailableForOrder((bool) $options['available_for_order']);
        }
        if (isset($options['show_price'])) {
            $command->setShowPrice((bool) $options['show_price']);
        }
        if (isset($options['online_only'])) {
            $command->setOnlineOnly((bool) $options['online_only']);
        }
        if (isset($options['show_condition'])) {
            $command->setShowCondition((bool) $options['show_condition']);
        }
        if (isset($options['condition'])) {
            $command->setCondition($options['condition']);
        }

        if (isset($manufacturer['manufacturer_id'])) {
            $command->setManufacturerId((int) $manufacturer['manufacturer_id']);
        }

        return [$command];
    }
}
