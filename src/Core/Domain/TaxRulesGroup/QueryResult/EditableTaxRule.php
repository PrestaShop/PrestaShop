<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\BehaviorId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Transfers tax rule data for editing
 */
class EditableTaxRule
{
    /**
     * @var TaxRuleId
     */
    private $taxRuleId;

    /**
     * @var TaxRulesGroupId
     */
    private $taxRulesGroupId;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var BehaviorId
     */
    private $behaviorId;

    /**
     * @var StateId|null
     */
    private $stateId;

    /**
     * @var string|null
     */
    private $zipCodeForm;

    /**
     * @var string|null
     */
    private $zipCodeTo;

    /**
     * @var TaxId|null
     */
    private $taxId;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @param TaxRuleId $taxRuleId
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     * @param BehaviorId $behaviorId
     */
    public function __construct(
        TaxRuleId $taxRuleId,
        TaxRulesGroupId $taxRulesGroupId,
        CountryId $countryId,
        BehaviorId $behaviorId
    ) {
        $this->taxRuleId = $taxRuleId;
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->countryId = $countryId;
        $this->behaviorId = $behaviorId;
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
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return BehaviorId
     */
    public function getBehaviorId(): BehaviorId
    {
        return $this->behaviorId;
    }

    /**
     * @return StateId|null
     */
    public function getStateId(): ?StateId
    {
        return $this->stateId;
    }

    /**
     * @param StateId $stateId
     *
     * @return EditableTaxRule
     */
    public function setStateId(StateId $stateId): EditableTaxRule
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        if (!empty($this->zipCodeForm) && !empty($this->zipCodeTo)) {
            return $this->zipCodeForm . '-' . $this->zipCodeTo;
        }

        if (!empty($this->zipCodeTo)) {
            return $this->zipCodeTo;
        }

        if (!empty($this->zipCodeForm)) {
            return $this->zipCodeForm;
        }

        return '0';
    }

    /**
     * @param string $zipCodeForm
     *
     * @return EditableTaxRule
     */
    public function setZipCodeForm(string $zipCodeForm): EditableTaxRule
    {
        $this->zipCodeForm = $zipCodeForm;

        return $this;
    }

    /**
     * @param string $zipCodeTo
     *
     * @return EditableTaxRule
     */
    public function setZipCodeTo(string $zipCodeTo): EditableTaxRule
    {
        $this->zipCodeTo = $zipCodeTo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCodeForm(): ?string
    {
        return $this->zipCodeForm;
    }

    /**
     * @return string|null
     */
    public function getZipCodeTo(): ?string
    {
        return $this->zipCodeTo;
    }

    /**
     * @return TaxId|null
     */
    public function getTaxId(): ?TaxId
    {
        return $this->taxId;
    }

    /**
     * @param TaxId $taxId
     *
     * @return EditableTaxRule
     */
    public function setTaxId(TaxId $taxId): EditableTaxRule
    {
        $this->taxId = $taxId;

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
     * @return EditableTaxRule
     */
    public function setDescription(string $description): EditableTaxRule
    {
        $this->description = $description;

        return $this;
    }
}
