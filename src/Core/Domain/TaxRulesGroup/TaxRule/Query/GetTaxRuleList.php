<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Query to get a paginated list of tax rules for a given tax rules group
 */
class GetTaxRuleList
{
    private TaxRulesGroupId $taxRulesGroupId;

    public function __construct(
        int $taxRulesGroupId,
        private readonly int $languageId,
        private readonly ?int $limit = null,
        private readonly ?int $offset = null,
    ) {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
    }

    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
