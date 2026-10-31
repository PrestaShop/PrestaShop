<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Dispatcher;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class LinkTest extends TestCase
{
    private $originalUseRoutes;

    private $originalRoutes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalUseRoutes = $this->getUseRoutesProperty()->getValue(Dispatcher::getInstance());
        $this->originalRoutes = $this->getRoutesProperty()->getValue(Dispatcher::getInstance());
    }

    protected function tearDown(): void
    {
        // Restore the Dispatcher singleton state mutated by the tests, otherwise the forced
        // use_routes value leaks into other test classes and changes their generated URLs.
        $this->getUseRoutesProperty()->setValue(Dispatcher::getInstance(), $this->originalUseRoutes);
        $this->getRoutesProperty()->setValue(Dispatcher::getInstance(), $this->originalRoutes);
        parent::tearDown();
    }

    private function getUseRoutesProperty(): ReflectionProperty
    {
        $property = (new ReflectionClass('Dispatcher'))->getProperty('use_routes');

        return $property;
    }

    private function getRoutesProperty(): ReflectionProperty
    {
        return (new ReflectionClass('Dispatcher'))->getProperty('routes');
    }

    private function getCategoryLinkWithRule(string $rule, int $categoryId, ?string $alias): string
    {
        $dispatcher = Dispatcher::getInstance();
        $this->getUseRoutesProperty()->setValue($dispatcher, true);

        $idLang = (int) Context::getContext()->language->id;
        $idShop = (int) Context::getContext()->shop->id;
        // Load the shop routes first, otherwise the lazy load would overwrite the rule added below
        $dispatcher->hasKeyword('category_rule', $idLang, 'id', $idShop);
        $dispatcher->addRoute(
            'category_rule',
            $rule,
            'category',
            $idLang,
            $dispatcher->default_routes['category_rule']['keywords'],
            [],
            $idShop
        );

        return Context::getContext()->link->getCategoryLink($categoryId, $alias, $idLang, null, $idShop);
    }

    private function getProductLink(
        bool $statusUseRoutes,
        int $id_product,
        ?int $id_product_attribute,
        ?string $ean13 = null
    ): array {
        $reflectionDispatcher = new ReflectionClass('Dispatcher');
        $property = $reflectionDispatcher->getProperty('use_routes');
        $property->setValue(Dispatcher::getInstance(), $statusUseRoutes);

        $url = Context::getContext()->link->getProductLink(
            $id_product,
            null,
            null,
            $ean13,
            Context::getContext()->language->id,
            null,
            $id_product_attribute,
            false,
            false,
            true
        );

        return parse_url($url);
    }

    public function testUrlTakesVariantIntoAccountWithUrlRewriting(): void
    {
        $filename = basename($this->getProductLink(true, 1, 2)['path']);

        $this->assertEquals('1-2-hummingbird-printed-t-shirt.html', $filename);
    }

    public function testUrlIgnoresVariantIfNotSpecifiedWithUrlRewriting(): void
    {
        $filename = basename($this->getProductLink(true, 1, null)['path']);

        $this->assertEquals('1-hummingbird-printed-t-shirt.html', $filename);
    }

    public function testUrlTakesVariantIntoAccountWithoutUrlRewriting(): void
    {
        parse_str($this->getProductLink(false, 1, 6)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertEquals(6, $query['id_product_attribute']);
    }

    public function testUrlIgnoresVariantIfNotSpecifiedWithoutUrlRewriting(): void
    {
        parse_str($this->getProductLink(false, 1, null)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertArrayNotHasKey('id_product_attribute', $query);
    }

    public function testSupplierUrlOmitsMetaTitleWhenRouteHasNoMetaTitleKeyword(): void
    {
        $reflectionDispatcher = new ReflectionClass('Dispatcher');
        $property = $reflectionDispatcher->getProperty('use_routes');
        $property->setValue(Dispatcher::getInstance(), true);

        $url = Context::getContext()->link->getSupplierLink(1);
        parse_str(parse_url($url)['query'] ?? '', $query);

        $this->assertArrayNotHasKey('meta_title', $query);
    }

    public function testProductUrlOmitsEan13WhenRouteHasNoEan13Keyword(): void
    {
        parse_str(
            $this->getProductLink(true, 1, null, '1234567890128')['query'] ?? '',
            $query
        );

        $this->assertArrayNotHasKey('ean13', $query);
    }

    /**
     * The BO add category form builds its SEO preview for a category that does not exist yet (id 0).
     */
    public function testNewCategoryUrlWithCategoriesKeywordInRoute(): void
    {
        $url = $this->getCategoryLinkWithRule('{categories:/}{id}-{rewrite}', 0, '{friendly-url}');

        $this->assertSame('/-{friendly-url}', parse_url($url, PHP_URL_PATH));
    }

    public function testNewCategoryUrlWithMetaTitleKeywordInRoute(): void
    {
        $url = $this->getCategoryLinkWithRule('{id}-{meta_title:-}{rewrite}', 0, '{friendly-url}');

        $this->assertSame('/-{friendly-url}', parse_url($url, PHP_URL_PATH));
    }

    public function testExistingCategoryUrlKeepsParentCategoriesInRoute(): void
    {
        $url = $this->getCategoryLinkWithRule('{categories:/}{id}-{rewrite}', 4, null);

        $this->assertSame('/clothes/4-men', parse_url($url, PHP_URL_PATH));
    }
}
