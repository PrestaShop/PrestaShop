<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Category\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryAssociatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryAssociatedProductList;

/**
 * Handles @see GetCategoryAssociatedProducts query
 */
#[AsQueryHandler]
final class GetCategoryAssociatedProductsHandler
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(GetCategoryAssociatedProducts $query): CategoryAssociatedProductList
    {
        return $this->categoryRepository->getAssociatedProducts(
            $query->getCategoryId(),
            $query->getLanguageId(),
            $query->getShopConstraint(),
            $query->getLimit(),
            $query->getOffset(),
            $query->getSearchPhrase()
        );
    }
}
