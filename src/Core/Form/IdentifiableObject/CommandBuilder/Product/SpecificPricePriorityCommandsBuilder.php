<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\RemoveSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class SpecificPricePriorityCommandsBuilder implements ProductCommandsBuilderInterface
{
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['pricing']['priority_management'])) {
            return [];
        }

        $prioritiesData = $formData['pricing']['priority_management'];

        if (empty($prioritiesData['use_custom_priority'])) {
            return [new RemoveSpecificPricePriorityForProductCommand($productId->getValue())];
        }

        if (!empty($prioritiesData['priorities'])) {
            return [
                new SetSpecificPricePriorityForProductCommand(
                    $productId->getValue(),
                    $prioritiesData['priorities']
                ),
            ];
        }

        return [];
    }
}
