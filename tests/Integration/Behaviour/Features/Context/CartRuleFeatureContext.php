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
use Context;
use Db;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
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
     * @Given /^cart rule "(.+?)" is restricted to the category "(.+?)" with a quantity of (\d+)$/
     */
    public function cartRuleWithProductRuleRestriction(string $cartRuleName, string $categoryName, int $quantity)
    {
        $cartRuleId = $this->getCartRuleId($cartRuleName);
        $this->categoryFeatureContext->checkCategoryWithNameExists($categoryName);
        $category = $this->categoryFeatureContext->getCategoryWithName($categoryName);

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) ' .
            'VALUES (' . (int) $cartRuleId . ', ' . $quantity . ')'
        );
        $idProductRuleGroup = Db::getInstance()->Insert_ID();

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`) ' .
            'VALUES (' . (int) $idProductRuleGroup . ', "categories")'
        );

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) ' .
            'VALUES (' . (int) $idProductRuleGroup . ', ' . $category->id . ')'
        );
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
        $cartRuleId = $this->getCartRuleId($cartRuleName);
        $this->productFeatureContext->checkProductWithNameExists($productName);
        $restrictedProduct = $this->productFeatureContext->getProductWithName($productName);
        $cartRule = new CartRule($cartRuleId);
        $cartRule->product_restriction = true;
        //@todo: product restriction and reduction_product are 2 different features. Should they really be mixed in here to one?
        $cartRule->reduction_product = $restrictedProduct->id;
        $cartRule->save();
        $this->cartRules[$cartRuleName] = $cartRule;

        // The reduction_product is not enough, we need to define product rules for condition (this is done by the controller usually)
        Db::getInstance()->insert(
            'cart_rule_product_rule_group',
            ['id_cart_rule' => $cartRuleId, 'quantity' => $quantity]
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
     * @Given /^cart rule "(.+)" is restricted to country "(.+)"$/
     */
    public function cartRuleNamedIsRestrictedToCountryNamed(string $cartRuleName, string $country)
    {
        $this->countryFeatureContext->checkCountryWithIsoCodeExists($country);
        $cartRuleId = $this->getCartRuleId($cartRuleName);

        $cartRule = new CartRule($cartRuleId);
        $cartRule->country_restriction = true;
        $cartRule->save();
        $this->cartRules[$cartRuleName] = $cartRule;

        $idCountry = (int) $this->countryFeatureContext->getCountryWithIsoCode($country);
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_country(`id_cart_rule`, `id_country`) ' .
            'VALUES(' . $cartRuleId . ', ' . $idCountry . ')'
        );
        Cache::clear();
    }

    /**
     * @When /^I enable cart rule "(.+)"$/
     */
    public function enableCartRule($cartRuleName)
    {
        $cartRule = $this->loadCartRule($cartRuleName);
        $cartRule->active = true;
        $cartRule->save();
    }

    /**
     * @Then /^cart rule "(.+)" can be applied to my cart$/
     */
    public function cartRuleNamedCanBeAppliedToMyCart($cartRuleName)
    {
        $cartRule = $this->loadCartRule($cartRuleName);
        $result = $cartRule->checkValidity(\Context::getContext(), false, false);

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
        $cartRule = $this->loadCartRule($cartRuleName);
        $this->getCurrentCart()->addCartRule($cartRule->id);
    }

    /**
     * @When /^I apply the voucher code "(.+)"$/
     *
     * @param string $code
     *
     * @return void
     */
    public function applyCartRuleByCode(string $code): void
    {
        $cartRule = $this->loadCartRule($code);

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
     * @Given discount code :cartRuleReference is not applied to my cart
     *
     * @param string $code
     *
     * @return void
     */
    public function assertDiscountCodeIsNotAppliedToCurrentCart(string $code): void
    {
        $cartRuleId = $this->getSharedStorage()->get($code);

        /** @var array<string, mixed> $cartRule */
        foreach ($this->getCurrentCart()->getCartRules() as $cartRule) {
            if ((int) $cartRule['id_cart_rule'] === $cartRuleId) {
                throw new RuntimeException(sprintf('Cart rule with code "%s" is applied to current cart', $code));
            }
        }
    }

    /**
     * @Given cart rule :referenceOrCode is applied to my cart
     * @Given discount :referenceOrCode is applied to my cart
     *
     * @param string $referenceOrCode
     *
     * @return void
     */
    public function assertCartRuleIsAppliedToCurrentCart(string $referenceOrCode): void
    {
        $cartRuleId = $this->getSharedStorage()->get($referenceOrCode);

        Cache::clean('Cart::getCartRules_*');
        $cartRules = $this->getCurrentCart()->getCartRules();
        /** @var array<string, mixed> $cartRule */
        foreach ($cartRules as $cartRule) {
            if ((int) $cartRule['id_cart_rule'] === $cartRuleId) {
                return;
            }
        }

        throw new RuntimeException(sprintf('Cart rule with code or reference "%s" is not applied to current cart', $referenceOrCode));
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
        $expectedCartRules = $table->getColumnsHash();
        $cartRuleRows = $this->getCurrentCart()->getCartRules();

        Assert::assertCount(count($expectedCartRules), $cartRuleRows, 'Unexpected cart rules count in cart');

        foreach ($cartRuleRows as $key => $cartRuleRow) {
            $cartRuleReference = $expectedCartRules[$key]['reference'];

            Assert::assertTrue(
                $this->getSharedStorage()->exists($cartRuleReference),
                sprintf('cart rule by reference "%s" doesnt exist', $cartRuleReference)
            );

            Assert::assertSame(
                (int) $cartRuleRow['id_cart_rule'],
                $this->getSharedStorage()->get($cartRuleReference),
                sprintf('Cart rule %s was not expected in cart (or the sequence is unexpected).', $cartRuleReference)
            );

            $expectedReduction = new DecimalNumber($expectedCartRules[$key]['reduction']);
            $actualReduction = new DecimalNumber((string) $cartRuleRow['value_real']);

            Assert::assertTrue(
                $actualReduction->equals($expectedReduction),
                sprintf('Unexpected contextual reduction. Expected %s, got %s', $expectedReduction, $actualReduction)
            );
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
            'Cart is empty' => 100,
            'This voucher is disabled' => 101,
            'This voucher has already been used' => 102,
            'This voucher is not valid yet' => 103,
            'This voucher has expired' => 104,
            'You must choose a delivery address before applying this voucher to your order' => 105,
            'You must choose a carrier before applying this voucher to your order' => 106,
            'The minimum amount to benefit from this promo code is' => 107,
            'This voucher is already in your cart' => 108,
            'This voucher is not combinable with an other voucher already in your cart:' => 109,
            'You cannot use this voucher with these products' => 110,
            'You cannot use this voucher on products on sale' => 111,
            'You cannot use this voucher with this carrier' => 112,
            'You cannot use this voucher in your country of delivery' => 113,
            'You cannot use this voucher in an empty cart' => 114,
            'You cannot use this voucher anymore (usage limit reached)' => 115,
            'You cannot use this voucher' => 116,
        ];

        foreach ($map as $errorPart => $code) {
            // @todo:
            //     some of these errors have %s placeholders (luckily at the end of the string),
            //      so we just match the start of the error message,
            //     it should be convenient enough for now, but might need improvement later
            if (str_starts_with($message, $errorPart)) {
                return $code;
            }
        }

        throw new RuntimeException(sprintf(
            'Invalid error-code mapping in test. Couldn\'t find the code for message "%s"',
            $message
        ));
    }

    /**
     * This method is temporary. We will get rid of it once all old cart rule creation/edition steps are cleaned up
     *
     * @param string $reference
     *
     * @return CartRule
     */
    private function loadCartRule(string $reference): CartRule
    {
        return new CartRule($this->getCartRuleId($reference));
    }

    private function getCartRuleId(string $cartRuleReference): int
    {
        if ($this->getSharedStorage()->exists($cartRuleReference)) {
            // @todo: This allows applying this step to cart rule which was created with a step using CQRS command and saved to shared storage
            // it is not ideal, but for now it should work, until restrictions are migrated.
            $cartRuleId = $this->getSharedStorage()->get($cartRuleReference);
        } else {
            $this->checkCartRuleWithNameExists($cartRuleReference);
            $cartRuleId = $this->cartRules[$cartRuleReference]->id;
        }

        return $cartRuleId;
    }
}
