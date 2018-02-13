<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Provider\ModuleProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group sf
 */
class ModuleProviderTest extends TestCase
{
    // @see /resources/translations/en-US/ModulesWirePaymentAdmin.en-US.xlf
    // @see /resources/translations/en-US/ModulesWirePaymentShop.en-US.xlf
    private $provider;
    private $moduleName;
    private static $resourcesDir;

    public function setUp()
    {
        $loader = $this->getMockBuilder('Symfony\Component\Translation\Loader\LoaderInterface')
            ->getMock()
        ;

        $this->moduleName = 'ps_wirepayment';
        self::$resourcesDir = __DIR__.'/../../resources/translations';
        $this->provider = new ModuleProvider($loader, self::$resourcesDir);
        $this->provider->setModuleName($this->moduleName);
    }

    public function testGetMessageCatalogue()
    {
        $expectedReturn = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogue', $expectedReturn);

        // Check integrity of translations
        $this->assertArrayHasKey('ModulesWirePaymentAdmin.en-US', $expectedReturn->all());
        $this->assertArrayHasKey('ModulesWirePaymentShop.en-US', $expectedReturn->all());

        $moduleAdminTranslations = $expectedReturn->all('ModulesWirePaymentAdmin.en-US');
        $this->assertCount(20, $moduleAdminTranslations);
        $this->assertArrayHasKey('Wire payment', $moduleAdminTranslations);
        $this->assertSame('Wire payment', $moduleAdminTranslations['Wire payment']);

        $moduleFrontTranslations = $expectedReturn->all('ModulesWirePaymentShop.en-US');
        $this->assertCount(4, $moduleFrontTranslations);
        $this->assertArrayHasKey('Pay by bank wire', $moduleFrontTranslations);
        $this->assertSame('Pay by bank wire', $moduleFrontTranslations['Pay by bank wire']);
    }
}
