<?php
/**
 * 2007-2019 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\NativeModulesProvider;

/**
 * @doc ./vendor/bin/phpunit -c tests/Unit/phpunit.xml --filter="NativeModulesProviderTest"
 */
final class NativeModulesProviderTest extends TestCase
{
    public function testGetNativeModules()
    {
        $nativeModulesProvider = new NativeModulesProvider();
        $nativeModules = $nativeModulesProvider->getNativeModules();
        $this->assertInternalType('array', $nativeModules);
        $this->assertCount(45, $nativeModules);
        $this->assertContains('bankwire', $nativeModules);
    }

    public function testIsNativeModule()
    {
        $nativeModulesProvider = new NativeModulesProvider();

        $this->assertTrue($nativeModulesProvider->isNativeModule('blockcart'));
        $this->assertFalse($nativeModulesProvider->isNativeModule('some_module'));
    }
}
