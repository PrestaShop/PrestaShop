<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Routing\Linter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\Linter\Exception\ControllerNotFoundException;
use PrestaShopBundle\Routing\Linter\Exception\NamingConventionException;
use PrestaShopBundle\Routing\Linter\Exception\SymfonyControllerConventionException;
use PrestaShopBundle\Routing\Linter\NamingConventionLinter;
use Symfony\Component\Routing\Route;
use Tests\Resources\Controller\TestController;

class NamingConventionLinterTest extends TestCase
{
    /**
     * @var NamingConventionLinter
     */
    private $namingConventionLinter;

    public function setUp(): void
    {
        $this->namingConventionLinter = new NamingConventionLinter();
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

    /**
     * @dataProvider getRoutesThatDoNotFollowSymfonyConventions
     */
    public function testLinterThrowsExceptionWhenControllerDoesNotFollowSymfonyConventions($routeName, Route $route)
    {
        $this->expectException(SymfonyControllerConventionException::class);

        $this->namingConventionLinter->lint($routeName, $route);
    }

    /**
     * @dataProvider getRoutesThatUseControllerNotFound
     */
    public function testLinterThrowsExceptionWhenControllerIsNotFound($routeName, Route $route)
    {
        $this->expectException(ControllerNotFoundException::class);

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

    public function getRoutesThatDoNotFollowSymfonyConventions(): iterable
    {
        yield [
            'admin_tests_do_something',
            new Route('/', [
                '_controller' => sprintf('%s:%s', TestController::class, 'doSomethingComplexAction'),
            ]),
        ];
    }

    public function getRoutesThatUseControllerNotFound(): iterable
    {
        yield [
            'admin_tests_do_something',
            new Route('/', [
                '_controller' => sprintf('%s::%s', TestController::class, 'methodNotFound'),
            ]),
        ];
    }
}
