<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Retrieves product specific prices
 */
class GetSpecificPriceList
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
     * @param int $productId
     * @param int $languageId
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, mixed> $filters
     */
    public function __construct(
        int $productId,
        int $languageId,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = []
    ) {
        $this->productId = new ProductId($productId);
        $this->languageId = new LanguageId($languageId);
        $this->limit = $limit;
        $this->offset = $offset;
        $this->filters = $filters;
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
