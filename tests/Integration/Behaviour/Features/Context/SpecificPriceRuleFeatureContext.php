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

namespace Tests\Integration\Behaviour\Features\Context;

use SpecificPriceRule;

class SpecificPriceRuleFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var SpecificPriceRule[]
     */
    protected $specificPriceRules = [];

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanCartRuleFixtures()
    {
        foreach ($this->specificPriceRules as $specificPriceRule) {
            $specificPriceRule->delete();
        }
        $this->specificPriceRules = [];
    }

    /**
     * @Given /^there is a specific price rule named "(.+)" with a percent discount of (\d+)% and minimum quantity of (\d+)$/
     */
    public function insertSpecificPriceRulePercent($priceRuleName, $value, $minimalQuantity)
    {
        $this->createSpecificPriceRule($priceRuleName, 'percentage', $value, $minimalQuantity);
    }

    /**
     * @Given /^there is a specific price rule named "(.+)" with an amount discount of (\d+) and minimum quantity of (\d+)$/
     */
    public function insertSpecificPriceRuleAmount($priceRuleName, $value, $minimalQuantity)
    {
        $this->createSpecificPriceRule($priceRuleName, 'amount', $value, $minimalQuantity);
    }

    protected function createSpecificPriceRule($priceRuleName, $type, $value, $minimalQuantity)
    {
        $rule = new SpecificPriceRule();
        $rule->id_shop = \Context::getContext()->shop->id;
        $rule->id_currency = 0; // 0 = all
        $rule->id_country = 0; // 0 = all
        $rule->id_group = 0; // 0 = all
        $rule->price = -1; // -1 to keep original product price
        $rule->reduction_tax = 1;
        $rule->name = 'price rule name';
        $rule->reduction_type = $type;
        $rule->reduction = $value;
        $rule->from_quantity = $minimalQuantity;
        $rule->add();
        $this->specificPriceRules[$priceRuleName] = $rule;

        $rule->apply();
    }

    /**
     * @Given /^specific price rule "(.+)" changes product price to (\d+\.\d+)$/
     */
    public function setPriceModifier($priceRuleName, $price)
    {
        $this->specificPriceRules[$priceRuleName]->price = $price;
        $this->specificPriceRules[$priceRuleName]->save();
        $this->specificPriceRules[$priceRuleName]->apply();
    }

    /**
     * @param $priceRuleName
     */
    public function checkCartRuleWithNameExists($priceRuleName)
    {
        $this->checkFixtureExists($this->specificPriceRules, 'Price rule', $priceRuleName);
    }
}
