<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

/**
 * Transfers a paginated list of the products associated with a category
 */
class CategoryAssociatedProductList
{
    /**
     * @param CategoryAssociatedProduct[] $products
     * @param int $totalCount number of products matching the search, on all pages
     */
    public function __construct(
        private readonly array $products,
        private readonly int $totalCount,
    ) {
    }

    /**
     * @return CategoryAssociatedProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
