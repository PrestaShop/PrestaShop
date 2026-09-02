<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class RelatedProductsCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['description']['related_products'])) {
            return [];
        }

        $relatedProducts = $formData['description']['related_products'];
        if (empty($relatedProducts)) {
            return [new RemoveAllRelatedProductsCommand($productId->getValue())];
        }

        $relatedProductIds = [];
        foreach ($relatedProducts as $relatedProduct) {
            $relatedProductId = (int) $relatedProduct['id'];
            if (!in_array($relatedProductId, $relatedProductIds)) {
                $relatedProductIds[] = $relatedProductId;
            }
        }

        $command = new SetRelatedProductsCommand(
            $productId->getValue(),
            $relatedProductIds
        );

        return [$command];
    }
}
