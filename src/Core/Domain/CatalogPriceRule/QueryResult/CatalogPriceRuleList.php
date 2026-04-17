<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult;

/**
 * Transfer CatalogPriceRule list data
 */
class CatalogPriceRuleList
{
    /**
     * @var CatalogPriceRuleForListing[]
     */
    private $catalogPriceRules;

    /**
     * @var int
     */
    private $totalCount;

    /**
     * @param CatalogPriceRuleForListing[] $catalogPriceRules
     * @param int $totalCount;
     */
    public function __construct(
        array $catalogPriceRules,
        int $totalCount
    ) {
        $this->catalogPriceRules = $catalogPriceRules;
        $this->totalCount = $totalCount;
    }

    /**
     * @return CatalogPriceRuleForListing[]
     */
    public function getCatalogPriceRules(): array
    {
        return $this->catalogPriceRules;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
