<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\PrestaShopBundle\Translation\Factory;

use PrestaShopBundle\Translation\Factory\ThemeTranslationsFactory;
use Symfony\Component\Translation\MessageCatalogue;
use PHPUnit\Framework\TestCase;

/**
 * @group sf
 */
class ThemeTranslationsFactoryTest extends TestCase
{
    const TEST_LOCALE = 'ab-CD';

    const TEST_THEME = 'classic';

    /**
     * @var ThemeTranslationsFactory
     */
    private $factory;

    private $themeProviderMock;

    private $translations;

    public function setUp()
    {
        $this->themeProviderMock = $this->getMockBuilder('PrestaShopBundle\Translation\Provider\ThemeProvider')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->factory = new ThemeTranslationsFactory($this->themeProviderMock);
        $this->factory->addProvider($this->mockThemeProvider());
    }

    /**
     * @dataProvider getThemeAndLocale
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
            ->willReturn($this->themeProviderMock)
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('setLocale')
            ->with($locale)
            ->willReturn($this->themeProviderMock)
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('getMessageCatalogue')
        ;

        $this->factory->createCatalogue($theme, $locale);
    }

    /**
     * @return array
     */
    public function getThemeAndLocale()
    {
        return array(
            array(
                self::TEST_THEME,
                self::TEST_LOCALE
            ),
        );
    }

    /**
     * @dataProvider getThemeAndLocale
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
            ->willReturn($this->themeProviderMock)
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('setLocale')
            ->with($locale)
            ->willReturn($this->themeProviderMock)
        ;

        $this->translations = $this->factory->createTranslationsArray($theme, $locale);

        $this->assertPropertiesTranslations($locale);

        $this->assertTranslationsContainThemeMessages($locale);

        $this->assertTranslationsContainCatalogueMessages($locale);

        $this->assertTranslationsContainDefaultAndDatabaseMessages($locale);
    }

    private function getMessageCatalogue()
    {
        return new MessageCatalogue(
            self::TEST_LOCALE,
            array(
                'DefaultDomain.'.self::TEST_LOCALE => array(
                    'Default message' => 'Default MESSAGE',
                    'Default message bis' => 'Bis'
                ),
                'ShopFront.'.self::TEST_LOCALE => array(
                    'Add to Cart' => 'Add to Cart',
                    'Edit product' => 'Edit it'
                ),
                'messages.'.self::TEST_LOCALE => array(
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                    'baz' => 'Baz',
                ),
            )
        );
    }

    private function getXliffCatalogue()
    {
        return new MessageCatalogue(
            self::TEST_LOCALE,
            array(
                'DefaultDomain.'.self::TEST_LOCALE => array(
                    'Default message' => 'Default MESSAGE override xliff',
                ),
                'ShopFront.'.self::TEST_LOCALE => array(
                    'Add to Cart' => 'Add to Cart override xliff',
                ),
                'messages.'.self::TEST_LOCALE => array(
                    'bar' => 'Bar override xlif',
                    'baz' => 'Baz override xliff',
                ),
            )
        );
    }

    private function getDatabaseCatalogue()
    {
        return new MessageCatalogue(
            self::TEST_LOCALE,
            array(
                'DefaultDomain' => array(
                    'Default message' => 'Default override database',
                ),
                'ShopFront' => array(
                    'Edit product' => 'Edit override database',
                ),
                'messages' => array(
                    'baz' => 'Baz is updated from database!',
                ),
            ) // Domains of database catalogue don't contain locale
        );
    }

    protected function mockThemeProvider()
    {
        return $this->mockProvider('PrestaShopBundle\Translation\Provider\ThemeProvider', 'theme', self::TEST_LOCALE);
    }

    protected function mockFrontOfficeProvider()
    {
        return $this->mockProvider('PrestaShopBundle\Translation\Provider\FrontOfficeProvider', 'front', self::TEST_LOCALE);
    }

    private function mockProvider($providerPath, $providerIdentifier, $locale)
    {
        $providerMock = $this->getMockBuilder($providerPath)
            ->disableOriginalConstructor()
            ->getMock()
        ;

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


    protected function assertPropertiesTranslations($locale)
    {
        $this->assertInternalType('array', $this->translations);

        $domain = 'messages.' . $locale;
        $this->assertArrayHasKey($domain, $this->translations);

        $domain = 'ShopFront.' . $locale;
        $this->assertArrayHasKey($domain, $this->translations);

        $domain = 'DefaultDomain.' . $locale;
        $this->assertArrayHasKey($domain, $this->translations);
    }

    /**
     * @param $locale
     */
    protected function assertTranslationsContainThemeMessages($locale)
    {
        $this->assertSame(
            array(
                'xlf' => null,
                'db' => null,
            ),
            $this->translations['DefaultDomain.' . $locale]['Default message bis'],
            'It should provide with default translations.'
        );

        $this->assertSame(
            array(
                'xlf' => null,
                'db' => null,
            ),
            $this->translations['messages.' . $locale]['foo'],
            'It should provide with default translations.'
        );
    }

    /**
     * @param $locale
     */
    protected function assertTranslationsContainCatalogueMessages($locale)
    {
        $this->assertSame(
            array(
                'xlf' => 'Add to Cart override xliff',
                'db' => null,
            ),
            $this->translations['ShopFront.' . $locale]['Add to Cart'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );

        $this->assertSame(
            array(
                'xlf' => 'Bar override xlif',
                'db' => null,
            ),
            $this->translations['messages.' . $locale]['bar'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );
    }

    /**
     * @param $locale
     */
    protected function assertTranslationsContainDefaultAndDatabaseMessages($locale)
    {
        $this->assertSame(
            array(
                'xlf' => 'Default MESSAGE override xliff',
                'db' => 'Default override database',
            ),
            $this->translations['DefaultDomain.' . $locale]['Default message'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );

        $this->assertSame(
            array(
                'xlf' => 'Baz override xliff',
                'db' => 'Baz is updated from database!',
            ),
            $this->translations['messages.' . $locale]['baz'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );
    }
}
