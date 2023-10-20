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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Tax;

use Country;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use State;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tax;
use TaxCalculator;
use TaxRule;
use TaxRulesGroup;

class TaxComputerTest extends KernelTestCase
{
    /**
     * @var TaxComputer
     */
    private $taxComputer;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->taxComputer = $container->get(TaxComputer::class);
    }

    /**
     * @dataProvider getComputePriceData
     */
    public function testComputePriceWithTaxes(float $taxRate, int $countryId, array $stateIds, string $priceWithoutTaxes, string $priceWithTaxes, int $behavior): void
    {
        $taxRuleGroupId = $this->addTaxRuleGroup($taxRate, $countryId, $stateIds, $behavior);
        $computedPrice = $this->taxComputer->computePriceWithTaxes(
            new DecimalNumber($priceWithoutTaxes),
            new TaxRulesGroupId($taxRuleGroupId),
            new CountryId($countryId)
        );

        $this->assertEquals(new DecimalNumber($priceWithTaxes), $computedPrice);
    }

    /**
     * @dataProvider getComputePriceData
     */
    public function testComputePriceWithoutTaxes(float $taxRate, int $countryId, array $stateIds, string $priceWithoutTaxes, string $priceWithTaxes, int $behavior): void
    {
        $taxRuleGroupId = $this->addTaxRuleGroup($taxRate, $countryId, $stateIds, $behavior);
        $computedPrice = $this->taxComputer->computePriceWithoutTaxes(
            new DecimalNumber($priceWithTaxes),
            new TaxRulesGroupId($taxRuleGroupId),
            new CountryId($countryId)
        );

        $this->assertEquals(new DecimalNumber($priceWithoutTaxes), $computedPrice);
    }

    public function getComputePriceData()
    {
        $countryId = Country::getByIso('fr');
        $country = new Country($countryId);

        /* taxes for countries without states */
        yield [20.0, $country->id, [0],  '10.00', '12.00', TaxCalculator::ONE_TAX_ONLY_METHOD];
        yield [6.0, $country->id, [0], '10.00', '10.60', TaxCalculator::ONE_TAX_ONLY_METHOD];
        yield [21.3, $country->id, [0], '10.00', '12.13', TaxCalculator::ONE_TAX_ONLY_METHOD];
        yield [14.7, $country->id, [0], '8.00', '9.176', TaxCalculator::ONE_TAX_ONLY_METHOD];
        // tax rate is rounded to 3.148
        yield [3.14769, $country->id, [0], '8.666', '8.93880568', TaxCalculator::ONE_TAX_ONLY_METHOD];

        $countryId = Country::getByIso('us');
        $country = new Country($countryId);
        $states = State::getStatesByIdCountry($countryId, true);
        $state = $states[0];
        $stateId = (int) $state['id_state'];

        /* taxes for countries with state */
        yield [20.0, $country->id, [$stateId],  '10.00', '12.00', TaxCalculator::ONE_TAX_ONLY_METHOD];
        yield [6.0, $country->id, [$stateId], '10.00', '10.60', TaxCalculator::ONE_TAX_ONLY_METHOD];
        yield [21.3, $country->id, [$stateId], '10.00', '12.13', TaxCalculator::ONE_TAX_ONLY_METHOD];
        yield [14.7, $country->id, [$stateId], '8.00', '9.176', TaxCalculator::ONE_TAX_ONLY_METHOD];
        // tax rate is rounded to 3.148
        yield [3.14769, $country->id, [$stateId], '8.666', '8.93880568', TaxCalculator::ONE_TAX_ONLY_METHOD];

        $secondState = $states[1];
        $secondStateId = (int) $secondState['id_state'];

        /* taxes for countries with states and tax has multiple states and only first one should be taken */
        yield [20.0, $country->id, [$stateId, $secondStateId],  '10.00', '12.00', TaxCalculator::COMBINE_METHOD];
        yield [6.0, $country->id, [$stateId, $secondStateId], '10.00', '10.60', TaxCalculator::COMBINE_METHOD];
        yield [21.3, $country->id, [$stateId, $secondStateId], '10.00', '12.13', TaxCalculator::COMBINE_METHOD];
        yield [14.7, $country->id, [$stateId, $secondStateId], '8.00', '9.176', TaxCalculator::COMBINE_METHOD];
        // tax rate is rounded to 3.148
        yield [3.14769, $country->id, [$stateId, $secondStateId], '8.666', '8.93880568', TaxCalculator::COMBINE_METHOD];

        /* taxes for countries with states and have multiple combined taxes for same state so the taxes are combined */
        yield [20.0, $country->id, [$stateId, $stateId],  '10.00', '14.00', TaxCalculator::COMBINE_METHOD];
        yield [6.0, $country->id, [$stateId, $stateId], '10.00', '11.20', TaxCalculator::COMBINE_METHOD];
        yield [21.3, $country->id, [$stateId, $stateId], '10.00', '14.26', TaxCalculator::COMBINE_METHOD];
        yield [14.7, $country->id, [$stateId, $stateId], '8.00', '10.352', TaxCalculator::COMBINE_METHOD];
        // tax rate is rounded to 3.148
        yield [3.14769, $country->id, [$stateId, $stateId], '8.666', '9.21161136', TaxCalculator::COMBINE_METHOD];
    }

    /**
     * @param float $taxRate
     * @param int $countryId
     * @param int[] $stateIds
     *
     * @return int
     */
    private function addTaxRuleGroup(float $taxRate, int $countryId, array $stateIds, int $behavior)
    {
        $tax = new Tax();
        $tax->name = [1 => 'testTax'];
        $tax->active = true;
        $tax->rate = $taxRate;
        $tax->add();

        $taxRulesGroup = new TaxRulesGroup();
        $taxRulesGroup->name = 'taxRulesGroupTestName';
        $taxRulesGroup->active = true;
        $taxRulesGroup->deleted = false;
        $taxRulesGroup->save();

        foreach ($stateIds as $stateId) {
            $taxRule = new TaxRule();
            $taxRule->id_tax = $tax->id;
            $taxRule->id_tax_rules_group = $taxRulesGroup->id;
            $taxRule->behavior = $behavior;
            $taxRule->id_country = $countryId;
            $taxRule->id_state = $stateId;
            $taxRule->save();
        }

        return (int) $taxRulesGroup->id;
    }
}
