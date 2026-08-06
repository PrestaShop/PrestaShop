<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Transfers tax rule data for editing
 */
class EditableTaxRule
{
    /**
     * @var TaxRuleId
     */
    private TaxRuleId $taxRuleId;

    /**
     * @var TaxRulesGroupId
     */
    private TaxRulesGroupId $taxRulesGroupId;

    /**
     * @var int
     */
    private int $countryId;

    /**
     * @var int
     */
    private int $stateId;

    /**
     * @var string
     */
    private string $zipcodeFrom;

    /**
     * @var string
     */
    private string $zipcodeTo;

    /**
     * @var int
     */
    private int $taxId;

    /**
     * @var int
     */
    private int $behavior;

    /**
     * @var string
     */
    private string $description;

    public function __construct(
        TaxRuleId $taxRuleId,
        TaxRulesGroupId $taxRulesGroupId,
        int $countryId,
        int $stateId,
        string $zipcodeFrom,
        string $zipcodeTo,
        int $taxId,
        int $behavior,
        string $description
    ) {
        $this->taxRuleId = $taxRuleId;
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->countryId = $countryId;
        $this->stateId = $stateId;
        $this->zipcodeFrom = $zipcodeFrom;
        $this->zipcodeTo = $zipcodeTo;
        $this->taxId = $taxId;
        $this->behavior = $behavior;
        $this->description = $description;
    }

    /**
     * @return TaxRuleId
     */
    public function getTaxRuleId(): TaxRuleId
    {
        return $this->taxRuleId;
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @return int
     */
    public function getStateId(): int
    {
        return $this->stateId;
    }

    /**
     * @return string
     */
    public function getZipcodeFrom(): string
    {
        return $this->zipcodeFrom;
    }

    /**
     * @return string
     */
    public function getZipcodeTo(): string
    {
        return $this->zipcodeTo;
    }

    /**
     * @return int
     */
    public function getTaxId(): int
    {
        return $this->taxId;
    }

    /**
     * @return int
     */
    public function getBehavior(): int
    {
        return $this->behavior;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
