<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Tester\Exception\PendingException;
use CartRule;
use Configuration;
use DateInterval;
use DateTime;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class CartRuleFeatureContext implements BehatContext
{

    use CartAwareTrait;

    /**
     * @var CartRule[]
     */
    protected $cartRules = [];

    /**
     * @var ProductFeatureContext
     */
    protected $productFeatureContext;

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function afterScenario_cleanCartRules()
    {
        foreach ($this->cartRules as $cartRule) {
            $cartRule->delete();
        }
        $this->cartRules = [];
    }


    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->productFeatureContext = $scope->getEnvironment()->getContext('Tests\Integration\Behaviour\Features\Context\ProductFeatureContext');
    }

    /**
     * @Given /^There is a cart rule with name "([^"]*)" and percent discount of ([\d\.]+)% and priority of (\d+) and quantity of (\d+) and quantity per user of (\d+)$/
     */
    public function thereIsACartRuleWithNameAndPercentDiscountOf50AndPriorityOfAndQuantityOfAndQuantityPerUserOf($cartRuleName, $percent, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $this->createCartRule($cartRuleName, $percent, 0, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser);
    }

    /**
     * @Given /^There is a cart rule with name "([^"]*)" and amount discount of (\d+) and priority of (\d+) and quantity of (\d+) and quantity per user of (\d+)$/
     */
    public function thereIsACartRuleWithNameAndAmountDiscountOfAndPriorityOfAndQuantityOfAndQuantityPerUserOf($cartRuleName, $amount, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $this->createCartRule($cartRuleName, 0, $amount, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser);
    }

    protected function createCartRule($cartRuleName, $percent, $amount, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $cartRule = new CartRule();
        $cartRule->reduction_percent = $percent;
        $cartRule->reduction_amount = $amount;
        $cartRule->name = [Configuration::get('PS_LANG_DEFAULT') => $cartRuleName];
        $cartRule->priority = $priority;
        $cartRule->quantity = $cartRuleQuantity;
        $cartRule->quantity_per_user = $cartRuleQuantityPerUser;
        $now = new DateTime();
        // sub 1s to avoid bad comparisons with strictly greater than
        $now->sub(new DateInterval('P2D'));
        $cartRule->date_from = $now->format('Y-m-d H:i:s');
        $now->add(new DateInterval('P1Y'));
        $cartRule->date_to = $now->format('Y-m-d H:i:s');
        $cartRule->active = 1;
        $cartRule->add();
        $this->cartRules[$cartRuleName] = $cartRule;
    }

    /**
     * @Given Cart rule named :cartRuleName has a code: :cartRuleCode
     */
    public function cartRuleNamedHasACode($cartRuleName, $cartRuleCode)
    {
        if (!isset($this->cartRules[$cartRuleName])) {
            throw new \Exception('Cart rule with name "' . $cartRuleName . '" was not added in fixtures');
        }
        $this->cartRules[$cartRuleName]->code = $cartRuleCode;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^Cart rule named :cartRuleName is restricted to product named :productName
     */
    public function cartRuleNamedIsRestrictedToProductNamed($cartRuleName, $productName)
    {
        if (!isset($this->cartRules[$cartRuleName])) {
            throw new \Exception('Cart rule with name "' . $cartRuleName . '" was not added in fixtures');
        }
        if (!$this->productFeatureContext->productWithNameExists($productName)) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $this->cartRules[$cartRuleName]->product_restriction = true;
        $this->cartRules[$cartRuleName]->reduction_product = $this->productFeatureContext->getProductWithName($productName)->id;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^Cart rule named :cartRuleName is restricted to carrier named :carrierName
     */
    public function cartRuleNamedIsRestrictedToCarrierNamed($cartRuleName, $carrierName)
    {
        throw new PendingException();
    }

    /**
     * @Given Cart rule named :cartRuleName has a gift product named :productName
     */
    public function cartRuleNamedHasAGiftProductNamed($cartRuleName, $productName)
    {
        if (!isset($this->cartRules[$cartRuleName])) {
            throw new \Exception('Cart rule with name "' . $cartRuleName . '" was not added in fixtures');
        }
        if (!$this->productFeatureContext->productWithNameExists($productName)) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $this->cartRules[$cartRuleName]->gift_product = $this->productFeatureContext->getProductWithName($productName)->id;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Then Cart rule named :cartRuleName cannot be applied to my cart
     */
    public function cartRuleNamedCannotBeAppliedToMyCart($cartRuleName)
    {
        if (!isset($this->cartRules[$cartRuleName])) {
            throw new \Exception('Cart rule with name "' . $cartRuleName . '" was not added in fixtures');
        }
        $result = $this->cartRules[$cartRuleName]->checkValidity(\Context::getContext(), false, false);
        if ($result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects false, got %s instead',
                    $result
                )
            );
        }
    }

    /**
     * @Then Cart rule named :cartRuleName can be applied to my cart
     */
    public function cartRuleNamedCanBeAppliedToMyCart($cartRuleName)
    {
        if (!isset($this->cartRules[$cartRuleName])) {
            throw new \Exception('Cart rule with name "' . $cartRuleName . '" was not added in fixtures');
        }
        $result = $this->cartRules[$cartRuleName]->checkValidity(\Context::getContext(), false, false);
        if (!$result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects true, got %s instead',
                    $result
                )
            );
        }
    }

    /**
     * @When I add cart rule named :cartRuleName to my cart
     */
    public function iAddCartRuleNamedToMyCart($cartRuleName)
    {
        if (!isset($this->cartRules[$cartRuleName])) {
            throw new \Exception('Cart rule with name "' . $cartRuleName . '" was not added in fixtures');
        }
        $this->getCurrentCart()->addCartRule($this->cartRules[$cartRuleName]->id);
    }

    /**
     * @When Some cart rules exist today for customer with id :customerId
     */
    public function someCartRulesExistTodayForCustomerWithId($customerId)
    {
        $result = CartRule::haveCartRuleToday($customerId);
        if (!$result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects true, got %s instead',
                    $result
                )
            );
        }
    }
}
