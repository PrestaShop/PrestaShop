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

class SearchProductCombinations
{
    public const DEFAULT_RESULTS_LIMIT = 20;

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
     * @var string
     */
    private $searchPhrase;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param int $productId
     * @param int $languageId
     * @param ShopConstraint $shopConstraint
     * @param string $searchPhrase
     */
    public function __construct(
        int $productId,
        int $languageId,
        ShopConstraint $shopConstraint,
        string $searchPhrase,
        int $limit = self::DEFAULT_RESULTS_LIMIT
    ) {
        $this->productId = new ProductId($productId);
        $this->languageId = new LanguageId($languageId);
        $this->shopConstraint = $shopConstraint;
        $this->searchPhrase = $searchPhrase;
        $this->limit = $limit;
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
     * @return string
     */
    public function getSearchPhrase(): string
    {
        return $this->searchPhrase;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
