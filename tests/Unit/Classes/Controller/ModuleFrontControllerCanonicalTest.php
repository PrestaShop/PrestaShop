<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use Language;
use Link;
use Module;
use ModuleFrontController;
use ModuleFrontControllerCore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class ModuleFrontControllerCanonicalTest extends TestCase
{
    /**
     * @dataProvider getRequests
     */
    public function testOnlyARequestWithoutItsOwnParametersIsRedirected(array $query, bool $expected): void
    {
        $controller = (new ReflectionClass(ModuleFrontController::class))->newInstanceWithoutConstructor();

        self::assertSame($expected, (new ReflectionMethod(ModuleFrontControllerCore::class, 'carriesOnlyRoutingParameters'))->invoke($controller, $query));
    }

    public static function getRequests(): array
    {
        return [
            'the plain parameter form of a module page' => [['controller' => 'gdpr', 'fc' => 'module', 'module' => 'psgdpr'], true],
            'a language id' => [['controller' => 'gdpr', 'fc' => 'module', 'module' => 'psgdpr', 'id_lang' => '1'], true],
            'a language iso code, as in a multi-language friendly URL' => [['controller' => 'gdpr', 'fc' => 'module', 'module' => 'psgdpr', 'isolang' => 'en'], true],
            'a payment callback carrying a cart' => [['controller' => 'validation', 'fc' => 'module', 'module' => 'ps_wirepayment', 'id_cart' => '7'], false],
            'a callback carrying a token' => [['controller' => 'validation', 'fc' => 'module', 'module' => 'ps_wirepayment', 'token' => 'abc'], false],
        ];
    }

    public function testAModulePageIsRedirectedToItsFriendlyUrl(): void
    {
        $controller = $this->buildController(static fn ($link) => $link->method('getModuleLink')->willReturn('http://shop.test/en/module/probe/validation'));
        $controller->expects($this->once())->method('canonicalRedirection')->with('http://shop.test/en/module/probe/validation');

        (new ReflectionMethod(ModuleFrontControllerCore::class, 'redirectToCanonicalModuleUrl'))->invoke($controller, 'validation');
    }

    public function testARouteThatCannotBeBuiltWithoutItsParameterIsNotRedirected(): void
    {
        $controller = $this->buildController(static fn ($link) => $link->method('getModuleLink')->willThrowException(new PrestaShopException('Dispatcher::createUrl() miss required parameter')));
        $controller->expects($this->never())->method('canonicalRedirection');

        (new ReflectionMethod(ModuleFrontControllerCore::class, 'redirectToCanonicalModuleUrl'))->invoke($controller, 'validation');
    }

    private function buildController(callable $configureLink): MockObject&ModuleFrontController
    {
        $controller = $this->createPartialMock(ModuleFrontController::class, ['canonicalRedirection']);
        $link = $this->createMock(Link::class);
        $configureLink($link);
        $context = $this->createMock(Context::class);
        $context->link = $link;
        $context->language = $this->createMock(Language::class);
        $context->language->id = 1;
        (new ReflectionProperty(ModuleFrontControllerCore::class, 'context'))->setValue($controller, $context);
        $module = $this->createMock(Module::class);
        $module->name = 'probe';
        $controller->module = $module;

        return $controller;
    }
}
