<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Retrieves product combinations
 */
class GetEditableCombinationsList
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var array<string, mixed>
     */
    private $filters;

    /**
     * @var string|null
     */
    private $orderBy;

    /**
     * @var string|null
     */
    private $orderWay;

    /**
     * @param int $productId
     * @param int $languageId
     * @param ShopConstraint $shopConstraint
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $orderBy
     * @param string|null $orderWay
     * @param array<string, mixed> $filters
     */
    public function __construct(
        int $productId,
        int $languageId,
        ShopConstraint $shopConstraint,
        ?int $limit = null,
        ?int $offset = null,
        ?string $orderBy = null,
        ?string $orderWay = null,
        array $filters = []
    ) {
        $this->productId = new ProductId($productId);
        $this->languageId = new LanguageId($languageId);
        $this->shopConstraint = $shopConstraint;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->filters = $filters;
        $this->orderBy = $orderBy;
        $this->orderWay = $orderWay;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * @return string|null
     */
    public function getOrderWay(): ?string
    {
        return $this->orderWay;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
