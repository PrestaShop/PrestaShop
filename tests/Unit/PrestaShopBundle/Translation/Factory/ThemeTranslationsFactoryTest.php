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

namespace Tests\Unit\PrestaShopBundle\Translation\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Factory\ThemeTranslationsFactory;
use PrestaShopBundle\Translation\Provider\FrontOfficeProvider;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeTranslationsFactoryTest extends TestCase
{
    private const TEST_LOCALE = 'ab-CD';

    private const TEST_THEME = 'classic';

    /**
     * @var ThemeTranslationsFactory
     */
    private $factory;

    /**
     * @var ThemeProvider|MockObject
     */
    private $themeProviderMock;

    private $translations;

    protected function setUp(): void
    {
        $this->themeProviderMock = $this->getMockBuilder(ThemeProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new ThemeTranslationsFactory($this->themeProviderMock);
        $this->factory->addProvider($this->mockThemeProvider());
    }

    /**
     * @dataProvider getThemeAndLocale
     *
     * @param string $theme
     * @param string $locale
     */
    public function testCreateCatalogue(string $theme, string $locale): void
    {
        $this->themeProviderMock
            ->expects($this->once())
            ->method('setThemeName')
            ->with($theme)
            ->willReturn($this->themeProviderMock);

        $this->themeProviderMock
            ->expects($this->once())
            ->method('setLocale')
            ->with($locale)
            ->willReturn($this->themeProviderMock);

        $this->themeProviderMock
            ->expects($this->once())
            ->method('getMessageCatalogue');

        $this->factory->createCatalogue($theme, $locale);
    }

    /**
     * @return array
     */
    public function getThemeAndLocale(): array
    {
        return [
            [
                self::TEST_THEME,
                self::TEST_LOCALE,
            ],
        ];
    }

    /**
     * @dataProvider getThemeAndLocale
     *
     * @param string $theme
     * @param string $locale
     */
    public function testCreateTranslationsArray(string $theme, string $locale): void
    {
        $this->themeProviderMock
            ->expects($this->once())
            ->method('setThemeName')
            ->with($theme)
            ->willReturn($this->themeProviderMock);

        $this->themeProviderMock
            ->expects($this->once())
            ->method('setLocale')
            ->with($locale)
            ->willReturn($this->themeProviderMock);

        $this->translations = $this->factory->createTranslationsArray($theme, $locale);

        $this->assertPropertiesTranslations($locale);

        $this->assertTranslationsContainThemeMessages($locale);

        $this->assertTranslationsContainCatalogueMessages($locale);

        $this->assertTranslationsContainDefaultAndDatabaseMessages($locale);
    }

    private function getMessageCatalogue(): MessageCatalogue
    {
        return new MessageCatalogue(
            self::TEST_LOCALE,
            [
                'DefaultDomain' => [
                    'Default message' => 'Default MESSAGE',
                    'Default message bis' => 'Bis',
                ],
                'ShopFront' => [
                    'Add to Cart' => 'Add to Cart',
                    'Edit product' => 'Edit it',
                ],
                'messages' => [
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                    'baz' => 'Baz',
                ],
            ]
        );
    }

    private function getXliffCatalogue(): MessageCatalogue
    {
        return new MessageCatalogue(
            self::TEST_LOCALE,
            [
                'DefaultDomain' => [
                    'Default message' => 'Default MESSAGE override xliff',
                ],
                'ShopFront' => [
                    'Add to Cart' => 'Add to Cart override xliff',
                ],
                'messages' => [
                    'bar' => 'Bar override xlif',
                    'baz' => 'Baz override xliff',
                ],
            ]
        );
    }

    private function getDatabaseCatalogue(): MessageCatalogue
    {
        return new MessageCatalogue(
            self::TEST_LOCALE,
            [
                'DefaultDomain' => [
                    'Default message' => 'Default override database',
                ],
                'ShopFront' => [
                    'Edit product' => 'Edit override database',
                ],
                'messages' => [
                    'baz' => 'Baz is updated from database!',
                ],
            ] // Domains of database catalogue don't contain locale
        );
    }

    protected function mockThemeProvider(): ThemeProvider
    {
        return $this->mockProvider(ThemeProvider::class, 'theme', self::TEST_LOCALE);
    }

    protected function mockFrontOfficeProvider(): FrontOfficeProvider
    {
        return $this->mockProvider(FrontOfficeProvider::class, 'front', self::TEST_LOCALE);
    }

    private function mockProvider(string $providerPath, string $providerIdentifier, string $locale)
    {
        $providerMock = $this->getMockBuilder($providerPath)
            ->disableOriginalConstructor()
            ->getMock();

        $providerMock
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn($providerIdentifier);

        $providerMock
            ->expects($this->any())
            ->method('getLocale')
            ->willReturn($locale);

        $providerMock
            ->expects($this->any())
            ->method('getXliffCatalogue')
            ->willReturn($this->getXliffCatalogue());

        $providerMock
            ->expects($this->any())
            ->method('getDatabaseCatalogue')
            ->willReturn($this->getDatabaseCatalogue());

        $providerMock
            ->expects($this->any())
            ->method('getMessageCatalogue')
            ->willReturn($this->getMessageCatalogue());

        return $providerMock;
    }

    /**
     * @param string $locale
     */
    protected function assertPropertiesTranslations(string $locale): void
    {
        $this->assertIsArray($this->translations);

        $this->assertArrayHasKey('messages', $this->translations);

        $this->assertArrayHasKey('ShopFront', $this->translations);

        $this->assertArrayHasKey('DefaultDomain', $this->translations);
    }

    /**
     * @param string $locale
     */
    protected function assertTranslationsContainThemeMessages(string $locale): void
    {
        $this->assertSame(
            [
                'xlf' => null,
                'db' => null,
            ],
            $this->translations['DefaultDomain']['Default message bis'],
            'It should provide with default translations.'
        );

        $this->assertSame(
            [
                'xlf' => null,
                'db' => null,
            ],
            $this->translations['messages']['foo'],
            'It should provide with default translations.'
        );
    }

    /**
     * @param string $locale
     */
    protected function assertTranslationsContainCatalogueMessages(string $locale): void
    {
        $this->assertSame(
            [
                'xlf' => 'Add to Cart override xliff',
                'db' => null,
            ],
            $this->translations['ShopFront']['Add to Cart'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );

        $this->assertSame(
            [
                'xlf' => 'Bar override xlif',
                'db' => null,
            ],
            $this->translations['messages']['bar'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );
    }

    /**
     * @param string $locale
     */
    protected function assertTranslationsContainDefaultAndDatabaseMessages(string $locale): void
    {
        $this->assertSame(
            [
                'xlf' => 'Default MESSAGE override xliff',
                'db' => 'Default override database',
            ],
            $this->translations['DefaultDomain']['Default message'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );

        $this->assertSame(
            [
                'xlf' => 'Baz override xliff',
                'db' => 'Baz is updated from database!',
            ],
            $this->translations['messages']['baz'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );
    }
}
