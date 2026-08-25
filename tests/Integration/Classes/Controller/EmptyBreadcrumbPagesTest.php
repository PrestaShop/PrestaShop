<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Context;
use Controller;
use FrontController;
use OrderController;
use PageNotFoundController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A page that adds no breadcrumb entry of its own is left with the bare home link. Themes treat a
 * one-level breadcrumb as empty and hide it, so those pages show no breadcrumb at all while every
 * neighbouring page shows one.
 */
class EmptyBreadcrumbPagesTest extends TestCase
{
    /**
     * @dataProvider getControllersThatRenderAPage
     */
    public function testAPageCarriesAnEntryOfItsOwn(string $controllerClass, string $expectedTitle): void
    {
        $links = $this->getBreadcrumbLinks($controllerClass);

        self::assertCount(2, $links, sprintf('%s left the breadcrumb with the home link alone', $controllerClass));
        self::assertSame('Home', $links[0]['title']);
        self::assertSame($expectedTitle, $links[1]['title']);
        self::assertNotEmpty($links[1]['url'], 'the entry needs a link of its own');
    }

    public static function getControllersThatRenderAPage(): array
    {
        return [
            'not found page' => [PageNotFoundController::class, '404 error'],
            'checkout page' => [OrderController::class, 'Checkout'],
        ];
    }

    /**
     * @return array<int, array{title: string, url: string}>
     */
    private function getBreadcrumbLinks(string $controllerClass): array
    {
        // The constructor of a front controller boots a whole page context, which is far more than
        // the breadcrumb needs - the method only reads the context and the translator.
        $controller = $this->makeControllerWithoutBooting($controllerClass);

        $context = Context::getContext();
        $this->setProperty($controller, 'context', $context);
        $this->setProperty($controller, 'translator', $context->getTranslator());

        // Called through reflection so the test measures what the breadcrumb holds rather than
        // the visibility the method happens to be declared with.
        $method = new ReflectionMethod($controllerClass, 'getBreadcrumbLinks');
        $method->setAccessible(true);

        return $method->invoke($controller)['links'];
    }

    private function makeControllerWithoutBooting(string $controllerClass): FrontController
    {
        return (new ReflectionClass($controllerClass))->newInstanceWithoutConstructor();
    }

    private function setProperty(Controller $controller, string $name, $value): void
    {
        $property = new ReflectionProperty(Controller::class, $name);
        $property->setAccessible(true);
        $property->setValue($controller, $value);
    }
}
