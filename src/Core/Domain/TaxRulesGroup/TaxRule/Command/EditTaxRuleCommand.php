<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;

/**
 * Command responsible for editing a tax rule
 */
class EditTaxRuleCommand
{
    /**
     * @var TaxRuleId
     */
    private TaxRuleId $taxRuleId;

    /**
     * @var int|null
     */
    private ?int $countryId = null;

    /**
     * @var int|null
     */
    private ?int $stateId = null;

    /**
     * @var int|null
     */
    private ?int $taxId = null;

    /**
     * @var int|null
     */
    private ?int $behavior = null;

    /**
     * @var string|null
     */
    private ?string $zipCode = null;

    /**
     * @var string|null
     */
    private ?string $description = null;

    /**
     * @param int $taxRuleId
     */
    public function __construct(int $taxRuleId)
    {
        $this->taxRuleId = new TaxRuleId($taxRuleId);
    }

    /**
     * @return TaxRuleId
     */
    public function getTaxRuleId(): TaxRuleId
    {
        return $this->taxRuleId;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return self
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    /**
     * @param int $stateId
     *
     * @return self
     */
    public function setStateId(int $stateId): self
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTaxId(): ?int
    {
        return $this->taxId;
    }

    /**
     * @param int $taxId
     *
     * @return self
     */
    public function setTaxId(int $taxId): self
    {
        $this->taxId = $taxId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBehavior(): ?int
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
     * @return string|null
     */
    public function getZipCode(): ?string
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
     * @return string|null
     */
    public function getDescription(): ?string
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
