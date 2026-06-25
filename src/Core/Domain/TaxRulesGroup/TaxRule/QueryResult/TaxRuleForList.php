<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult;

/**
 * Transfers tax rule data for list display
 */
class TaxRuleForList
{
    public function __construct(
        private readonly int $taxRuleId,
        private readonly string $countryName,
        private readonly string $stateName,
        private readonly string $zipcode,
        private readonly int $behavior,
        private readonly string $taxName,
        private readonly string $taxRate,
        private readonly string $description,
    ) {
    }

    public function getTaxRuleId(): int
    {
        return $this->taxRuleId;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }

    public function getStateName(): string
    {
        return $this->stateName;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getBehavior(): int
    {
        return $this->behavior;
    }

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getTaxRate(): string
    {
        return $this->taxRate;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
