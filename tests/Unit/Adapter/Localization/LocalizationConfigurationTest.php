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

namespace Tests\Unit\Adapter\Localization;

use PrestaShop\PrestaShop\Adapter\Localization\LocalizationConfiguration;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Language\LanguageActivatorInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;


use Tests\TestCase\AbstractConfigurationTestCase;

class LocalizationConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;
    private const TAX_ADDRESS_TYPE = 'id_address_invoice';
    private const ECOTAX_TAX_RULES_GROUP_ID = 5;

    /**
     * @var LanguageActivatorInterface
     */
    private $mockLanguageActivator;

    /**
     * @var CurrencyManager
     */
    private $mockCurrencyManager;

    /**
     * @var AdminModuleDataProvider
     */
    private $mockAdminModuleDataProvider;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->createConfigurationMock();
        $this->mockShopConfiguration = $this->createShopContextMock();
        $this->mockMultistoreFeature = $this->createMultistoreFeatureMock();
        $this->mockLanguageActivator = $this->createLanguageActivatorMock();
        $this->mockCurrencyManagerActivator = $this->createCurrencyManagerMock();
        $this->mockAdminModuleDataProvider = $this->createAdminModuleDataProviderMock();
    }

    /**
     * @return LanguageActivatorInterface
     */
    protected function createLanguageActivatorMock()
    {
        return $this->getMockBuilder(LanguageActivatorInterface::class)
            ->getMock();
    }

    /**
     * @return CurrencyManager
     */
    protected function createCurrencyManagerMock()
    {
        return $this->getMockBuilder(CurrencyManager::class)
            ->setMethods(['updateDefaultCurrency'])
            ->getMock();
    }

    /**
     * @return AdminModuleDataProvider
     */
    protected function createAdminModuleDataProviderMock()
    {
        return $this->getMockBuilder(AdminModuleDataProvider::class)
            ->getMock();
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $localizationConfiguration = new LocalizationConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockLanguageActivator,
            $this->mockCurrencyManagerActivator,
            $this->mockAdminModuleDataProvider,
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_LANG_DEFAULT', 1, $shopConstraint, 5],
                    ['PS_DETECT_LANG', false, $shopConstraint, true],
                    ['PS_COUNTRY_DEFAULT', null, $shopConstraint, 3],
                    ['PS_DETECT_COUNTRY', false, $shopConstraint, true],
                    ['PS_CURRENCY_DEFAULT', null, $shopConstraint, 5],
                    ['PS_TIMEZONE', null, $shopConstraint, 'Europe/Paris'],
                ]
            );

        $result = $localizationConfiguration->getConfiguration();
        $this->assertSame(
            [
                'default_language' => 5,
                'detect_language_from_browser' => true,
                'default_country' => 3,
                'detect_country_from_browser' => true,
                'default_currency' => 2,
                'timezone' => 'Europe/Paris',
            ],
            $result
        );
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $localizationConfiguration = new LocalizationConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockLanguageActivator,
            $this->mockCurrencyManagerActivator,
            $this->mockAdminModuleDataProvider,
        );

        $this->expectException($exception);

        $localizationConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, [
                'default_language' => 'wrong_type',
                'detect_language_from_browser' => true,
                'default_country' => 3,
                'detect_country_from_browser' => true,
                'default_currency' => 2,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => 5,
                'detect_language_from_browser' => 'wrong_type',
                'default_country' => 3,
                'detect_country_from_browser' => true,
                'default_currency' => 2,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => 5,
                'detect_language_from_browser' => true,
                'default_country' => 'wrong_type',
                'detect_country_from_browser' => true,
                'default_currency' => 2,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => 5,
                'detect_language_from_browser' => true,
                'default_country' => 3,
                'detect_country_from_browser' => 'wrong_type',
                'default_currency' => 2,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => 5,
                'detect_language_from_browser' => true,
                'default_country' => 3,
                'detect_country_from_browser' => true,
                'default_currency' => 'wrong_type',
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => 5,
                'detect_language_from_browser' => true,
                'default_country' => 3,
                'detect_country_from_browser' => true,
                'default_currency' => 2,
                'timezone' => true,
            ]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $localizationConfiguration = new LocalizationConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->mockLanguageActivator,
            $this->mockCurrencyManagerActivator,
            $this->mockAdminModuleDataProvider,
        );

        $res = $localizationConfiguration->updateConfiguration([
            'default_language' => 5,
            'detect_language_from_browser' => true,
            'default_country' => 3,
            'detect_country_from_browser' => true,
            'default_currency' => 2,
            'timezone' => 'Europe/Paris',
        ]);

        $this->assertSame([], $res);
    }

    /**
     * @return array[]
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}