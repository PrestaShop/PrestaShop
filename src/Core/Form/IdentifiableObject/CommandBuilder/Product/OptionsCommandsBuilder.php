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
        if (!isset($formData['options']) &&
            !isset($formData['description']['manufacturer']) &&
            !isset($formData['footer']['active'])) {
            return [];
        }

        $options = $formData['options'] ?? [];
        $manufacturerId = isset($formData['description']['manufacturer']) ? (int) $formData['description']['manufacturer'] : null;
        $command = new UpdateProductOptionsCommand($productId->getValue());

        if (isset($options['visibility']['visibility'])) {
            $command->setVisibility($options['visibility']['visibility']);
        }
        if (isset($options['visibility']['available_for_order'])) {
            $command->setAvailableForOrder((bool) $options['visibility']['available_for_order']);
        }
        if (isset($options['visibility']['show_price'])) {
            $command->setShowPrice((bool) $options['visibility']['show_price']);
        }
        if (isset($options['visibility']['online_only'])) {
            $command->setOnlineOnly((bool) $options['visibility']['online_only']);
        }
        if (isset($options['show_condition'])) {
            $command->setShowCondition((bool) $options['show_condition']);
        }
        if (isset($options['condition'])) {
            $command->setCondition($options['condition']);
        }

        if (null !== $manufacturerId) {
            $command->setManufacturerId($manufacturerId);
        }

        if (isset($formData['footer']['active'])) {
            $command->setActive((bool) $formData['footer']['active']);
        }

        return [$command];
    }
}
