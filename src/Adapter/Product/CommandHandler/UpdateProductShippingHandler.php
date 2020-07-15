<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductShippingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Product;

/**
 * Handles @var UpdateProductShippingCommand using legacy object model
 */
final class UpdateProductShippingHandler extends AbstractProductHandler implements UpdateProductShippingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductShippingCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());
        $this->fillUpdatableFieldsWithCommandData($product, $command);

        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_SHIPPING_OPTIONS);
    }

    /**
     * @param Product $product
     * @param UpdateProductShippingCommand $command
     */
    private function fillUpdatableFieldsWithCommandData(Product $product, UpdateProductShippingCommand $command): void
    {
        if (null !== $command->getWidth()) {
            $product->width = (string) $command->getWidth();
            $this->validateField($product, 'width', ProductConstraintException::INVALID_WIDTH);
            $this->fieldsToUpdate['width'] = true;
        }

        if (null !== $command->getHeight()) {
            $product->height = (string) $command->getHeight();
            $this->validateField($product, 'height', ProductConstraintException::INVALID_HEIGHT);
            $this->fieldsToUpdate['height'] = true;
        }

        if (null !== $command->getDepth()) {
            $product->depth = (string) $command->getDepth();
            $this->validateField($product, 'depth', ProductConstraintException::INVALID_DEPTH);
            $this->fieldsToUpdate['depth'] = true;
        }

        if (null !== $command->getWeight()) {
            $product->weight = (string) $command->getWeight();
            $this->validateField($product, 'weight', ProductConstraintException::INVALID_WEIGHT);
            $this->fieldsToUpdate['weight'] = true;
        }

        if (null !== $command->getAdditionalShippingCost()) {
            $product->additional_shipping_cost = (string) $command->getAdditionalShippingCost();
            $this->validateField($product, 'additional_shipping_cost', ProductConstraintException::INVALID_ADDITIONAL_SHIPPING_COST);
            $this->fieldsToUpdate['additional_shipping_cost'] = true;
        }

        if (null !== $command->getCarrierReferences()) {
            $product->setCarriers($command->getCarrierReferences());
        }

        if (null !== $command->getDeliveryTimeNotesType()) {
            $product->additional_delivery_times = $command->getDeliveryTimeNotesType()->getValue();
            $this->fieldsToUpdate['additional_delivery_times'] = true;
        }

        if (null !== $command->getLocalizedDeliveryTimeInStockNotes()) {
            $product->delivery_in_stock = $command->getLocalizedDeliveryTimeInStockNotes();
            $this->fieldsToUpdate['delivery_in_stock'] = true;
        }

        if (null !== $command->getLocalizedDeliveryTimeOutOfStockNotes()) {
            $product->delivery_out_stock = $command->getLocalizedDeliveryTimeOutOfStockNotes();
            $this->fieldsToUpdate['delivery_out_stock'] = true;
        }
    }
}
