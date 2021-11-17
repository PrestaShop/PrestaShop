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

namespace Tests\Unit\Adapter {
    use Cart;
    use Country;
    use Currency;
    use Customer;
    use Language;
    use PrestaShop\PrestaShop\Adapter\ContextStateManager;
    use PrestaShop\PrestaShop\Adapter\LegacyContext;
    use Shop;
    use Tests\TestCase\ContextStateTestCase;

    class ContextStateManagerTest extends ContextStateTestCase
    {
        protected $legacyContext;

        protected function setUp(): void
        {
            parent::setUp();
            $this->legacyContext = new LegacyContext();
        }

        public function testCartState()
        {
            $context = $this->createContextMock([
                'cart' => $this->createContextFieldMock(Cart::class, 42),
            ]);
            $this->assertEquals(42, $context->cart->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setCart($this->createContextFieldMock(Cart::class, 51));
            $this->assertEquals(51, $context->cart->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setCart($this->createContextFieldMock(Cart::class, 69));
            $this->assertEquals(69, $context->cart->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->cart->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testCountryState()
        {
            $context = $this->createContextMock([
                'country' => $this->createContextFieldMock(Country::class, 42),
            ]);
            $this->assertEquals(42, $context->country->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setCountry($this->createContextFieldMock(Country::class, 51));
            $this->assertEquals(51, $context->country->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setCountry($this->createContextFieldMock(Country::class, 69));
            $this->assertEquals(69, $context->country->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->country->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testCurrencyState()
        {
            $context = $this->createContextMock([
                'currency' => $this->createContextFieldMock(Currency::class, 42),
            ]);
            $this->assertEquals(42, $context->currency->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setCurrency($this->createContextFieldMock(Currency::class, 51));
            $this->assertEquals(51, $context->currency->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setCurrency($this->createContextFieldMock(Currency::class, 69));
            $this->assertEquals(69, $context->currency->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->currency->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testCustomerState()
        {
            $context = $this->createContextMock([
                'customer' => $this->createContextFieldMock(Customer::class, 42),
            ]);
            $this->assertEquals(42, $context->customer->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setCustomer($this->createContextFieldMock(Customer::class, 51));
            $this->assertEquals(51, $context->customer->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setCustomer($this->createContextFieldMock(Customer::class, 69));
            $this->assertEquals(69, $context->customer->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->customer->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testLanguageState()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testShopState()
        {
            $context = $this->createContextMock([
                'shop' => $this->createContextFieldMock(Shop::class, 42),
            ]);
            Shop::setContext(Shop::CONTEXT_SHOP, 42);
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(42, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 51));
            $this->assertEquals(51, $context->shop->id);
            $this->assertEquals(51, Shop::getContextShopID());
            $this->assertEquals(51, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 69));
            $this->assertEquals(69, $context->shop->id);
            $this->assertEquals(69, Shop::getContextShopID());
            $this->assertEquals(69, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(42, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(42, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testShopStateAll()
        {
            $context = $this->createContextMock([
                'shop' => $this->createContextFieldMock(Shop::class, 42),
            ]);
            Shop::setContext(Shop::CONTEXT_ALL);
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(null, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 51));
            $this->assertEquals(51, $context->shop->id);
            $this->assertEquals(51, Shop::getContextShopID());
            $this->assertEquals(51, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 69));
            $this->assertEquals(69, $context->shop->id);
            $this->assertEquals(69, Shop::getContextShopID());
            $this->assertEquals(69, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(null, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(null, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testShopStateGroup()
        {
            $context = $this->createContextMock([
                'shop' => $this->createContextFieldMock(Shop::class, 42),
            ]);
            Shop::setContext(Shop::CONTEXT_GROUP, 42);
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 51));
            $this->assertEquals(51, $context->shop->id);
            $this->assertEquals(51, Shop::getContextShopID());
            $this->assertEquals(51, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 69));
            $this->assertEquals(69, $context->shop->id);
            $this->assertEquals(69, Shop::getContextShopID());
            $this->assertEquals(69, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testNullField()
        {
            $context = $this->createContextMock([
                'language' => null,
            ]);
            $this->assertNull($context->language);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertNull($context->language);
        }

        public function testMultipleFields()
        {
            $context = $this->createContextMock([
                'cart' => $this->createContextFieldMock(Cart::class, 42),
                'country' => $this->createContextFieldMock(Country::class, 42),
                'currency' => $this->createContextFieldMock(Currency::class, 42),
                'customer' => $this->createContextFieldMock(Customer::class, 42),
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(42, $context->currency->id);
            $this->assertEquals(42, $context->customer->id);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager
                ->setCart($this->createContextFieldMock(Cart::class, 51))
                ->setCountry($this->createContextFieldMock(Country::class, 51))
                ->setCurrency($this->createContextFieldMock(Currency::class, 51))
                ->setCustomer($this->createContextFieldMock(Customer::class, 51))
                ->setLanguage($this->createContextFieldMock(Language::class, 51))
            ;
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $this->assertEquals(51, $context->cart->id);
            $this->assertEquals(51, $context->country->id);
            $this->assertEquals(51, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(51, $context->language->id);

            $contextStateManager->restorePreviousContext();

            $this->assertEquals(42, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(42, $context->currency->id);
            $this->assertEquals(42, $context->customer->id);
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testSavedContexts()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->saveCurrentContext();
            $this->assertCount(2, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);
            $this->assertCount(2, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            // If several sets have been called, the state returns to the value that was saved
            $contextStateManager->saveCurrentContext();

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testMultipleSavedContextFields()
        {
            $context = $this->createContextMock([
                'cart' => $this->createContextFieldMock(Cart::class, 42),
                'country' => $this->createContextFieldMock(Country::class, 42),
                'currency' => $this->createContextFieldMock(Currency::class, 42),
                'customer' => $this->createContextFieldMock(Customer::class, 42),
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(42, $context->currency->id);
            $this->assertEquals(42, $context->customer->id);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager
                ->setCart($this->createContextFieldMock(Cart::class, 51))
                ->setCurrency($this->createContextFieldMock(Currency::class, 51))
                ->setCustomer($this->createContextFieldMock(Customer::class, 51))
            ;
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $this->assertEquals(51, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(51, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager->saveCurrentContext();
            $this->assertCount(2, $contextStateManager->getContextFieldsStack());

            $contextStateManager
                ->setCart($this->createContextFieldMock(Cart::class, 69))
                ->setCurrency($this->createContextFieldMock(Currency::class, 69))
            ;

            $this->assertEquals(69, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(69, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(42, $context->language->id);
            $this->assertCount(2, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();

            $this->assertEquals(51, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(51, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(42, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();

            $this->assertEquals(42, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(42, $context->currency->id);
            $this->assertEquals(42, $context->customer->id);
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testTooManyRestore()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->saveCurrentContext();

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);
            $this->assertCount(2, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            // This removes all persisted states, we keep null when nothing is saved
            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }

        public function testSavedContextsFirst()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($this->legacyContext);
            $this->assertNull($contextStateManager->getContextFieldsStack());

            // Save point 1
            $contextStateManager->saveCurrentContext();
            // Nothing saved yet so no reason to init the stack
            $this->assertNull($contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            // Save point 2
            $contextStateManager->saveCurrentContext();
            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);
            $this->assertCount(2, $contextStateManager->getContextFieldsStack());

            // Back to save point 2
            $contextStateManager->restorePreviousContext();
            $this->assertEquals(69, $context->language->id);
            $this->assertCount(1, $contextStateManager->getContextFieldsStack());

            // Back to save point 1
            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
            $this->assertNull($contextStateManager->getContextFieldsStack());
        }
    }
}

namespace {
    class Shop extends ShopCore
    {
        public static function getGroupFromShop($shop_id, $as_id = true)
        {
            return $shop_id;
        }
    }
}
