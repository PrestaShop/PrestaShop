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

use PrestaShopBundle\Translation\Provider\ModulesProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group sf
 */
class ModulesProviderTest extends TestCase
{
    // @see /resources/translations/en-US/AdminActions.en-US.xlf
    private $provider;
    private static $resourcesDir;

    public function setUp()
    {
        $loader = $this->getMockBuilder('Symfony\Component\Translation\Loader\LoaderInterface')
            ->getMock()
        ;

        self::$resourcesDir = __DIR__.'/../../resources/translations';
        $this->provider = new ModulesProvider($loader, self::$resourcesDir);
    }

    public function testGetMessageCatalogue()
    {
        $expectedReturn = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogue', $expectedReturn);

        // Check integrity of translations
        $this->assertArrayHasKey('ModulesWirePaymentAdmin.en-US', $expectedReturn->all());
        $this->assertArrayHasKey('ModulesWirePaymentShop.en-US', $expectedReturn->all());

        $moduleAdminTranslations = $expectedReturn->all('ModulesCheckPaymentAdmin.en-US');
        $this->assertCount(9, $moduleAdminTranslations);
        $this->assertArrayHasKey('Payments by check', $moduleAdminTranslations);
        $this->assertSame('Payments by check', $moduleAdminTranslations['Payments by check']);

        $moduleFrontTranslations = $expectedReturn->all('ModulesCheckPaymentShop.en-US');
        $this->assertCount(15, $moduleFrontTranslations);
        $this->assertArrayHasKey('Pay by check', $moduleFrontTranslations);
        $this->assertSame('Pay by check', $moduleFrontTranslations['Pay by check']);
    }
}
