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
    use Context;
    use Country;
    use Currency;
    use Customer;
    use Language;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use PrestaShop\PrestaShop\Adapter\ContextStateManager;
    use Shop;

    class ContextStateManagerTest extends TestCase
    {
        public function testCartState()
        {
            $context = $this->createContextMock([
                'cart' => $this->createContextFieldMock(Cart::class, 42),
            ]);
            $this->assertEquals(42, $context->cart->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setCart($this->createContextFieldMock(Cart::class, 51));
            $this->assertEquals(51, $context->cart->id);

            $contextStateManager->setCart($this->createContextFieldMock(Cart::class, 69));
            $this->assertEquals(69, $context->cart->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->cart->id);
        }

        public function testCountryState()
        {
            $context = $this->createContextMock([
                'country' => $this->createContextFieldMock(Country::class, 42),
            ]);
            $this->assertEquals(42, $context->country->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setCountry($this->createContextFieldMock(Country::class, 51));
            $this->assertEquals(51, $context->country->id);

            $contextStateManager->setCountry($this->createContextFieldMock(Country::class, 69));
            $this->assertEquals(69, $context->country->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->country->id);
        }

        public function testCurrencyState()
        {
            $context = $this->createContextMock([
                'currency' => $this->createContextFieldMock(Currency::class, 42),
            ]);
            $this->assertEquals(42, $context->currency->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setCurrency($this->createContextFieldMock(Currency::class, 51));
            $this->assertEquals(51, $context->currency->id);

            $contextStateManager->setCurrency($this->createContextFieldMock(Currency::class, 69));
            $this->assertEquals(69, $context->currency->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->currency->id);
        }

        public function testCustomerState()
        {
            $context = $this->createContextMock([
                'customer' => $this->createContextFieldMock(Customer::class, 42),
            ]);
            $this->assertEquals(42, $context->customer->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setCustomer($this->createContextFieldMock(Customer::class, 51));
            $this->assertEquals(51, $context->customer->id);

            $contextStateManager->setCustomer($this->createContextFieldMock(Customer::class, 69));
            $this->assertEquals(69, $context->customer->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->customer->id);
        }

        public function testLanguageState()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
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

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 51));
            $this->assertEquals(51, $context->shop->id);
            $this->assertEquals(51, Shop::getContextShopID());
            $this->assertEquals(51, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 69));
            $this->assertEquals(69, $context->shop->id);
            $this->assertEquals(69, Shop::getContextShopID());
            $this->assertEquals(69, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(42, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(42, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
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

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 51));
            $this->assertEquals(51, $context->shop->id);
            $this->assertEquals(51, Shop::getContextShopID());
            $this->assertEquals(51, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 69));
            $this->assertEquals(69, $context->shop->id);
            $this->assertEquals(69, Shop::getContextShopID());
            $this->assertEquals(69, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(null, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(null, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());
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

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 51));
            $this->assertEquals(51, $context->shop->id);
            $this->assertEquals(51, Shop::getContextShopID());
            $this->assertEquals(51, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->setShop($this->createContextFieldMock(Shop::class, 69));
            $this->assertEquals(69, $context->shop->id);
            $this->assertEquals(69, Shop::getContextShopID());
            $this->assertEquals(69, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->shop->id);
            $this->assertEquals(null, Shop::getContextShopID());
            $this->assertEquals(42, Shop::getContextShopGroupID());
            $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());
        }

        public function testNullField()
        {
            $context = $this->createContextMock([
                'language' => null,
            ]);
            $this->assertNull($context->language);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);

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

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager
                ->setCart($this->createContextFieldMock(Cart::class, 51))
                ->setCountry($this->createContextFieldMock(Country::class, 51))
                ->setCurrency($this->createContextFieldMock(Currency::class, 51))
                ->setCustomer($this->createContextFieldMock(Customer::class, 51))
                ->setLanguage($this->createContextFieldMock(Language::class, 51))
            ;

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
        }

        public function testSavedContexts()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);

            $contextStateManager->saveCurrentContext();

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(69, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);

            // If several sets have been called, the state returns to the value that was saved
            $contextStateManager->saveCurrentContext();

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
        }

        public function testMultipleSavedContextFieds()
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

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager
                ->setCart($this->createContextFieldMock(Cart::class, 51))
                ->setCurrency($this->createContextFieldMock(Currency::class, 51))
                ->setCustomer($this->createContextFieldMock(Customer::class, 51))
            ;

            $this->assertEquals(51, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(51, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager->saveCurrentContext();

            $contextStateManager
                ->setCart($this->createContextFieldMock(Cart::class, 69))
                ->setCurrency($this->createContextFieldMock(Currency::class, 69))
            ;

            $this->assertEquals(69, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(69, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager->restorePreviousContext();

            $this->assertEquals(51, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(51, $context->currency->id);
            $this->assertEquals(51, $context->customer->id);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager->restorePreviousContext();

            $this->assertEquals(42, $context->cart->id);
            $this->assertEquals(42, $context->country->id);
            $this->assertEquals(42, $context->currency->id);
            $this->assertEquals(42, $context->customer->id);
            $this->assertEquals(42, $context->language->id);
        }

        public function testTooManyRestore()
        {
            $context = $this->createContextMock([
                'language' => $this->createContextFieldMock(Language::class, 42),
            ]);
            $this->assertEquals(42, $context->language->id);

            $contextStateManager = new ContextStateManager($context);
            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
            $this->assertEquals(51, $context->language->id);

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
            $this->assertEquals(69, $context->language->id);

            $contextStateManager->saveCurrentContext();

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(69, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);

            // This removes all persisted states, but we always re-init one for future calls
            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);

            $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
            $this->assertEquals(93, $context->language->id);

            $contextStateManager->restorePreviousContext();
            $this->assertEquals(42, $context->language->id);
        }

        /**
         * @param string $className
         * @param int $objectId
         *
         * @return MockObject|Cart|Country|Currency|Customer|Language|Shop
         */
        private function createContextFieldMock(string $className, int $objectId)
        {
            $contextField = $this->getMockBuilder($className)
                ->disableOriginalConstructor()
                ->getMock();

            $contextField->id = $objectId;

            return $contextField;
        }

        /**
         * @param array $contextFields
         *
         * @return MockObject|Context
         */
        private function createContextMock(array $contextFields)
        {
            $contextMock = $this->getMockBuilder(Context::class)
                ->disableOriginalConstructor()
                ->getMock();

            foreach ($contextFields as $fieldName => $contextValue) {
                $contextMock->$fieldName = $contextValue;
            }

            return $contextMock;
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
