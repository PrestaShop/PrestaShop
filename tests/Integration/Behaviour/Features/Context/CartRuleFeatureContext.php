<?php
/**
 * 2007-2019 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Cache;
use CartRule;
use Configuration;
use DateInterval;
use DateTime;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Db;

class CartRuleFeatureContext extends AbstractPrestaShopFeatureContext
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
     * @var CarrierFeatureContext
     */
    protected $carrierFeatureContext;

    /**
     * @var CustomerFeatureContext
     */
    protected $customerFeatureContext;

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanCartRuleFixtures()
    {
        foreach ($this->cartRules as $cartRule) {
            $cartRule->delete();
        }
        $this->cartRules = [];
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->productFeatureContext = $scope->getEnvironment()->getContext(ProductFeatureContext::class);
        $this->carrierFeatureContext = $scope->getEnvironment()->getContext(CarrierFeatureContext::class);
        $this->customerFeatureContext = $scope->getEnvironment()->getContext(CustomerFeatureContext::class);
    }

    /**
     * @Given /^there is a cart rule named "(.+)" that applies a percent discount of (\d+\.\d+)% with priority (\d+), quantity of (.*) and quantity per user (.*)$/
     */
    public function thereIsACartRuleWithNameAndPercentDiscountOf50AndPriorityOfAndQuantityOfAndQuantityPerUserOf($cartRuleName, $percent, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $this->createCartRule($cartRuleName, $percent, 0, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser);
    }

    /**
     * @Given /^there is a cart rule named "(.+)" that applies an amount discount of (\d+\.\d+) with priority (\d+), quantity of (.*) and quantity per user (.*)$/
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
     * @Given /^cart rule "(.+)" has a discount code "(.+)"$/
     */
    public function cartRuleNamedHasACode($cartRuleName, $cartRuleCode)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->code = $cartRuleCode;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" is restricted to product "(.+)"$/
     */
    public function cartRuleNamedIsRestrictedToProductNamed($cartRuleName, $productName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->productFeatureContext->checkProductWithNameExists($productName);
        $this->cartRules[$cartRuleName]->product_restriction = true;
        $this->cartRules[$cartRuleName]->reduction_product = $this->productFeatureContext->getProductWithName($productName)->id;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" is restricted to carrier "(.+)"$/
     */
    public function cartRuleNamedIsRestrictedToCarrierNamed($cartRuleName, $carrierName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->carrierFeatureContext->checkCarrierWithNameExists($carrierName);
        $this->cartRules[$cartRuleName]->carrier_restriction = 1;
        $this->cartRules[$cartRuleName]->save();
        Db::getInstance()->execute('
          INSERT INTO ' . _DB_PREFIX_ . "cart_rule_carrier(`id_cart_rule`, `id_carrier`)
          VALUES('" . (int) $this->cartRules[$cartRuleName]->id . "',
          '" . (int) $this->carrierFeatureContext->getCarrierWithName($carrierName)->id . "')
        ");
        Cache::clear();
    }

    /**
     * @Given /^cart rule "(.+)" offers a gift product "(.+)"$/
     */
    public function cartRuleNamedHasAGiftProductNamed($cartRuleName, $productName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->productFeatureContext->checkProductWithNameExists($productName);
        $this->cartRules[$cartRuleName]->gift_product = $this->productFeatureContext->getProductWithName($productName)->id;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Then /^cart rule "(.+)" cannot be applied to my cart$/
     */
    public function cartRuleNamedCannotBeAppliedToMyCart($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
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
     * @Then /^cart rule "(.+)" can be applied to my cart$/
     */
    public function cartRuleNamedCanBeAppliedToMyCart($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
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
     * @When /^I use the discount "(.+)"$/
     *
     * @param $cartRuleName
     */
    public function iAddCartRuleNamedToMyCart($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->getCurrentCart()->addCartRule($this->cartRules[$cartRuleName]->id);
    }

    /**
     * @When /^at least one cart rule applies today for customer with id (\d+)$/
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

    /**
     * @When /^cart rule count in my cart should be (\d+)$/
     */
    public function cartRuleInCartCount($cartRuleCount)
    {
        $result = count($this->getCurrentCart()->getCartRules());
        if ($result != $cartRuleCount) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $cartRuleCount,
                    $result
                )
            );
        }
    }

    /**
     * @param $cartRuleName
     */
    public function checkCartRuleWithNameExists($cartRuleName)
    {
        $this->checkFixtureExists($this->cartRules, 'Cart rule', $cartRuleName);
    }

    /**
     * @Then /^customer "(.+)" should have (\d+) cart rule(?:s)? that apply to (?:him|her)$/
     */
    public function checkCartRuleCountForCustomer($customerName, $expectedCount)
    {
        $this->customerFeatureContext->checkCustomerWithNameExists($customerName);
        $customer = $this->customerFeatureContext->getCustomerWithName($customerName);
        $cartRules = CartRule::getCustomerCartRules($customer->id_lang, $customer->id, true, false);
        if ($expectedCount != count($cartRules)) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedCount,
                    count($cartRules)
                )
            );
        }
    }

    /**
     * @Then /^cart rule for customer "(.+)" in position (\d+) should apply a discount of (\d+.\d+)$/
     */
    public function checkCartRuleValueForCustomer($customerName, $position, $expectedValue)
    {
        $this->customerFeatureContext->checkCustomerWithNameExists($customerName);
        $customer = $this->customerFeatureContext->getCustomerWithName($customerName);
        $cartRules = CartRule::getCustomerCartRules($customer->id_lang, $customer->id, true, false);
        if (!isset($cartRules[$position - 1]['id_cart_rule'])) {
            throw new \Exception(
                sprintf('Undefined cartRule on position #%s', $position - 1)
            );
        }
        $cartRule = new CartRule($cartRules[$position - 1]['id_cart_rule']);
        if ($expectedValue != $cartRule->reduction_amount) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedValue,
                    $cartRule->reduction_amount
                )
            );
        }
    }
}
