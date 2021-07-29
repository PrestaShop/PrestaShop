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

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Builds commands from product stock form type
 */
final class StockCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['stock'])) {
            return [];
        }

        $quantityData = $formData['stock'];
        $command = new UpdateProductStockInformationCommand($productId->getValue());

        if (isset($quantityData['quantities']['quantity'])) {
            $command->setQuantity((int) $quantityData['quantities']['quantity']);
        }
        if (isset($quantityData['quantities']['minimal_quantity'])) {
            $command->setMinimalQuantity((int) $quantityData['quantities']['minimal_quantity']);
        }
        if (isset($quantityData['options']['stock_location'])) {
            $command->setLocation($quantityData['options']['stock_location']);
        }
        if (isset($quantityData['options']['low_stock_threshold'])) {
            $command->setLowStockThreshold((int) $quantityData['options']['low_stock_threshold']);
        }
        if (isset($quantityData['options']['low_stock_alert'])) {
            $command->setLowStockAlert((bool) $quantityData['options']['low_stock_alert']);
        }
        if (isset($quantityData['pack_stock_type'])) {
            $command->setPackStockType((int) $quantityData['pack_stock_type']);
        }
        if (isset($quantityData['availability']['out_of_stock_type'])) {
            $command->setOutOfStockType((int) $quantityData['availability']['out_of_stock_type']);
        }
        if (isset($quantityData['availability']['available_now_label'])) {
            $command->setLocalizedAvailableNowLabels($quantityData['availability']['available_now_label']);
        }
        if (isset($quantityData['availability']['available_later_label'])) {
            $command->setLocalizedAvailableLaterLabels($quantityData['availability']['available_later_label']);
        }
        if (isset($quantityData['availability']['available_date'])) {
            $command->setAvailableDate(new DateTime($quantityData['availability']['available_date']));
        }

        return [$command];
    }
}
