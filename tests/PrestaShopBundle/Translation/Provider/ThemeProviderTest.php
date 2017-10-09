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

namespace Tests\PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

/**
 * @group sf
 */
class ThemeProviderTest extends TestCase
{
    private $provider;
    private static $resourcesDir;

    public function setUp()
    {
        $loader = $this->getMockBuilder('Symfony\Component\Translation\Loader\LoaderInterface')
            ->getMock()
        ;

        self::$resourcesDir = __DIR__.'/../../resources/themes/fakeTheme2';
        $this->provider = new ThemeProvider($loader, self::$resourcesDir);
        $this->provider->filesystem = new Filesystem();
    }

    public function testGetMessageCatalogue()
    {
        // The xliff file contains 29 keys
        $expectedReturn = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogue', $expectedReturn);

        // Check integrity of translations
        $this->assertArrayHasKey('ShopTheme.en-US', $expectedReturn->all());
        $this->assertArrayHasKey('ShopThemeCustomerAccount.en-US', $expectedReturn->all());
        $translations = $expectedReturn->all('ShopTheme.en-US');

        $this->assertCount(29, $translations);
        $this->assertArrayHasKey('Contact us', $translations);
        $this->assertSame('Contact us', $translations['Contact us']);
    }
}
