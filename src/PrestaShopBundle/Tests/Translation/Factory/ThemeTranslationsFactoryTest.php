<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Translation\Factory;

use PrestaShopBundle\Translation\Factory\ThemeTranslationsFactory;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeTranslationsFactoryTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_LOCALE = 'ab-CD';

    const FAKE_THEME = 'fake-theme';

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
        $this->factory->addProvider($this->mockFrontOfficeProvider());
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
                self::FAKE_THEME,
                self::FAKE_LOCALE
            ),
            array(
                'classic',
                self::FAKE_LOCALE
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

        $this->themeProviderMock
            ->expects($this->once())
            ->method('getXliffCatalogue')
            ->willReturn($this->getMessageCatalogue())
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('getDatabaseCatalogue')
            ->willReturn($this->getDatabaseCatalogue())
        ;

        $this->translations = $this->factory->createTranslationsArray($theme, $locale);

        $this->assertTranslationsContainThemeMessages($locale);

        $this->assertTranslationsContainDefaultAndDatabaseMessages($locale);

        $this->assertTranslationsContainCatalogueMessages($locale);
    }

    private function getMessageCatalogue()
    {
        $messageCatalogue = new MessageCatalogue(self::FAKE_LOCALE);
        $messages = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        );

        $messageCatalogue->add($messages, 'messages.'.self::FAKE_LOCALE);

        return $messageCatalogue;
    }

    private function getDatabaseCatalogue()
    {
        $databaseCatalogue = new MessageCatalogue(self::FAKE_LOCALE);
        $messages = array(
            'baz' => 'Baz is updated !',
        );

        $databaseCatalogue->add($messages, 'messages');

        return $databaseCatalogue;
    }

    protected function mockFrontOfficeProvider()
    {
        $frontOfficeProviderMock = $this->getMockBuilder('PrestaShopBundle\Translation\Provider\FrontOfficeProvider')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $frontOfficeProviderMock
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn('front');

        $frontOfficeProviderMock
            ->expects($this->any())
            ->method('getXliffCatalogue')
            ->willReturn(new MessageCatalogue(
                self::FAKE_LOCALE,
                array(
                  'ShopFront.'.self::FAKE_LOCALE => array(
                      'Add to Cart' => 'Add to CART',
                      'Remove from Cart' => 'Remove from CART',
                      'Edit product' => 'Edit it!',
                  )
                )
            ));

        $frontOfficeProviderMock
            ->expects($this->any())
            ->method('getDefaultCatalogue')
            ->willReturn(new MessageCatalogue(
                self::FAKE_LOCALE,
                array(
                    'DefaultDomain.'.self::FAKE_LOCALE => array(
                        'Default message' => 'Default MESSAGE',
                        'Default message bis' => 'Bis'
                    ),
                    'ShopFront.'.self::FAKE_LOCALE => array(
                        'Add to Cart' => 'Add to Cart',
                        'Edit product' => 'Edit it'
                    )
                )
            ));

        $frontOfficeProviderMock
            ->expects($this->any())
            ->method('getDatabaseCatalogue')
            ->willReturn(new MessageCatalogue(
                self::FAKE_LOCALE,
                array(
                    'DefaultDomain' => array(
                        'Default message bis' => 'Bis override',
                    ),
                    'ShopFront' => array(
                        'Edit product' => 'Edit'
                    )
                ) // Domains of database catalogue don't contain locale
            ));

        return $frontOfficeProviderMock;
    }

    /**
     * @param $locale
     */
    protected function assertTranslationsContainThemeMessages($locale)
    {
        $domain = 'messages.' . $locale;

        $this->assertInternalType('array', $this->translations);
        $this->assertArrayHasKey($domain, $this->translations);

        $this->assertSame(
            array(
                'xlf' => 'Baz',
                'db' => 'Baz is updated !',
            ),
            $this->translations[$domain]['baz']
        );
    }

    /**
     * @param $locale
     */
    protected function assertTranslationsContainDefaultAndDatabaseMessages($locale)
    {
        $domain = 'DefaultDomain.' . $locale;
        $this->assertArrayHasKey($domain, $this->translations);

        $this->assertSame(
            array(
                'xlf' => 'Default MESSAGE',
                'db' => '',
            ),
            $this->translations[$domain]['Default message'],
            'It should provide with default translations.'
        );

        $this->assertSame(
            array(
                'xlf' => 'Bis',
                'db' => 'Bis override',
            ),
            $this->translations[$domain]['Default message bis'],
            'It should provide with default translations and their database overrides.'
        );
    }

    /**
     * @param $locale
     */
    protected function assertTranslationsContainCatalogueMessages($locale)
    {
        $domain = 'ShopFront.' . $locale;
        $this->assertArrayHasKey($domain, $this->translations);

        $this->assertSame(
            array(
                'xlf' => 'Add to CART',
                'db' => '',
            ),
            $this->translations[$domain]['Add to Cart'],
            'It should provide with translations from XLIFF catalogue overriding the defaults.'
        );

        $this->assertSame(
            array(
                'xlf' => 'Remove from CART',
                'db' => '',
            ),
            $this->translations[$domain]['Remove from Cart'],
            'It should provide with translations from XLIFF catalogue.'
        );

        $this->assertSame(
            array(
                'xlf' => 'Edit it!',
                'db' => 'Edit',
            ),
            $this->translations[$domain]['Edit product'],
            'It should provide with translations from XLIFF catalogue overriding the defaults and database overrides.'
        );
    }
}
