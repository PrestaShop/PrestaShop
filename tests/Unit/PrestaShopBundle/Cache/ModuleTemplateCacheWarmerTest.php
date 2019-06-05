<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\Cache;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Cache\ModuleTemplateCacheWarmer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

class ModuleTemplateCacheWarmerTest extends TestCase
{
    public function testOnlyGenerateTwig()
    {
        $twigMock = $this
        ->getMockBuilder(Environment::class)
        ->disableOriginalConstructor()
        ->setMethods(array('loadTemplate'))
        ->getMock();

        $container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $container->expects($this->any())->method('get')->with('twig')->will($this->returnValue($twigMock));

        $cacheWarmer = new ModuleTemplateCacheWarmer($container, null, [__DIR__ . '/../Twig/Fixtures/modules' => 'Modules']);

        // Actual test: Should only have one file to load
        $twigMock
            ->expects($this->once())
            ->method('loadTemplate');

        $cacheWarmer->warmUp(sys_get_temp_dir());
    }
}
