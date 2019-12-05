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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\Routing\Linter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\PrestaShopBundle;
use PrestaShopBundle\Routing\Linter\Exception\NamingConventionException;
use PrestaShopBundle\Routing\Linter\NamingConventionLinter;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route;
use Tests\Resources\Controller\TestController;

class NamingConventionLinterTest extends TestCase
{
    /**
     * @var NamingConventionLinter
     */
    private $namingConventionLinter;

    public function setUp()
    {
        $this->namingConventionLinter = new NamingConventionLinter($this->createControllerNameParser());
    }

    /**
     * @dataProvider getRoutesThatFollowNamingConventions
     */
    public function testLinterPassesWhenRouteAndControllerFollowNamingConventions($routeName, Route $route)
    {
        $this->namingConventionLinter->lint($routeName, $route);

        $this->assertTrue($exceptionWasNotThrown = true);
    }

    /**
     * @dataProvider getRoutesThatDoNotFollowNamingConventions
     */
    public function testLinterThrowsExceptionWhenRouteAndControllerDoesNotFollowNamingConventions($routeName, Route $route)
    {
        $this->expectException(NamingConventionException::class);

        $this->namingConventionLinter->lint($routeName, $route);
    }

    public function getRoutesThatFollowNamingConventions()
    {
        yield [
            'admin_tests_index',
            new Route('/', [
                '_controller' => sprintf('%s::%s', TestController::class, 'indexAction'),
            ]),
        ];

        yield [
            'admin_tests_do_something_complex',
            new Route('/', [
                '_controller' => sprintf('%s::%s', TestController::class, 'doSomethingComplexAction'),
            ]),
        ];
    }

    public function getRoutesThatDoNotFollowNamingConventions()
    {
        yield [
            'admin_test_index',
            new Route('/', [
                '_controller' => sprintf('%s::%s', TestController::class, 'createAction'),
            ]),
        ];

        yield [
            'admin_tests_do_something',
            new Route('/', [
                '_controller' => sprintf('%s::%s', TestController::class, 'doSomethingComplexAction'),
            ]),
        ];
    }

    public function createControllerNameParser()
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getBundles')->willReturn([
            'prestashop' => $this->createMock(PrestaShopBundle::class),
        ]);

        return new ControllerNameParser($kernel);
    }
}
