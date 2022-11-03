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

namespace Tests\Integration\Classes;

use CartRule;
use Customer;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Resources\DatabaseDump;
use Tools;

class CartRuleTest extends TestCase
{
    /**
     * @var Customer
     */
    protected $dummyCustomer;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var int
     */
    protected $defaultLanguageId;

    public static function setUpBeforeClass(): void
    {
        DatabaseDump::restoreTables(
            [
                'cart_rule',
                'cart_rule_lang',
            ]
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummyCustomer = $this->createDummyCustomer();
        $this->configuration = new Configuration();
        $this->defaultLanguageId = $this->configuration->get('PS_LANG_DEFAULT', null, ShopConstraint::allShops());
    }

    public function testGetCartRulesForCustomer(): void
    {
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getCustomerCartRules(
            $this->defaultLanguageId,
            $this->dummyCustomer->id
        );

        $this->assertEquals(1, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerEvenDisabled(): void
    {
        $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getCustomerCartRules(
            $this->defaultLanguageId,
            $this->dummyCustomer->id,
            true
        );

        // We assert 0 because 'active' flag does not work in "CartRule::getCustomerCartRules"
        // because of CartRule::isFeatureActive and one additional check
        // which doesn't not work if we have only 1 customer rule or all of them are disabled
        // see https://github.com/PrestaShop/PrestaShop/issues/21556 for more details
        $this->assertEquals(0, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethod(): void
    {
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(1, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethodEvenDisabled(): void
    {
        $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(1, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethodBothEnabledAndDisabled(): void
    {
        $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(3, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethodBothEnabledAndDisabledWithOtherCustomerCartRulesAvailable(): void
    {
        $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $differentCustomer = $this->createDummyCustomer();
        $yetAnotherCustomer = $this->createDummyCustomer();

        // Just to make sure that our CartRule::getAlCustomerCartRules works well
        $this->createDummyCartRule(false, (int) $differentCustomer->id);
        $this->createDummyCartRule(true, (int) $yetAnotherCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $yetAnotherCustomerCartRules = CartRule::getAllCustomerCartRules(
            (int) $yetAnotherCustomer->id
        );

        $this->assertEquals(2, count($customerCartRules));
        $this->assertEquals(1, count($yetAnotherCustomerCartRules));
    }

    public function testGetAllCartRulesWithGlobalCartRulesAvailable(): void
    {
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id, false);
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(2, count($customerCartRules));
    }

    /**
     * Test sorting of the CartRules
     *
     * Target sort order:
     * - Active CartRules for customer
     * - Active CartRules for everyone
     * - Inactive CartRules for customer
     * - Inactive CartRules for everyone
     */
    public function testSortingOfTheAvailableCustomerCartRules(): void
    {
        // inactive customer's rule
        $inactiveCustomerRule = $this->createDummyCartRule(false, (int) $this->dummyCustomer->id, false);

        // inactive global rule
        $inactiveGlobalRule = $this->createDummyCartRule(false, 0, false);

        // active global rule
        $activeGlobalRule = $this->createDummyCartRule(true, 0, false);

        // active customer's rule
        $activeCustomerRule = $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(
            [
                $activeCustomerRule->id,
                $activeGlobalRule->id,
                $inactiveCustomerRule->id,
                $inactiveGlobalRule->id,
            ],
            array_column($customerCartRules, 'id_cart_rule')
        );
    }

    /**
     * @param bool $active
     * @param int $customerId
     * @param bool $code
     *
     * @return CartRule
     */
    public function createDummyCartRule(
        bool $active,
        int $customerId,
        bool $code = true
    ): CartRule {
        $randomNumber = rand(999, 9999);
        $cart_rule = new CartRule();
        $cart_rule->code = $code ? 'TEST_CART_RULE_' . $randomNumber : '';
        $cart_rule->name = [
            $this->defaultLanguageId => 'Test Cart Rule #' . $randomNumber,
        ];
        $cart_rule->id_customer = $customerId;
        $cart_rule->free_shipping = true;
        $cart_rule->quantity = 1;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->minimum_amount_currency = $this->configuration->get('PS_CURRENCY_DEFAULT', null, ShopConstraint::allShops());
        $cart_rule->reduction_currency = $this->configuration->get('PS_CURRENCY_DEFAULT', null, ShopConstraint::allShops());
        $cart_rule->date_from = date('Y-m-d H:i:s', time());
        $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
        $cart_rule->active = $active;
        $cart_rule->add();

        return $cart_rule;
    }

    /**
     * @return Customer
     */
    public function createDummyCustomer(): Customer
    {
        $customer = new Customer();
        $customer->firstname = 'Jenna';
        $customer->lastname = 'Doe';
        $customer->email = 'pub+' . uniqid() . '@prestashop.com';
        $customer->passwd = Tools::hash('prestashop');
        $customer->save();

        return $customer;
    }
}
