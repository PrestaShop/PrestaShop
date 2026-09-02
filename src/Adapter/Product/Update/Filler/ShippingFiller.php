<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

/**
 * Fills product properties which are related to product shipping
 */
class ShippingFiller implements ProductFillerInterface
{
    use LocalizedObjectModelTrait;

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
            $this->fillLocalizedValues($product, 'delivery_in_stock', $command->getLocalizedDeliveryTimeInStockNotes(), $updatableProperties);
        }

        if (null !== $command->getLocalizedDeliveryTimeOutOfStockNotes()) {
            $this->fillLocalizedValues($product, 'delivery_out_stock', $command->getLocalizedDeliveryTimeOutOfStockNotes(), $updatableProperties);
        }

        return $updatableProperties;
    }
}
