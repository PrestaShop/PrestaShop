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

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Cache;
use CartRule;
use Configuration;
use Context;
use DateInterval;
use DateTime;
use Db;
use Order;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use RuntimeException;
use Validate;

class CartRuleFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;
    use SharedStorageTrait;
    use LastExceptionTrait;

    /**
     * @var CartRule[]
     */
    protected $cartRules = [];

    /**
     * @var CountryFeatureContext
     */
    protected $countryFeatureContext;

    /**
     * @var LegacyProductFeatureContext
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
     * @var CategoryFeatureContext
     */
    protected $categoryFeatureContext;

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
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        /** @var CountryFeatureContext $countryFeatureContext */
        $countryFeatureContext = $environment->getContext(CountryFeatureContext::class);
        /** @var LegacyProductFeatureContext $productFeatureContext */
        $productFeatureContext = $environment->getContext(LegacyProductFeatureContext::class);
        /** @var CarrierFeatureContext $carrierFeatureContext */
        $carrierFeatureContext = $environment->getContext(CarrierFeatureContext::class);
        /** @var CustomerFeatureContext $customerFeatureContext */
        $customerFeatureContext = $environment->getContext(CustomerFeatureContext::class);
        /** @var CategoryFeatureContext $categoryFeatureContext */
        $categoryFeatureContext = $environment->getContext(CategoryFeatureContext::class);

        $this->countryFeatureContext = $countryFeatureContext;
        $this->productFeatureContext = $productFeatureContext;
        $this->carrierFeatureContext = $carrierFeatureContext;
        $this->customerFeatureContext = $customerFeatureContext;
        $this->categoryFeatureContext = $categoryFeatureContext;
    }

    /**
     * @Given /^there is a cart rule named "(.+)" that applies a percent discount of (\d+\.\d+)% with priority (\d+), quantity of (\d+) and quantity per user (\d+)$/
     */
    public function thereIsACartRuleWithNameAndPercentDiscountOf50AndPriorityOfAndQuantityOfAndQuantityPerUserOf($cartRuleName, $percent, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $this->createCartRule($cartRuleName, $percent, 0, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser);
    }

    /**
     * @Given /^there is a cart rule named "(.+)" that applies no discount with priority (\d+), quantity of (\d+) and quantity per user (\d+)$/
     */
    public function thereIsACartRuleWithNameAndNoDiscountAndPriorityOfAndQuantityOfAndQuantityPerUserOf($cartRuleName, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $this->createCartRule($cartRuleName, 0, 0, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser);
    }

    /**
     * @Given /^there is a cart rule named "(.+)" that applies an amount discount of (\d+\.\d+) with priority (\d+), quantity of (\d+) and quantity per user (\d+)$/
     */
    public function thereIsACartRuleWithNameAndAmountDiscountOfAndPriorityOfAndQuantityOfAndQuantityPerUserOf($cartRuleName, $amount, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser)
    {
        $this->createCartRule($cartRuleName, 0, $amount, $priority, $cartRuleQuantity, $cartRuleQuantityPerUser);
    }

    protected function createCartRule(
        $cartRuleName,
        $percent,
        $amount,
        $priority,
        $cartRuleQuantity,
        $cartRuleQuantityPerUser
    ) {
        $cartRule = new CartRule();
        $cartRule->reduction_percent = $percent;
        $cartRule->reduction_amount = $amount;
        $cartRule->name = [Configuration::get('PS_LANG_DEFAULT') => $cartRuleName];
        $cartRule->description = $cartRuleName;
        $cartRule->priority = $priority;
        $cartRule->quantity = $cartRuleQuantity;
        $cartRule->quantity_per_user = $cartRuleQuantityPerUser;
        $now = new DateTime();
        // sub 1s to avoid bad comparisons with strictly greater than
        $now->sub(new DateInterval('P2D'));
        $cartRule->date_from = $now->format('Y-m-d H:i:s');
        $now->add(new DateInterval('P1Y'));
        $cartRule->date_to = $now->format('Y-m-d H:i:s');
        $cartRule->active = true;
        $cartRule->add();
        $this->cartRules[$cartRuleName] = $cartRule;
        SharedStorage::getStorage()->set($cartRuleName, $cartRule->id);
    }

    /**
     * @Given /^cart rule "(.+?)" is restricted to the category "(.+?)" with a quantity of (\d+)$/
     */
    public function cartRuleWithProductRuleRestriction(string $cartRuleName, string $categoryName, int $quantity)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->categoryFeatureContext->checkCategoryWithNameExists($categoryName);
        $category = $this->categoryFeatureContext->getCategoryWithName($categoryName);

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) ' .
            'VALUES (' . (int) $this->cartRules[$cartRuleName]->id . ', ' . $quantity . ')'
        );
        $idProductRuleGroup = Db::getInstance()->Insert_ID();

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) ' .
            'VALUES (' . (int) $idProductRuleGroup . ', "categories")'
        );
        $idProductRule = Db::getInstance()->Insert_ID();

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) ' .
            'VALUES (' . (int) $idProductRuleGroup . ', ' . $category->id . ')'
        );
    }

    /**
     * @Given /^cart rule "(.+)" has a discount code "(.+)"$/
     */
    public function cartRuleNamedHasACode($cartRuleName, $cartRuleCode)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->code = $cartRuleCode;
        $this->cartRules[$cartRuleName]->save();
        SharedStorage::getStorage()->set($cartRuleCode, $this->cartRules[$cartRuleName]->id);
    }

    /**
     * @Given /^cart rule "(.+)" has no discount code$/
     */
    public function cartRuleNamedHasNoCode($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->code = '';
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" is restricted to product "(.+)"$/
     * @Given /^cart rule "(.+)" is restricted to product "(.+)" with a quantity of (\d+)$/
     */
    public function cartRuleNamedIsRestrictedToProductNamed(
        string $cartRuleName,
        string $productName,
        int $quantity = 1
    ): void {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->productFeatureContext->checkProductWithNameExists($productName);

        $restrictedProduct = $this->productFeatureContext->getProductWithName($productName);
        $this->cartRules[$cartRuleName]->product_restriction = true;
        $this->cartRules[$cartRuleName]->reduction_product = $restrictedProduct->id;
        $this->cartRules[$cartRuleName]->save();

        // The reduction_product is not enough, we need to define product rules for condition (this is done by the controller usually)
        Db::getInstance()->insert(
            'cart_rule_product_rule_group',
            ['id_cart_rule' => $this->cartRules[$cartRuleName]->id, 'quantity' => $quantity]
        );
        $productRuleGroupId = Db::getInstance()->Insert_ID();
        Db::getInstance()->insert(
            'cart_rule_product_rule',
            ['id_product_rule_group' => $productRuleGroupId, 'type' => 'products']
        );
        $productRuleId = Db::getInstance()->Insert_ID();
        Db::getInstance()->insert(
            'cart_rule_product_rule_value',
            ['id_product_rule' => $productRuleId, 'id_item' => $restrictedProduct->id]
        );
    }

    /**
     * @Given /^cart rule "(.+)" is restricted to carrier "(.+)"$/
     */
    public function cartRuleNamedIsRestrictedToCarrierNamed($cartRuleName, $carrierName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->carrierFeatureContext->checkCarrierWithNameExists($carrierName);
        $this->cartRules[$cartRuleName]->carrier_restriction = true;
        $this->cartRules[$cartRuleName]->save();
        Db::getInstance()->execute('
          INSERT INTO ' . _DB_PREFIX_ . "cart_rule_carrier(`id_cart_rule`, `id_carrier`)
          VALUES('" . (int) $this->cartRules[$cartRuleName]->id . "',
          '" . (int) $this->carrierFeatureContext->getCarrierWithName($carrierName)->id . "')
        ");
        Cache::clear();
    }

    /**
     * @Given /^cart rule "(.+)" is restricted to country "(.+)"$/
     */
    public function cartRuleNamedIsRestrictedToCountryNamed(string $cartRuleName, string $country)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->countryFeatureContext->checkCountryWithIsoCodeExists($country);
        $this->cartRules[$cartRuleName]->country_restriction = true;
        $this->cartRules[$cartRuleName]->save();

        $idCartRule = (int) $this->cartRules[$cartRuleName]->id;
        $idCountry = (int) $this->countryFeatureContext->getCountryWithIsoCode($country);
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_country(`id_cart_rule`, `id_country`) ' .
            'VALUES(' . $idCartRule . ', ' . $idCountry . ')'
        );
        Cache::clear();
    }

    /**
     * @Given /^cart rule "(.+)" is restricted to cheapest product$/
     */
    public function cartRuleIsRestrictedToCheapestProduct($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->product_restriction = true;
        $this->cartRules[$cartRuleName]->reduction_product = -1;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" is restricted on the selection of products$/
     */
    public function cartRuleIsRestrictedToSelectionProducts(string $cartRuleName): void
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->reduction_product = -2;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" is applied on every order$/
     */
    public function cartRuleIsRestrictedToEveryOrder($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->product_restriction = false;
        $this->cartRules[$cartRuleName]->reduction_product = 0;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" is disabled$/
     */
    public function cartRuleIsDisabled($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->active = false;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @When /^I enable cart rule "(.+)"$/
     */
    public function enableCartRule($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->active = true;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" does not apply to already discounted products$/
     */
    public function cartRuleDoesNotApplyToDiscountedProduct($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->reduction_exclude_special = true;
        $this->cartRules[$cartRuleName]->save();
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
     * @Given /^cart rule "(.+)" offers free shipping$/
     */
    public function cartRuleOffersFreeShipping($cartRuleName)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->free_shipping = true;
        $this->cartRules[$cartRuleName]->save();
    }

    /**
     * @Given /^cart rule "(.+)" applies discount only when cart total is above (\d+\.\d+)$/
     */
    public function cartRuleAppliesBetween($cartRuleName, $min)
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->cartRules[$cartRuleName]->minimum_amount = $min;
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
            throw new \RuntimeException(sprintf('Expects false, got %s instead', $result));
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
            throw new \RuntimeException(sprintf('Expects true, got %s instead', $result));
        }
    }

    /**
     * @When /^I use the discount "(.+)"$/
     *
     * @param string $cartRuleName
     */
    public function iAddCartRuleNamedToMyCart(string $cartRuleName): void
    {
        $this->checkCartRuleWithNameExists($cartRuleName);
        $this->getCurrentCart()->addCartRule($this->cartRules[$cartRuleName]->id);
    }

    /**
     * @When /^I apply the discount code "(.+)"$/
     *
     * @param string $code
     *
     * @return void
     */
    public function applyCartRuleByCode(string $code): void
    {
        $cartRuleId = $this->getSharedStorage()->get($code);
        $cartRule = new CartRule($cartRuleId);

        if (!Validate::isLoadedObject($cartRule)) {
            throw new RuntimeException(sprintf('Failed to load cart rule %d', $cartRule->id));
        }

        if ($errorMessage = $cartRule->checkValidity(Context::getContext())) {
            // checkValidity method doesn't throw exception, but returns string
            // so we map it to error code and reuse the LastExceptionTrait to be able to assert the exception on next step
            $this->setLastException(
                new CartRuleValidityException($errorMessage, $this->getCartRuleValidityCodeByMessage($errorMessage))
            );

            return;
        }

        $this->getCurrentCart()->addCartRule((int) $cartRule->id);
    }

    /**
     * @Then I should get cart rule validation error saying :expectedMessage
     *
     * @param string $expectedMessage
     *
     * @return void
     */
    public function assertCartRuleValidationError(string $expectedMessage): void
    {
        $this->assertLastErrorIs(
            CartRuleValidityException::class,
            $this->getCartRuleValidityCodeByMessage($expectedMessage)
        );
    }

    /**
     * @todo: What is this method supposed to assert. Especially in cases when it checks customer id 0??
     * @When /^at least one cart rule applies today for customer with id (\d+)$/
     */
    public function someCartRulesExistTodayForCustomerWithId($customerId)
    {
        $result = CartRule::haveCartRuleToday($customerId);
        if (!$result) {
            throw new \RuntimeException(sprintf('Expects true, got %s instead', $result));
        }
    }

    /**
     * @When /^cart rule count in my cart should be (\d+)$/
     */
    public function cartRuleInCartCount($cartRuleCount)
    {
        $result = count($this->getCurrentCart()->getCartRules());
        if ($result != $cartRuleCount) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $cartRuleCount, $result));
        }
    }

    /**
     * @param string $cartRuleName
     */
    public function checkCartRuleWithNameExists(string $cartRuleName): void
    {
        $this->checkFixtureExists($this->cartRules, 'Cart rule', $cartRuleName);
    }

    /**
     * @Then /^customer "(.+)" should have (\d+) cart rule(?:s)? that apply to (?:him|her)$/
     */
    public function checkCartRuleCountForCustomer(string $customerName, int $expectedCount)
    {
        $this->customerFeatureContext->checkCustomerWithNameExists($customerName);
        $customer = $this->customerFeatureContext->getCustomerWithName($customerName);
        $cartRules = CartRule::getCustomerCartRules($customer->id_lang, $customer->id, true, false);
        if ($expectedCount != count($cartRules)) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $expectedCount, count($cartRules)));
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
            throw new \Exception(sprintf('Undefined cartRule on position #%s', $position - 1));
        }
        $cartRule = new CartRule($cartRules[$position - 1]['id_cart_rule']);
        if ($expectedValue != $cartRule->reduction_amount) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $expectedValue, $cartRule->reduction_amount));
        }
    }

    /**
     * @Then the current cart should have the following contextual reductions:
     *
     * @param TableNode $table
     */
    public function checkCartRuleContextualValue(TableNode $table)
    {
        $contextualReductionValues = $table->getRowsHash();
        $cartRules = $this->getCurrentCart()->getCartRules();

        foreach ($cartRules as $currentCartRule) {
            if (!isset($contextualReductionValues[$currentCartRule['description']])) {
                throw new \RuntimeException(sprintf('Cart rule %s was not expected.', $currentCartRule['description']));
            }

            // float numbers are compared as string because float numbers seemingly equals can still be unequals.
            if ((string) $currentCartRule['value_real'] !== (string) $contextualReductionValues[$currentCartRule['description']]) {
                throw new \RuntimeException(
                    sprintf(
                        'Expects %s, got %s instead',
                        $contextualReductionValues[$currentCartRule['description']],
                        $currentCartRule['value_real']
                    )
                );
            }
            unset($contextualReductionValues[$currentCartRule['description']]);
        }

        if (!empty($contextualReductionValues)) {
            throw new \RuntimeException(sprintf('The cart rule "%s" was not found', reset($contextualReductionValues)));
        }
    }

    /**
     * @Then usage limit per user for cart rule :cartRuleReference is detected
     *
     * @param string $cartRuleReference
     */
    public function checkCartRuleUsageLimitIsDetected(string $cartRuleReference)
    {
        // Using the string error message as a check value is far from ideal, but the legacy `checkValidity` method
        // only returns an error string or a boolean, which would keep us from detecting the error returned
        $expectedErrorMessage = 'You cannot use this voucher anymore (usage limit reached)';

        $cartRuleId = (int) SharedStorage::getStorage()->get($cartRuleReference);
        $cartRule = new CartRule($cartRuleId);
        $result = $cartRule->checkValidity(Context::getContext(), true);
        if ($result != $expectedErrorMessage) {
            throw new \RuntimeException(sprintf('Expects "usage limit reached" error message, got %s instead', $result));
        }
    }

    /**
     * Legacy cart rule validation returns errors as strings (CartRule::checkVBalidity()),
     * so to identify lastError in steps we will use this custom map,
     * which will eventually allow us to reuse LastExceptionTrait and assert exceptions by codes
     *
     * @return int
     */
    private function getCartRuleValidityCodeByMessage(string $message): int
    {
        $map = [
            'This voucher is disabled' => 100,
            'This voucher has already been used' => 101,
            'This voucher is not valid yet' => 102,
            'This voucher has expired' => 103,
            'You cannot use this voucher anymore (usage limit reached)' => 104,
            'You cannot use this voucher' => 105,
            'You must choose a delivery address before applying this voucher to your order' => 106,
            'You cannot use this voucher in your country of delivery' => 107,
            'You must choose a carrier before applying this voucher to your order' => 108,
            'You cannot use this voucher with this carrier' => 109,
            'You cannot use this voucher on products on sale' => 110,
            'You cannot use this voucher in an empty cart' => 111,
            'You cannot use this voucher with these products' => 112,
            'The minimum amount to benefit from this promo code is' => 113,
            'This voucher is already in your cart' => 114,
            'This voucher is not combinable with an other voucher already in your cart:' => 115,
            'Cart is empty' => 116,
        ];

        foreach ($map as $errorPart => $code) {
            // @todo:
            //     some of these errors have %s placeholders, so we just match the most part of it,
            //     it should be convenient enough for now, but will need improvement later
            if (0 === strpos($message, $errorPart)) {
                return $code;
            }
        }

        throw new RuntimeException(sprintf(
            'Invalid error-code mapping in test. Couldn\'t find the code for message "%s"',
            $message
        ));
    }
}
