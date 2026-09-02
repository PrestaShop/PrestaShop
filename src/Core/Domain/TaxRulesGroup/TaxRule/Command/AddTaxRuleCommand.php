<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command responsible for adding a tax rule to a tax rules group.
 * When countryId is 0, the handler creates rules for all active countries.
 */
class AddTaxRuleCommand
{
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
    private int $taxId;

    /**
     * @var int[]
     */
    private array $stateIds = [0];

    /**
     * @var int
     */
    private int $behavior = 0;

    /**
     * @var string
     */
    private string $zipCode = '';

    /**
     * @var string
     */
    private string $description = '';

    /**
     * @param int $taxRulesGroupId
     * @param int $countryId 0 means all countries
     * @param int $taxId 0 means no tax
     */
    public function __construct(int $taxRulesGroupId, int $countryId, int $taxId)
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
        $this->countryId = $countryId;
        $this->taxId = $taxId;
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
    public function getTaxId(): int
    {
        return $this->taxId;
    }

    /**
     * @return int[]
     */
    public function getStateIds(): array
    {
        return $this->stateIds;
    }

    /**
     * @param int[] $stateIds
     *
     * @return self
     */
    public function setStateIds(array $stateIds): self
    {
        $this->stateIds = $stateIds;

        return $this;
    }

    /**
     * @return int
     */
    public function getBehavior(): int
    {
        return $this->behavior;
    }

    /**
     * @param int $behavior
     *
     * @return self
     *
     * @throws TaxRuleConstraintException
     */
    public function setBehavior(int $behavior): self
    {
        if ($behavior < 0 || $behavior > 2) {
            throw new TaxRuleConstraintException(
                sprintf('Invalid tax rule behavior "%d". Expected value between 0 and 2.', $behavior),
                TaxRuleConstraintException::INVALID_BEHAVIOR
            );
        }

        $this->behavior = $behavior;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     *
     * @return self
     */
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
