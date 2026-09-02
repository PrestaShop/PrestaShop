<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SearchProductsForAssociation
{
    /**
     * This is the minimum length of search phrase
     */
    public const SEARCH_PHRASE_MIN_LENGTH = 3;

    /**
     * @var string
     */
    private $phrase;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @param string $phrase
     * @param int $languageId
     * @param int $shopId
     * @param int|null $limit
     */
    public function __construct(string $phrase, int $languageId, int $shopId, ?int $limit = null)
    {
        if (null !== $limit && $limit <= 0) {
            throw new ProductConstraintException('Search limit must be a positive integer or null', ProductConstraintException::INVALID_SEARCH_LIMIT);
        }
        if (mb_strlen($phrase) < static::SEARCH_PHRASE_MIN_LENGTH) {
            throw new ProductConstraintException(sprintf(
                'Search phase must have a minimum length of %d characters.',
                static::SEARCH_PHRASE_MIN_LENGTH
            ), ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH);
        }

        $this->phrase = $phrase;
        $this->limit = $limit;
        $this->shopId = new ShopId($shopId);
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * @return ShopId
     */
    public function getShopId(): ShopId
    {
        return $this->shopId;
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
}
