<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use ModuleFrontController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * A module front controller now redirects to its friendly URL, which core controllers have always
 * done. Module controllers are also the payment and callback endpoints though, so the decision has
 * to leave anything carrying real parameters alone - a redirect would drop them.
 */
class ModuleFrontControllerCanonicalTest extends TestCase
{
    private array $originalGet;
    private array $originalServer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalGet = $_GET;
        $this->originalServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected function tearDown(): void
    {
        $_GET = $this->originalGet;
        $_SERVER = $this->originalServer;
        parent::tearDown();
    }

    /**
     * @dataProvider getRequests
     */
    public function testOnlyARequestWithoutItsOwnParametersIsRedirected(array $query, bool $expected, string $because): void
    {
        $_GET = $query;

        self::assertSame($expected, $this->decide(), $because);
    }

    public static function getRequests(): array
    {
        return [
            'the plain parameter form of a module page' => [
                ['controller' => 'gdpr', 'fc' => 'module', 'module' => 'psgdpr'],
                true,
                'a module page reached through the parameter form has a friendly URL to point at',
            ],
            'a language parameter still belongs to the routing' => [
                ['controller' => 'gdpr', 'fc' => 'module', 'module' => 'psgdpr', 'id_lang' => '1'],
                true,
                'the language is part of the route, not of the page',
            ],
            'a payment callback carrying a cart' => [
                ['controller' => 'validation', 'fc' => 'module', 'module' => 'ps_wirepayment', 'id_cart' => '7'],
                false,
                'redirecting would drop the cart the callback is about',
            ],
            'a callback carrying a token' => [
                ['controller' => 'validation', 'fc' => 'module', 'module' => 'ps_wirepayment', 'token' => 'abc'],
                false,
                'redirecting would drop the token the callback is authenticated with',
            ],
        ];
    }

    private function decide(): bool
    {
        $controller = (new ReflectionClass(ModuleFrontController::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(ModuleFrontController::class, 'carriesOnlyRoutingParameters');
        $method->setAccessible(true);

        return $method->invoke($controller, $_GET);
    }
}
