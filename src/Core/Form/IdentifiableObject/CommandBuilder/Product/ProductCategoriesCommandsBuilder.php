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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Builder used to build SetAssociatedProductCategoriesCommand or RemoveAllAssociatedProductCategoriesCommand.
 */
class ProductCategoriesCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['categories']['product_categories'])) {
            return [];
        }

        if (empty($formData['categories']['product_categories'])) {
            return [
                new RemoveAllAssociatedProductCategoriesCommand($productId->getValue()),
            ];
        }

        $productCategories = $formData['categories']['product_categories'];
        $associatedCategoryIds = [];
        $defaultCategoryId = 0;
        foreach ($productCategories as $categoryId => $categoryData) {
            if ((bool) $categoryData['is_associated']) {
                $associatedCategoryIds[] = (int) $categoryId;
            }
            if ((bool) $categoryData['is_default']) {
                $defaultCategoryId = (int) $categoryId;
            }
        }

        // Default is always amongst the associated
        if (!empty($defaultCategoryId) && !in_array($defaultCategoryId, $associatedCategoryIds)) {
            $associatedCategoryIds[] = $defaultCategoryId;
        }

        // If no associated categories is defined remove them all
        if (empty($associatedCategoryIds)) {
            return [
                new RemoveAllAssociatedProductCategoriesCommand($productId->getValue()),
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
                $associatedCategoryIds
            ),
        ];
    }
}
