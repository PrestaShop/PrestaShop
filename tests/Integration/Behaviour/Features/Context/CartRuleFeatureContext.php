<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Cache;
use CartRule;
use Context;
use DateInterval;
use DateTime;
use Db;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleValidityException;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\DatabaseDump;
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
     * @var LegacyProductFeatureContext
     */
    protected $productFeatureContext;

    /**
     * @var CustomerFeatureContext
     */
    protected $customerFeatureContext;

    /**
     * @var CategoryFeatureContext
     */
    protected $categoryFeatureContext;

    /**
     * @BeforeScenario @restore-cart-rules-before-scenario
     *
     * @AfterScenario @restore-cart-rules-after-scenario
     *
     * @return void
     */
    public static function restoreCartRules(): void
    {
        DatabaseDump::restoreMatchingTables('^cart_rule.*^');
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        /** @var LegacyProductFeatureContext $productFeatureContext */
        $productFeatureContext = $environment->getContext(LegacyProductFeatureContext::class);
        /** @var CustomerFeatureContext $customerFeatureContext */
        $customerFeatureContext = $environment->getContext(CustomerFeatureContext::class);
        /** @var CategoryFeatureContext $categoryFeatureContext */
        $categoryFeatureContext = $environment->getContext(CategoryFeatureContext::class);

        $this->productFeatureContext = $productFeatureContext;
        $this->customerFeatureContext = $customerFeatureContext;
        $this->categoryFeatureContext = $categoryFeatureContext;
    }

    /**
     * @When there is a cart rule :cartRuleReference with following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $node
     */
    public function createCartRuleIfNotExists(string $cartRuleReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);

        // If reference exists check that the cart rule matches the expected values
        $cartRuleId = $this->getSharedStorage()->exists($cartRuleReference) ? $this->getSharedStorage()->get($cartRuleReference) : 0;
        $cartRule = new CartRule($cartRuleId);

        // If no ID stored as reference or the cart rule is not in DB we create it
        if (!$cartRuleId || !((int) $cartRule->id)) {
            $cartRule = new CartRule();
            $cartRule->name = $data['name'];
            $cartRule->description = $data['description'] ?? '';
            $cartRule->reduction_percent = $data['discount_percentage'] ?? 0;
            $cartRule->reduction_amount = $data['discount_amount'] ?? 0;
            if (isset($data['discount_currency'])) {
                $cartRule->reduction_currency = $this->referenceToId($data['discount_currency']);
            }
            $cartRule->reduction_tax = isset($data['discount_includes_tax']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['discount_includes_tax']);
            $cartRule->priority = $data['priority'] ?? 1;
            $cartRule->quantity = $data['total_quantity'] ?? $data['quantity'] ?? 1;
            $cartRule->quantity_per_user = $data['quantity_per_user'] ?? 1;
            $cartRule->code = $data['code'] ?? '';
            $cartRule->free_shipping = isset($data['free_shipping']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']);
            $cartRule->partial_use = !isset($data['allow_partial_use']) || PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']);
            $cartRule->gift_product = isset($data['gift_product']) ? $this->referenceToId($data['gift_product']) : 0;
            $cartRule->minimum_amount = isset($data['minimum_amount']) ? (float) $data['minimum_amount'] : 0;
            $cartRule->minimum_amount_currency = isset($data['minimum_amount_currency']) ? $this->referenceToId($data['minimum_amount_currency']) : 0;
            $cartRule->minimum_amount_tax = isset($data['minimum_amount_tax_included']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']);
            $cartRule->minimum_amount_shipping = isset($data['minimum_amount_shipping_included']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included']);
            $cartRule->reduction_exclude_special = isset($data['apply_to_discounted_products']) && !PrimitiveUtils::castStringBooleanIntoBoolean($data['apply_to_discounted_products']);
            if (isset($data['valid_from'])) {
                $cartRule->date_from = $data['valid_from'];
            } else {
                $now = new DateTime();
                // sub 1s to avoid bad comparisons with strictly greater than
                $now->sub(new DateInterval('P2D'));
                $cartRule->date_from = $now->format('Y-m-d H:i:s');
            }
            if (isset($data['valid_to'])) {
                $cartRule->date_to = $data['valid_to'];
            } else {
                $now = new DateTime();
                $now->add(new DateInterval('P1Y'));
                $cartRule->date_to = $now->format('Y-m-d H:i:s');
            }
            $cartRule->active = !isset($data['active']) || PrimitiveUtils::castStringBooleanIntoBoolean($data['active']);
            if (isset($data['discount_product'])) {
                $cartRule->reduction_product = $this->referenceToId($data['discount_product']);
            } elseif (isset($data['cheapest_product'])) {
                $cheapestProduct = PrimitiveUtils::castStringBooleanIntoBoolean($data['cheapest_product']);
                if ($cheapestProduct) {
                    $cartRule->reduction_product = DiscountSettings::CHEAPEST_PRODUCT;
                }
            } else {
                $cartRule->reduction_product = DiscountSettings::PRODUCTS_TOTAL;
            }

            $cartRule->add();
            $this->getSharedStorage()->set($cartRuleReference, (int) $cartRule->id);
            if (!empty($cartRule->code)) {
                $this->getSharedStorage()->set($cartRule->code, (int) $cartRule->id);
            }

            if (isset($data['carriers'])) {
                $carriersIds = $this->referencesToIds($data['carriers']);
                $this->setCartRuleCarriers($cartRule, $carriersIds);
            }
            if (isset($data['countries'])) {
                $countryIds = $this->referencesToIds($data['countries']);
                $this->setCartRuleCountries($cartRule, $countryIds);
            }
        } else {
            Assert::assertEquals($cartRule->name, $data['name'], 'Unexpected cart rule name');
            Assert::assertEquals($cartRule->description, $data['description'] ?? '', 'Unexpected cart rule description');
            Assert::assertEquals($cartRule->reduction_percent, (float) ($data['discount_percentage'] ?? 0), 'Unexpected cart rule reduction percent');
            Assert::assertEquals($cartRule->reduction_amount, (float) ($data['discount_amount'] ?? 0), 'Unexpected cart rule amount');
            if (isset($data['discount_currency'])) {
                Assert::assertEquals($cartRule->reduction_currency, $this->referenceToId($data['discount_currency']), 'Unexpected cart rule reduction currency');
            }
            Assert::assertEquals($cartRule->reduction_tax, isset($data['discount_includes_tax']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['discount_includes_tax']), 'Unexpected cart rule reduction tax');
            Assert::assertEquals($cartRule->priority, $data['priority'] ?? 1, 'Unexpected cart rule priority');
            if (isset($data['total_quantity']) || isset($data['quantity'])) {
                Assert::assertEquals($cartRule->quantity, $data['total_quantity'] ?? $data['quantity'] ?? 1, 'Unexpected cart rule quantity');
            }
            Assert::assertEquals($cartRule->quantity_per_user, $data['quantity_per_user'] ?? 1, 'Unexpected cart rule quantity per user');
            Assert::assertEquals($cartRule->code, $data['code'] ?? '', 'Unexpected cart rule code');
            Assert::assertEquals($cartRule->free_shipping, isset($data['free_shipping']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']), 'Unexpected cart rule free shipping');
            Assert::assertEquals($cartRule->partial_use, !isset($data['allow_partial_use']) || PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']), 'Unexpected cart rule partial use');
            Assert::assertEquals($cartRule->gift_product, isset($data['gift_product']) ? $this->referenceToId($data['gift_product']) : 0, 'Unexpected cart rule gift product');
            Assert::assertEquals($cartRule->minimum_amount, isset($data['minimum_amount']) ? (float) $data['minimum_amount'] : 0, 'Unexpected cart rule minimum amount');
            Assert::assertEquals($cartRule->minimum_amount_currency, isset($data['minimum_amount_currency']) ? $this->referenceToId($data['minimum_amount_currency']) : 0, 'Unexpected cart rule minimum amount currency');
            Assert::assertEquals($cartRule->minimum_amount_tax, isset($data['minimum_amount_tax_included']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']), 'Unexpected cart rule minimum amount tax include');
            Assert::assertEquals($cartRule->minimum_amount_shipping, isset($data['minimum_amount_shipping_included']) && PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included']), 'Unexpected cart rule minimum amount shipping included');
            Assert::assertEquals($cartRule->reduction_exclude_special, isset($data['apply_to_discounted_products']) && !PrimitiveUtils::castStringBooleanIntoBoolean($data['apply_to_discounted_products']), 'Unexpected cart rule applying to discounted products');
            Assert::assertEquals($cartRule->active, !isset($data['active']) || PrimitiveUtils::castStringBooleanIntoBoolean($data['active']), 'Unexpected cart rule active');
            if (isset($data['valid_from'])) {
                Assert::assertEquals($cartRule->date_from, $data['valid_from'], 'Unexpected cart rule valid from');
            }
            if (isset($data['valid_to'])) {
                Assert::assertEquals($cartRule->date_to, $data['valid_to'], 'Unexpected cart rule valid to');
            }
            if (isset($data['discount_product'])) {
                Assert::assertEquals($cartRule->reduction_product, $this->referenceToId($data['discount_product']), 'Unexpected cart rule discount product');
            } elseif (isset($data['cheapest_product'])) {
                $cheapestProduct = PrimitiveUtils::castStringBooleanIntoBoolean($data['cheapest_product']);
                if ($cheapestProduct) {
                    Assert::assertEquals($cartRule->reduction_product, DiscountSettings::CHEAPEST_PRODUCT, 'Unexpected cheapest product');
                } else {
                    Assert::assertEquals($cartRule->reduction_product, DiscountSettings::PRODUCTS_TOTAL, 'Unexpected cheapest product');
                }
            }
            if (isset($data['carriers'])) {
                $expectedCarriersIds = $this->referencesToIds($data['carriers']);
                $repository = CommonFeatureContext::getContainer()->get(DiscountRepository::class);
                $carrierIds = $repository->getCarriersIds(new DiscountId((int) $cartRule->id));
                Assert::assertEquals($expectedCarriersIds, $carrierIds, 'Unexpected carrier ids');
            }
            if (isset($data['countries'])) {
                $expectedCountryIds = $this->referencesToIds($data['countries']);
                $repository = CommonFeatureContext::getContainer()->get(DiscountRepository::class);
                $countryIds = $repository->getCountriesIds(new DiscountId((int) $cartRule->id));
                Assert::assertEquals($expectedCountryIds, $countryIds, 'Unexpected country ids');
            }
        }

        // Check there is no field that was not handled by this step
        unset($data['name'], $data['description'], $data['discount_percentage'], $data['discount_amount'], $data['discount_currency'], $data['discount_includes_tax']);
        unset($data['priority'], $data['total_quantity'], $data['quantity_per_user'], $data['code'], $data['free_shipping'], $data['allow_partial_use'], $data['active']);
        unset($data['valid_from'], $data['valid_to'], $data['apply_to_discounted_products'], $data['gift_product']);
        unset($data['minimum_amount'], $data['minimum_amount_currency'], $data['minimum_amount_tax_included'], $data['minimum_amount_shipping_included'], $data['discount_product']);
        unset($data['quantity'], $data['cheapest_product'], $data['carriers'], $data['countries']);
        if (!empty($data)) {
            throw new RuntimeException(sprintf('There are fields that were not handled in cart rule creation: %s', implode(',', array_keys($data))));
        }
    }

    /**
     * @When I update quantity for cart rule :cartRuleReference to :quantity
     *
     * @param string $cartRuleReference
     * @param int $quantity
     *
     * @return void
     */
    public function setCartRuleQuantity(string $cartRuleReference, int $quantity): void
    {
        $cartRule = new CartRule($this->referenceToId($cartRuleReference));
        $cartRule->quantity = $quantity;
        $cartRule->save();
    }

    /**
     * @When I restrict following carriers :carrierReferences for cart rule :cartRuleReference
     *
     * @param string $cartRuleReference
     * @param string $carrierReferences
     *
     * @return void
     */
    public function setRestrictedCarriers(string $cartRuleReference, string $carrierReferences): void
    {
        $cartRule = new CartRule($this->referenceToId($cartRuleReference));
        $this->setCartRuleCarriers($cartRule, $this->referencesToIds($carrierReferences));
    }

    protected function setCartRuleCarriers(CartRule $cartRule, array $carrierIds): void
    {
        // First clear existing associations
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_carrier` WHERE `id_cart_rule` = ' . (int) $cartRule->id
        );

        // Now add the associations
        if (!empty($carrierIds)) {
            foreach ($carrierIds as $carrierId) {
                Db::getInstance()->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_carrier` (`id_cart_rule`, `id_carrier`) ' .
                    'VALUES (' . (int) $cartRule->id . ', ' . (int) $carrierId . ')'
                );
            }
        }
        $cartRule->carrier_restriction = !empty($carrierIds);
        $cartRule->save();
    }

    protected function setCartRuleCountries(CartRule $cartRule, array $countryIds): void
    {
        // First clear existing associations
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_country` WHERE `id_cart_rule` = ' . (int) $cartRule->id
        );

        // Now add the associations
        if (!empty($countryIds)) {
            foreach ($countryIds as $countryId) {
                Db::getInstance()->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_country` (`id_cart_rule`, `id_country`) ' .
                    'VALUES (' . (int) $cartRule->id . ', ' . (int) $countryId . ')'
                );
            }
        }
        $cartRule->country_restriction = !empty($countryIds);
        $cartRule->save();
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
        // @todo: product restriction and reduction_product are 2 different features. Should they really be mixed in here to one?
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
        $result = $cartRule->checkValidity(Context::getContext(), false, false);

        if (!$result) {
            throw new RuntimeException(sprintf('Expects true, got %s instead', $result));
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
     * @Then I should get cart rule validation error
     *
     * @return void
     */
    public function assertGenericCartRuleValidationError(): void
    {
        $this->assertLastErrorIs(CartRuleValidityException::class);
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $cartRuleCount, $result));
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedCount, count($cartRules)));
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
            throw new Exception(sprintf('Undefined cartRule on position #%s', $position - 1));
        }
        $cartRule = new CartRule($cartRules[$position - 1]['id_cart_rule']);
        if ($expectedValue != $cartRule->reduction_amount) {
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedValue, $cartRule->reduction_amount));
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
            throw new RuntimeException(sprintf('Expects "usage limit reached" error message, got %s instead', $result));
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
