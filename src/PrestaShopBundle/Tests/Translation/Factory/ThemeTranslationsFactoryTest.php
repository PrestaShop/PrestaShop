<?php
/**
 * 2007-2016 PrestaShop.
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Translation\Factory;

use PrestaShopBundle\Translation\Factory\ThemeTranslationsFactory;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeTranslationsFactoryTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_LOCALE = 'ab-CD';
    const FAKE_THEME = 'fake-theme';

    private $factory;
    private $themeProviderMock;

    public function setUp()
    {
        $this->themeProviderMock = $this->getMockBuilder('PrestaShopBundle\Translation\Provider\ThemeProvider')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->factory = new ThemeTranslationsFactory($this->themeProviderMock);
    }

    public function testCreateCatalogue()
    {
        $this->themeProviderMock
            ->expects($this->once())
            ->method('setThemeName')
            ->with(self::FAKE_THEME)
            ->willReturn($this->themeProviderMock)
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('setLocale')
            ->with(self::FAKE_LOCALE)
            ->willReturn($this->themeProviderMock)
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('getMessageCatalogue')
        ;

        $this->factory->createCatalogue(self::FAKE_THEME, self::FAKE_LOCALE);
    }

    public function testCreateTranslationsArray()
    {
        $this->themeProviderMock
            ->expects($this->once())
            ->method('setThemeName')
            ->with(self::FAKE_THEME)
            ->willReturn($this->themeProviderMock)
        ;

        $this->themeProviderMock
            ->expects($this->once())
            ->method('setLocale')
            ->with(self::FAKE_LOCALE)
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

        $translations = $this->factory->createTranslationsArray(self::FAKE_THEME, self::FAKE_LOCALE);

        $domain = 'messages.'.self::FAKE_LOCALE;

        $this->assertInternalType('array', $translations);
        $this->assertArrayHasKey($domain, $translations);

        $this->assertSame(
            array(
                'xlf' => 'Baz',
                'db' => 'Baz is updated !',
        ), $translations[$domain]['baz']);
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
}
