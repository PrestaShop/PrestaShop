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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\Behavior;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Create tax rules with provided data
 */
class AddTaxRulesCommand
{
    /**
     * @var TaxRulesGroupId
     */
    private $taxRulesGroupId;

    /**
     * @var int
     */
    private $behavior;

    /**
     * @var CountryId|null
     */
    private $countryId;

    /**
     * @var StateId[]|null
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
     * @param int $taxRulesGroupId
     * @param int $behavior
     * @param int[] $stateIds
     *
     * @throws TaxRuleConstraintException
     * @throws TaxRulesGroupConstraintException
     * @throws StateConstraintException
     */
    public function __construct(int $taxRulesGroupId, int $behavior, array $stateIds)
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
        $this->behavior = new Behavior($behavior);
        $this->stateIds = $this->setStateIds($stateIds);
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return Behavior
     */
    public function getBehavior(): Behavior
    {
        return $this->behavior;
    }

    /**
     * @return StateId[]|null
     */
    public function getStateIds(): ?array
    {
        return $this->stateIds;
    }

    /**
     * @return CountryId|null
     */
    public function getCountryId(): ?CountryId
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return AddTaxRulesCommand
     *
     * @throws CountryConstraintException
     */
    public function setCountryId(int $countryId): AddTaxRulesCommand
    {
        $this->countryId = new CountryId($countryId);

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
     * @return AddTaxRulesCommand
     */
    public function setZipCode(string $zipCode): AddTaxRulesCommand
    {
        $this->zipCode = $zipCode;

        return $this;
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
     * @return AddTaxRulesCommand
     *
     * @throws TaxConstraintException
     */
    public function setTaxId(int $taxId): AddTaxRulesCommand
    {
        $this->taxId = new TaxId($taxId);

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
     * @return AddTaxRulesCommand
     */
    public function setDescription(string $description): AddTaxRulesCommand
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param int[] $stateIds
     *
     * @return StateId[]
     *
     * @throws StateConstraintException
     */
    private function setStateIds(array $stateIds): array
    {
        $stateIdValueObjects = [];

        foreach ($stateIds as $stateId) {
            $stateIdValueObjects[] = new StateId($stateId);
        }

        return $stateIdValueObjects;
    }
}
