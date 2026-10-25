<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

/**
 * Defines the grid listing the products associated directly with a category
 */
final class CategoryProductGridDefinitionFactory extends AbstractAssociatedProductGridDefinitionFactory
{
    public const GRID_ID = 'category_product';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getIndexRoute(): string
    {
        return 'admin_categories_edit';
    }

    protected function getEntityIdRouteParam(): string
    {
        return 'categoryId';
    }
}
