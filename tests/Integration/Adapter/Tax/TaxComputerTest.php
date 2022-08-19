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

namespace Tests\Integration\Adapter\Tax;

use Country;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use State;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tax;
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
        $this->taxComputer = $container->get('prestashop.adapter.tax.tax_computer');
    }

    /**
     * @dataProvider getComputePriceData
     */
    public function testComputePriceWithTaxes(float $taxRate, int $countryId, int $stateId, string $priceWithoutTaxes, string $priceWithTaxes): void
    {
        $taxRuleGroupId = $this->addTaxRuleGroup($taxRate, $countryId, $stateId);
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
    public function testComputePriceWithoutTaxes(float $taxRate, int $countryId, int $stateId, string $priceWithoutTaxes, string $priceWithTaxes): void
    {
        $taxRuleGroupId = $this->addTaxRuleGroup($taxRate, $countryId, $stateId);
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

        yield [20.0, $country->id, 0,  '10.00', '12.00'];
        yield [6.0, $country->id, 0, '10.00', '10.60'];
        yield [21.3, $country->id, 0, '10.00', '12.13'];
        yield [14.7, $country->id, 0, '8.00', '9.176'];
        // tax rate is rounded to 3.148
        yield [3.14769, $country->id, 0, '8.666', '8.93880568'];

        $countryId = Country::getByIso('us');
        $country = new Country($countryId);
        $states = State::getStatesByIdCountry($countryId, true);
        $state = reset($states);
        $stateId = (int) $state['id_state'];

        yield [20.0, $country->id, $stateId,  '10.00', '12.00'];
        yield [6.0, $country->id, $stateId, '10.00', '10.60'];
        yield [21.3, $country->id, $stateId, '10.00', '12.13'];
        yield [14.7, $country->id, $stateId, '8.00', '9.176'];
        // tax rate is rounded to 3.148
        yield [3.14769, $country->id, $stateId, '8.666', '8.93880568'];
    }

    private function addTaxRuleGroup(float $taxRate, int $countryId, int $stateId)
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

        $taxRule = new TaxRule();
        $taxRule->id_tax = $tax->id;
        $taxRule->id_tax_rules_group = $taxRulesGroup->id;
        $taxRule->behavior = 1;
        $taxRule->id_country = $countryId;
        $taxRule->id_state = $stateId;
        $taxRule->save();

        return (int) $taxRulesGroup->id;
    }
}
