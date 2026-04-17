<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Builder used to build SetAssociatedProductCategoriesCommand or RemoveAllAssociatedProductCategoriesCommand.
 */
class ProductCategoriesCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['description']['categories']['product_categories'])) {
            return [];
        }

        if (empty($formData['description']['categories']['product_categories'])) {
            return [
                new RemoveAllAssociatedProductCategoriesCommand($productId->getValue(), $singleShopConstraint),
            ];
        }

        $productCategories = $formData['description']['categories']['product_categories'];
        $associatedCategoryIds = [];

        foreach ($productCategories as $categoryData) {
            $associatedCategoryIds[] = (int) $categoryData['id'];
        }

        $defaultCategoryId = (int) $formData['description']['categories']['default_category_id'];

        // Default is always amongst the associated
        if (!empty($defaultCategoryId) && !in_array($defaultCategoryId, $associatedCategoryIds)) {
            $associatedCategoryIds[] = $defaultCategoryId;
        }

        // If no associated categories is defined remove them all
        if (empty($associatedCategoryIds)) {
            return [
                new RemoveAllAssociatedProductCategoriesCommand($productId->getValue(), $singleShopConstraint),
            ];
        }

        // If no default is defined use the first one
        if (empty($defaultCategoryId)) {
            $defaultCategoryId = $associatedCategoryIds[0];
        }

        return [
            new SetAssociatedProductCategoriesCommand(
                $productId->getValue(),
                $defaultCategoryId,
                $associatedCategoryIds,
                $singleShopConstraint
            ),
        ];
    }
}
