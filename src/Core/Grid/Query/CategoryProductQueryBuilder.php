<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

/**
 * Builds the queries of the grid listing the products associated directly with a category (not with its
 * subcategories)
 */
final class CategoryProductQueryBuilder extends AbstractAssociatedProductQueryBuilder
{
    protected function getAssociationTable(): string
    {
        return 'category_product';
    }

    protected function getAssociatedEntityColumn(): string
    {
        return 'id_category';
    }
}
