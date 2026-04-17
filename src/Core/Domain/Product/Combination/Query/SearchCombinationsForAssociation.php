<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SearchCombinationsForAssociation
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
     * @var array<string>
     */
    protected $filters;

    /**
     * @param string $phrase
     * @param int $languageId
     * @param int $shopId
     * @param array $filters
     * @param int|null $limit
     *
     * @throws ProductConstraintException
     * @throws ShopException
     */
    public function __construct(
        string $phrase,
        int $languageId,
        int $shopId,
        array $filters = [],
        ?int $limit = null)
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
        $this->filters = $filters;
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

    /**
     * @return array<string>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
