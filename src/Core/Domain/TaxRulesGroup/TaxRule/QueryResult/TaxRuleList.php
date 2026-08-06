<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult;

/**
 * Transfers paginated list of tax rules
 */
class TaxRuleList
{
    /**
     * @param TaxRuleForList[] $taxRules
     * @param int $totalCount
     */
    public function __construct(
        private readonly array $taxRules,
        private readonly int $totalCount,
    ) {
    }

    /**
     * @return TaxRuleForList[]
     */
    public function getTaxRules(): array
    {
        return $this->taxRules;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
