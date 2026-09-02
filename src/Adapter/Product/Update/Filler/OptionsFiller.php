<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

/**
 * Fills product properties which can be considered as product options
 */
class OptionsFiller implements ProductFillerInterface
{
    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getVisibility()) {
            $product->visibility = $command->getVisibility()->getValue();
            $updatableProperties[] = 'visibility';
        }

        if (null !== $command->isAvailableForOrder()) {
            $product->available_for_order = $command->isAvailableForOrder();
            $updatableProperties[] = 'available_for_order';
        }
        $availableForOrder = $product->available_for_order;

        if (null !== $command->showPrice() && !$availableForOrder) {
            $product->show_price = $command->showPrice();
            $updatableProperties[] = 'show_price';
        } elseif ($availableForOrder && !$product->show_price) {
            $product->show_price = true;
            $updatableProperties[] = 'show_price';
        }

        if (null !== $command->isOnlineOnly()) {
            $product->online_only = $command->isOnlineOnly();
            $updatableProperties[] = 'online_only';
        }

        if (null !== $command->getCondition()) {
            $product->condition = $command->getCondition()->getValue();
            $updatableProperties[] = 'condition';
        }

        if (null !== $command->showCondition()) {
            $product->show_condition = $command->showCondition();
            $updatableProperties[] = 'show_condition';
        }

        $manufacturerId = $command->getManufacturerId();
        if (null !== $manufacturerId) {
            $product->id_manufacturer = $manufacturerId->getValue();
            $updatableProperties[] = 'id_manufacturer';
        }

        return $updatableProperties;
    }
}
