<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\Query;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Gets a paginated list of the products associated directly with a category, the ones counted in the categories list
 * (products of its subcategories are not included)
 */
class GetCategoryAssociatedProducts
{
    private CategoryId $categoryId;

    private LanguageId $languageId;

    /**
     * @param int $categoryId
     * @param int $languageId language of the product names
     * @param ShopConstraint $shopConstraint shop whose name and status are returned, each product's default shop
     *                                       when the constraint is not a single shop
     * @param int|null $limit
     * @param int|null $offset
     * @param string $searchPhrase keeps only the products with this ID, or with a part of their reference or name
     */
    public function __construct(
        int $categoryId,
        int $languageId,
        private readonly ShopConstraint $shopConstraint,
        private readonly ?int $limit = null,
        private readonly ?int $offset = null,
        private readonly string $searchPhrase = '',
    ) {
        $this->categoryId = new CategoryId($categoryId);
        $this->languageId = new LanguageId($languageId);
    }

    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getSearchPhrase(): string
    {
        return $this->searchPhrase;
    }
}
