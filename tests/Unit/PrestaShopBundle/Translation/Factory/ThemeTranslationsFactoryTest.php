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

namespace Tests\Unit\PrestaShopBundle\Translation\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Factory\ThemeTranslationsFactory;
use PrestaShopBundle\Translation\Provider\FrontOfficeProvider;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeTranslationsFactoryTest extends TestCase
{
    const TEST_LOCALE = 'ab-CD';

    const TEST_THEME = 'classic';

    /**
     * @var ThemeTranslationsFactory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ThemeProvider
     */
    private $themeProviderMock;

    private $translations;

    protected function setUp()
    {
        $this->themeProviderMock = $this->getMockBuilder(ThemeProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new ThemeTranslationsFactory($this->themeProviderMock);
        $this->factory->addProvider($this->mockThemeProvider());
    }

    /**
     * @dataProvider provideThemeAndLocale
     *
     * @param $theme
     * @param $locale
     */
    public function testCreateCatalogue($theme, $locale)
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
    public function provideThemeAndLocale()
    {
        return [
            [self::TEST_THEME, self::TEST_LOCALE],
        ];
    }

    /**
     * @dataProvider provideThemeAndLocale
     *
     * @param $theme
     * @param $locale
     */
    public function testCreateTranslationsArray($theme, $locale)
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

        $this->assertPropertiesTranslations();

        $this->assertTranslationsContainThemeMessages();

        $this->assertTranslationsContainCatalogueMessages();

        $this->assertTranslationsContainDefaultAndDatabaseMessages();
    }

    private function getDefaultCatalogue()
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

    private function getFilesystemCatalogue()
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

    private function getUserTranslatedCatalogue()
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ThemeProvider
     */
    protected function mockThemeProvider()
    {
        return $this->mockProvider(ThemeProvider::class, 'theme', self::TEST_LOCALE);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FrontOfficeProvider
     */
    protected function mockFrontOfficeProvider()
    {
        return $this->mockProvider(FrontOfficeProvider::class, 'front', self::TEST_LOCALE);
    }

    private function mockProvider($providerPath, $providerIdentifier, $locale)
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
            ->method('getFilesystemCatalogue')
            ->willReturn($this->getFilesystemCatalogue());

        $providerMock
            ->expects($this->any())
            ->method('getUserTranslatedCatalogue')
            ->willReturn($this->getUserTranslatedCatalogue());

        $providerMock
            ->expects($this->any())
            ->method('getDefaultCatalogue')
            ->willReturn($this->getDefaultCatalogue());

        return $providerMock;
    }

    protected function assertPropertiesTranslations()
    {
        $this->assertInternalType('array', $this->translations);

        $this->assertArrayHasKey('messages', $this->translations);

        $this->assertArrayHasKey('ShopFront', $this->translations);

        $this->assertArrayHasKey('DefaultDomain', $this->translations);
    }

    protected function assertTranslationsContainThemeMessages()
    {
        $this->assertSame(
            [
                'default' => 'Default message bis',
                'xliff' => null,
                'database' => null,
            ],
            $this->translations['DefaultDomain']['Default message bis'],
            'It should provide with default translations.'
        );

        $this->assertSame(
            [
                'default' => 'foo',
                'xliff' => null,
                'database' => null,
            ],
            $this->translations['messages']['foo'],
            'It should provide with default translations.'
        );
    }

    protected function assertTranslationsContainCatalogueMessages()
    {
        $this->assertSame(
            [
                'default' => 'Add to Cart',
                'xliff' => 'Add to Cart override xliff',
                'database' => null,
            ],
            $this->translations['ShopFront']['Add to Cart'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );

        $this->assertSame(
            [
                'default' => 'bar',
                'xliff' => 'Bar override xlif',
                'database' => null,
            ],
            $this->translations['messages']['bar'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );
    }

    protected function assertTranslationsContainDefaultAndDatabaseMessages()
    {
        $this->assertSame(
            [
                'default' => 'Default message',
                'xliff' => 'Default MESSAGE override xliff',
                'database' => 'Default override database',
            ],
            $this->translations['DefaultDomain']['Default message'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );

        $this->assertSame(
            [
                'default' => 'baz',
                'xliff' => 'Baz override xliff',
                'database' => 'Baz is updated from database!',
            ],
            $this->translations['messages']['baz'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );
    }
}
