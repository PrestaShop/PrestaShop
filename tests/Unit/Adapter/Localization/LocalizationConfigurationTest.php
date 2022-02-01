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

use PrestaShop\PrestaShop\Adapter\Currency\CurrencyManager;
use PrestaShop\PrestaShop\Adapter\Localization\LocalizationConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Language\LanguageActivatorInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class LocalizationConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;
    private const COUNTRY_DEFAULT = 3;
    private const LANG_DEFAULT = 5;
    private const CURRENCY_DEFAULT = 2;

    /**
     * @var LanguageActivatorInterface
     */
    private $mockLanguageActivator;

    /**
     * @var CurrencyManager
     */
    private $mockCurrencyManager;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->createConfigurationMock();
        $this->mockShopConfiguration = $this->createShopContextMock();
        $this->mockMultistoreFeature = $this->createMultistoreFeatureMock();
        $this->mockLanguageActivator = $this->createLanguageActivatorMock();
        $this->mockCurrencyManager = $this->createCurrencyManagerMock();
    }

    /**
     * @return LanguageActivatorInterface
     */
    protected function createLanguageActivatorMock(): LanguageActivatorInterface
    {
        return $this->getMockBuilder(LanguageActivatorInterface::class)
            ->getMock();
    }

    /**
     * @return CurrencyManager
     */
    protected function createCurrencyManagerMock(): CurrencyManager
    {
        return $this->getMockBuilder(CurrencyManager::class)
            ->setMethods(['updateDefaultCurrency'])
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
            $this->mockCurrencyManager
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_LANG_DEFAULT', 1, $shopConstraint, self::LANG_DEFAULT],
                    ['PS_DETECT_LANG', false, $shopConstraint, true],
                    ['PS_COUNTRY_DEFAULT', null, $shopConstraint, self::COUNTRY_DEFAULT],
                    ['PS_DETECT_COUNTRY', false, $shopConstraint, true],
                    ['PS_CURRENCY_DEFAULT', null, $shopConstraint, self::CURRENCY_DEFAULT],
                    ['PS_TIMEZONE', null, $shopConstraint, 'Europe/Paris'],
                ]
            );

        $result = $localizationConfiguration->getConfiguration();
        $this->assertSame(
            [
                'default_language' => self::LANG_DEFAULT,
                'detect_language_from_browser' => true,
                'default_country' => self::COUNTRY_DEFAULT,
                'detect_country_from_browser' => true,
                'default_currency' => self::CURRENCY_DEFAULT,
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
            $this->mockCurrencyManager
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
                'default_country' => self::COUNTRY_DEFAULT,
                'detect_country_from_browser' => true,
                'default_currency' => self::CURRENCY_DEFAULT,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => self::LANG_DEFAULT,
                'detect_language_from_browser' => 'wrong_type',
                'default_country' => self::COUNTRY_DEFAULT,
                'detect_country_from_browser' => true,
                'default_currency' => self::CURRENCY_DEFAULT,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => self::LANG_DEFAULT,
                'detect_language_from_browser' => true,
                'default_country' => 'wrong_type',
                'detect_country_from_browser' => true,
                'default_currency' => self::CURRENCY_DEFAULT,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => self::LANG_DEFAULT,
                'detect_language_from_browser' => true,
                'default_country' => self::COUNTRY_DEFAULT,
                'detect_country_from_browser' => 'wrong_type',
                'default_currency' => self::CURRENCY_DEFAULT,
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => self::LANG_DEFAULT,
                'detect_language_from_browser' => true,
                'default_country' => self::COUNTRY_DEFAULT,
                'detect_country_from_browser' => true,
                'default_currency' => 'wrong_type',
                'timezone' => 'Europe/Paris',
            ]],
            [InvalidOptionsException::class, [
                'default_language' => self::LANG_DEFAULT,
                'detect_language_from_browser' => true,
                'default_country' => self::COUNTRY_DEFAULT,
                'detect_country_from_browser' => true,
                'default_currency' => self::CURRENCY_DEFAULT,
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
            $this->mockCurrencyManager
        );

        $res = $localizationConfiguration->updateConfiguration([
            'default_language' => self::LANG_DEFAULT,
            'detect_language_from_browser' => true,
            'default_country' => self::COUNTRY_DEFAULT,
            'detect_country_from_browser' => true,
            'default_currency' => self::CURRENCY_DEFAULT,
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
