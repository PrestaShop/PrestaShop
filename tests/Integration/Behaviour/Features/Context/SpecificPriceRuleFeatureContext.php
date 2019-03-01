<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Tester\Exception\PendingException;
use CartRule;
use Configuration;
use DateInterval;
use DateTime;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use SpecificPriceRule;

class SpecificPriceRuleFeatureContext implements BehatContext
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
    public function cleanCartRules()
    {
        foreach ($this->specificPriceRules as $specificPriceRule) {
            $specificPriceRule->delete();
        }
        $this->specificPriceRules = [];
    }

    /**
     * @Given /^There is a specific price rule with name (.+) and reduction in (percentage|amount) and reduction value of (\d+) and minimal quantity of (\d+)$/
     */
    public function insertSpecificPriceRule($priceRuleName, $type, $value, $minimalQuantity)
    {
        $rule = new SpecificPriceRule();
        $rule->id_shop = \Context::getContext()->shop->id;
        $rule->id_currency = 0; // 0 = all
        $rule->id_country = 0; // 0 = all
        $rule->id_group = 0; // 0 = all
        $rule->price = -1;// -1 to keep original product price
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
     * @Given /^specific price rule with name (.+) change product price to ([\d\.]+)$/
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
        if (!isset($this->specificPriceRules[$priceRuleName])) {
            throw new \Exception('Price rule with name "' . $priceRuleName . '" was not added in fixtures');
        }
    }
}
