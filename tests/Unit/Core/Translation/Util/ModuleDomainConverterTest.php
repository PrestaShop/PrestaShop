<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Translation\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Util\ModuleDomainConverter;

/**
 * @doc ./vendor/bin/phpunit
 */
class ModuleDomainConverterTest extends TestCase
{
    /**
     * @var ModuleDomainConverter the module domain converter
     */
    private $moduleDomainConverter;

    protected function setUp()
    {
        $activeModuleList = [
            'foo',
            'bar',
            'baz',
            'foo_bar',
            'foo.bar',
        ];

        $this->moduleDomainConverter = new ModuleDomainConverter($activeModuleList);
    }

    /**
     * @dataProvider getModulesDomains
     *
     * @param string $domain
     * @param string $module
     */
    public function testGetModuleFromDomain($module, $domain)
    {
        $this->assertSame($module, $this->moduleDomainConverter->getModuleFromDomain($domain));
    }

    /**
     * @dataProvider getModulesDomains
     *
     * @param string $domain
     * @param string $module
     */
    public function testGetDomainFromModule($module, $domain)
    {
        $this->assertSame($domain, $this->moduleDomainConverter->getDomainFromModule($module));
    }

    public function getModulesDomains()
    {
        return [
            'foo' => ['foo', 'Modules.Foo'],
            'foo_bar' => ['foo_bar', 'Modules.FooBar'],
        ];
    }
}
