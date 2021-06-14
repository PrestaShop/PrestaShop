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

namespace Tests\Integration\Classes;

use CartRule;
use Customer;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Install\DatabaseDump;
use Tools;

class CartRuleTest extends TestCase
{
    /**
     * @var Customer
     */
    public $dummyCustomer;

    /**
     * @var CartRule
     */
    public $dummyCartRule;

    /**
     * @var Configuration
     */
    public $configuration;

    public static function setUpBeforeClass(): void
    {
        DatabaseDump::restoreDb();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummyCustomer = $this->createDummyCustomer();
        $this->configuration = new Configuration();
    }

    public function testGetCartRulesForCustomer(): void
    {
        $this->dummyCartRule = $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getCustomerCartRules(
            $this->configuration->get('PS_LANG_DEFAULT'),
            $this->dummyCustomer->id
        );

        $this->assertEquals(1, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerEvenDisabled(): void
    {
        $this->dummyCartRule = $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getCustomerCartRules(
            $this->configuration->get('PS_LANG_DEFAULT'),
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
        $this->dummyCartRule = $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(1, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethodEvenDisabled(): void
    {
        $this->dummyCartRule = $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(1, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethodBothEnabledAndDisabled()
    {
        $this->createDummyCartRule(false, (int) $this->dummyCustomer->id);
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);
        $this->createDummyCartRule(true, (int) $this->dummyCustomer->id);

        $customerCartRules = CartRule::getAllCustomerCartRules(
            (int) $this->dummyCustomer->id
        );

        $this->assertEquals(3, count($customerCartRules));
    }

    public function testGetAllCartRulesForCustomerWithDedicatedMethodBothEnabledAndDisabledWithOtherCustomerCartRulesAvailable()
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

    /**
     * @param bool $active
     * @param int $customerId
     *
     * @return CartRule
     */
    public function createDummyCartRule(
        bool $active = true,
        int $customerId
    ) {
        $randomNumber = rand(999, 9999);
        $cart_rule = new CartRule();
        $cart_rule->code = 'TEST_CART_RULE_' . $randomNumber;
        $cart_rule->name = [
            $this->configuration->get('PS_LANG_DEFAULT') => 'Test Cart Rule #' . $randomNumber,
        ];
        $cart_rule->id_customer = (int) $customerId;
        $cart_rule->free_shipping = true;
        $cart_rule->quantity = 1;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->minimum_amount_currency = $this->configuration->get('PS_CURRENCY_DEFAULT');
        $cart_rule->reduction_currency = $this->configuration->get('PS_CURRENCY_DEFAULT');
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
        $customer->email = 'pub+jenna+' . rand(0, 99) . '@prestashop.com';
        $customer->passwd = Tools::hash('prestashop');
        $customer->save();

        return $customer;
    }
}
