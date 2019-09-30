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

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\Behavior;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRuleId;

/**
 * Update tax rule with provided data
 */
class UpdateTaxRuleCommand
{
    /**
     * @var TaxRuleId
     */
    private $taxRuleId;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var Behavior
     */
    private $behavior;

    /**
     * @var StateId[]
     */
    private $stateIds;

    /**
     * @var string|null
     */
    private $zipCode;

    /**
     * @var TaxId|null
     */
    private $taxId;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @param int $taxRuleId
     * @param int $countryId
     * @param int $behavior
     * @param array $stateIds
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws TaxRuleConstraintException
     */
    public function __construct(
        int $taxRuleId,
        int $countryId,
        int $behavior,
        array $stateIds
    ) {
        $this->taxRuleId = new TaxRuleId($taxRuleId);
        $this->countryId = new CountryId($countryId);
        $this->behavior = new Behavior($behavior);
        $this->setStateIds($stateIds);
    }

    /**
     * @return TaxRuleId
     */
    public function getTaxRuleId(): TaxRuleId
    {
        return $this->taxRuleId;
    }

    /**
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return Behavior
     */
    public function getBehavior(): Behavior
    {
        return $this->behavior;
    }

    /**
     * @return StateId[]
     */
    public function getStateIds(): array
    {
        return $this->stateIds;
    }

    /**
     * @return TaxId|null
     */
    public function getTaxId(): ?TaxId
    {
        return $this->taxId;
    }

    /**
     * @param int $taxId
     *
     * @return UpdateTaxRuleCommand
     *
     * @throws TaxConstraintException
     */
    public function setTaxId(int $taxId): UpdateTaxRuleCommand
    {
        $this->taxId = new TaxId($taxId);

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
     * @return UpdateTaxRuleCommand
     */
    public function setZipCode(string $zipCode): UpdateTaxRuleCommand
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
     * @param string|null $description
     *
     * @return UpdateTaxRuleCommand
     */
    public function setDescription(string $description): UpdateTaxRuleCommand
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param int[] $stateIds
     *
     * @return UpdateTaxRuleCommand
     *
     * @throws StateConstraintException
     */
    private function setStateIds(array $stateIds): UpdateTaxRuleCommand
    {
        foreach ($stateIds as $stateId) {
            $this->stateIds[] = new StateId($stateId);
        }

        return $this;
    }
}
