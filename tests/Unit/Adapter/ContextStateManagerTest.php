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

namespace Tests\Unit\Adapter;

use Cart;
use Country;
use Currency;
use Customer;
use Language;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setCart($this->createContextFieldMock(Cart::class, 69));
        $this->assertEquals(69, $context->cart->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals(42, $context->cart->id);
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }

    public function testControllerState()
    {
        $context = $this->createContextMock([
            'controller' => $this->createLegacyControllerContextMock('AdminProductsController'),
        ]);
        $this->assertEquals('AdminProductsController', $context->controller->controller_name);

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setController($this->createLegacyControllerContextMock('AdminOrdersController'));
        $this->assertEquals('AdminOrdersController', $context->controller->controller_name);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setController($this->createLegacyControllerContextMock('AdminCartsController'));
        $this->assertEquals('AdminCartsController', $context->controller->controller_name);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals('AdminProductsController', $context->controller->controller_name);
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setCountry($this->createContextFieldMock(Country::class, 69));
        $this->assertEquals(69, $context->country->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setCurrency($this->createContextFieldMock(Currency::class, 69));
        $this->assertEquals(69, $context->currency->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setCustomer($this->createContextFieldMock(Customer::class, 69));
        $this->assertEquals(69, $context->customer->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
        $this->assertEquals('test42', $context->getTranslator()->getLocale());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
        $this->assertEquals(51, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());
        $this->assertEquals('test51', $context->getTranslator()->getLocale());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());
        $this->assertEquals('test69', $context->getTranslator()->getLocale());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals(42, $context->language->id);
        $this->assertNull($contextStateManager->getContextFieldsStack());
        $this->assertEquals('test42', $context->getTranslator()->getLocale());
    }

    public function testLocalizationLocaleState()
    {
        $context = $this->createContextMock([
            'currentLocale' => $this->createLocalizationLocaleMock('fr-FR'),
        ]);
        $this->assertEquals('fr-FR', $context->currentLocale->getCode());
        $this->assertEquals('fr-FR', $context->getCurrentLocale()->getCode());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setCurrentLocale($this->createLocalizationLocaleMock('en-US'));
        $this->assertEquals('en-US', $context->currentLocale->getCode());
        $this->assertEquals('en-US', $context->getCurrentLocale()->getCode());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setCurrentLocale($this->createLocalizationLocaleMock('en-GB'));
        $this->assertEquals('en-GB', $context->currentLocale->getCode());
        $this->assertEquals('en-GB', $context->getCurrentLocale()->getCode());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals('fr-FR', $context->currentLocale->getCode());
        $this->assertEquals('fr-FR', $context->getCurrentLocale()->getCode());
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
        /* @phpstan-ignore-next-line */
        $this->assertEquals(51, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
        /* @phpstan-ignore-next-line */
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
            'currentLocale' => $this->createLocalizationLocaleMock('fr-FR'),
            'controller' => $this->createLegacyControllerContextMock('AdminProductsController'),
        ]);
        $this->assertEquals(42, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(42, $context->currency->id);
        $this->assertEquals(42, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('fr-FR', $context->currentLocale->getCode());
        $this->assertEquals('fr-FR', $context->getCurrentLocale()->getCode());
        $this->assertEquals('AdminProductsController', $context->controller->controller_name);

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager
            ->setCart($this->createContextFieldMock(Cart::class, 51))
            ->setCountry($this->createContextFieldMock(Country::class, 51))
            ->setCurrency($this->createContextFieldMock(Currency::class, 51))
            ->setCustomer($this->createContextFieldMock(Customer::class, 51))
            ->setLanguage($this->createContextFieldMock(Language::class, 51))
            ->setCurrentLocale($this->createLocalizationLocaleMock('en-US'))
            ->setController($this->createLegacyControllerContextMock('AdminCartsController'));

        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $this->assertEquals(51, $context->cart->id);
        $this->assertEquals(51, $context->country->id);
        $this->assertEquals(51, $context->currency->id);
        $this->assertEquals(51, $context->customer->id);
        $this->assertEquals(51, $context->language->id);
        $this->assertEquals('en-US', $context->currentLocale->getCode());
        $this->assertEquals('en-US', $context->getCurrentLocale()->getCode());
        $this->assertEquals('AdminCartsController', $context->controller->controller_name);

        $contextStateManager->restorePreviousContext();

        $this->assertEquals(42, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(42, $context->currency->id);
        $this->assertEquals(42, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('fr-FR', $context->currentLocale->getCode());
        $this->assertEquals('fr-FR', $context->getCurrentLocale()->getCode());
        $this->assertEquals('AdminProductsController', $context->controller->controller_name);
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->saveCurrentContext();
        $this->assertCount(2, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
        $this->assertEquals(93, $context->language->id);
        $this->assertCount(2, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals(42, $context->language->id);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        // If several sets have been called, the state returns to the value that was saved
        $contextStateManager->saveCurrentContext();

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 51));
        $this->assertEquals(51, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
            'controller' => $this->createLegacyControllerContextMock('AdminProductsController'),
            'currentLocale' => $this->createLocalizationLocaleMock('fr-FR'),
        ]);
        $this->assertEquals(42, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(42, $context->currency->id);
        $this->assertEquals(42, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('AdminProductsController', $context->controller->controller_name);
        $this->assertEquals('fr-FR', $context->currentLocale->getCode());
        $this->assertEquals('fr-FR', $context->getCurrentLocale()->getCode());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager
            ->setCart($this->createContextFieldMock(Cart::class, 51))
            ->setCurrency($this->createContextFieldMock(Currency::class, 51))
            ->setCustomer($this->createContextFieldMock(Customer::class, 51))
            ->setController($this->createLegacyControllerContextMock('AdminCartsController'))
            ->setCurrentLocale($this->createLocalizationLocaleMock('en-US'))
        ;
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $this->assertEquals(51, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(51, $context->currency->id);
        $this->assertEquals(51, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('AdminCartsController', $context->controller->controller_name);
        $this->assertEquals('en-US', $context->currentLocale->getCode());
        $this->assertEquals('en-US', $context->getCurrentLocale()->getCode());

        $contextStateManager->saveCurrentContext();
        $this->assertCount(2, $contextStateManager->getContextFieldsStack());

        $contextStateManager
            ->setCart($this->createContextFieldMock(Cart::class, 69))
            ->setCurrency($this->createContextFieldMock(Currency::class, 69))
            ->setController($this->createLegacyControllerContextMock('AdminImagesController'))
            ->setCurrentLocale($this->createLocalizationLocaleMock('en-GB'))
        ;

        $this->assertEquals(69, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(69, $context->currency->id);
        $this->assertEquals(51, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('AdminImagesController', $context->controller->controller_name);
        $this->assertEquals('en-GB', $context->currentLocale->getCode());
        $this->assertEquals('en-GB', $context->getCurrentLocale()->getCode());
        $this->assertCount(2, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();

        $this->assertEquals(51, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(51, $context->currency->id);
        $this->assertEquals(51, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('AdminCartsController', $context->controller->controller_name);
        $this->assertEquals('en-US', $context->currentLocale->getCode());
        $this->assertEquals('en-US', $context->getCurrentLocale()->getCode());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();

        $this->assertEquals(42, $context->cart->id);
        $this->assertEquals(42, $context->country->id);
        $this->assertEquals(42, $context->currency->id);
        $this->assertEquals(42, $context->customer->id);
        $this->assertEquals(42, $context->language->id);
        $this->assertEquals('AdminProductsController', $context->controller->controller_name);
        $this->assertEquals('fr-FR', $context->currentLocale->getCode());
        $this->assertEquals('fr-FR', $context->getCurrentLocale()->getCode());
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->saveCurrentContext();

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
        $this->assertEquals(93, $context->language->id);
        $this->assertCount(2, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
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
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 69));
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        // Save point 2
        $contextStateManager->saveCurrentContext();
        $contextStateManager->setLanguage($this->createContextFieldMock(Language::class, 93));
        $this->assertEquals(93, $context->language->id);
        $this->assertCount(2, $contextStateManager->getContextFieldsStack());

        // Back to save point 2
        $contextStateManager->restorePreviousContext();
        $this->assertEquals(69, $context->language->id);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        // Back to save point 1
        $contextStateManager->restorePreviousContext();
        $this->assertEquals(42, $context->language->id);
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }

    private function createLocalizationLocaleMock(string $code): LocaleInterface|MockObject
    {
        $locale = $this->createMock(LocaleInterface::class);
        $locale
            ->method('getCode')
            ->willReturn($code)
        ;

        return $locale;
    }
}
