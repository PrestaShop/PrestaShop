<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tax;

use Address;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Division;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use TaxManagerFactory;

class TaxComputer
{
    /**
     * The conversion between tax rate as percent to tax ratio as float value can make us lose some precision,
     * so we increase the default precision (6) to avoid losing two digits by diving by 100 (two decimal factors).
     */
    protected const DIVISION_PRECISION = Division::DEFAULT_PRECISION + 2;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param int $langId
     */
    public function __construct(
        int $langId
    ) {
        $this->langId = $langId;
    }

    /**
     * @param DecimalNumber $priceTaxExcluded
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    public function computePriceWithTaxes(
        DecimalNumber $priceTaxExcluded,
        TaxRulesGroupId $taxRulesGroupId,
        CountryId $countryId
    ): DecimalNumber {
        $taxRatio = $this->getTaxRatio($taxRulesGroupId, $countryId);

        return $priceTaxExcluded->times($taxRatio);
    }

    /**
     * @param DecimalNumber $priceTaxIncluded
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    public function computePriceWithoutTaxes(
        DecimalNumber $priceTaxIncluded,
        TaxRulesGroupId $taxRulesGroupId,
        CountryId $countryId
    ): DecimalNumber {
        $taxRatio = $this->getTaxRatio($taxRulesGroupId, $countryId);

        return $priceTaxIncluded->dividedBy($taxRatio);
    }

    /**
     * Returns the tax rate for a group and a specific country. The value is the decimal rate (usually a float between 0 and 1)
     *
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    public function getTaxRate(TaxRulesGroupId $taxRulesGroupId, CountryId $countryId): DecimalNumber
    {
        $country = new \Country($countryId->getValue());

        $address = new Address();
        $address->id_country = $countryId->getValue();
        if ($country->contains_states) {
            $taxRules = \TaxRule::getTaxRulesByGroupId($this->langId, $taxRulesGroupId->getValue());
            $firstTaxRule = reset($taxRules);
            $address->id_state = $firstTaxRule['id_state'];
        }

        $taxCalculator = TaxManagerFactory::getManager($address, $taxRulesGroupId->getValue())->getTaxCalculator();

        return new DecimalNumber((string) $taxCalculator->getTotalRate());
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    protected function getTaxRatio(TaxRulesGroupId $taxRulesGroupId, CountryId $countryId): DecimalNumber
    {
        $countryTaxRate = $this->getTaxRate($taxRulesGroupId, $countryId);

        return $countryTaxRate->dividedBy(new DecimalNumber('100'), self::DIVISION_PRECISION)->plus(new DecimalNumber('1'));
    }
}
