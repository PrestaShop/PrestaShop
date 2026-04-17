<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\RemoveAllProductsFromPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\SetPackProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class PackedProductsCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $initialType = $formData['header']['initial_type'] ?? null;
        if ($initialType !== ProductType::TYPE_PACK || !isset($formData['stock']['packed_products'])) {
            return [];
        }

        $packedProducts = $formData['stock']['packed_products'];
        if (empty($packedProducts)) {
            return [new RemoveAllProductsFromPackCommand($productId->getValue())];
        }
        $command = new SetPackProductsCommand(
            $productId->getValue(),
            $packedProducts
        );

        return [$command];
    }
}
