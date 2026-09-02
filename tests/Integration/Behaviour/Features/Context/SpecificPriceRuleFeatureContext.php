<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Context;
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
        $rule->id_shop = Context::getContext()->shop->id;
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
     * @param string $priceRuleName
     */
    public function checkCartRuleWithNameExists(string $priceRuleName): void
    {
        $this->checkFixtureExists($this->specificPriceRules, 'Price rule', $priceRuleName);
    }
}
