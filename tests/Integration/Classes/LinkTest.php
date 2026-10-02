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

    /** @var array */
    private $originalGet;

    /** @var string|null */
    private $originalController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalUseRoutes = $this->getUseRoutesProperty()->getValue(Dispatcher::getInstance());
        $this->originalGet = $_GET;
        $this->originalController = $this->getControllerProperty()->getValue(Dispatcher::getInstance());
    }

    protected function tearDown(): void
    {
        // Restore the Dispatcher singleton state mutated by the tests, otherwise the forced
        // use_routes value leaks into other test classes and changes their generated URLs.
        $this->getUseRoutesProperty()->setValue(Dispatcher::getInstance(), $this->originalUseRoutes);
        $_GET = $this->originalGet;
        $this->getControllerProperty()->setValue(Dispatcher::getInstance(), $this->originalController);
        parent::tearDown();
    }

    private function getControllerProperty(): ReflectionProperty
    {
        $property = (new ReflectionClass('Dispatcher'))->getProperty('controller');
        $property->setAccessible(true);

        return $property;
    }

    private function getUseRoutesProperty(): ReflectionProperty
    {
        $property = (new ReflectionClass('Dispatcher'))->getProperty('use_routes');
        $property->setAccessible(true);

        return $property;
    }

    private function getProductLink(
        bool $statusUseRoutes,
        int $id_product,
        ?int $id_product_attribute,
        ?string $ean13 = null
    ): array {
        $reflectionDispatcher = new ReflectionClass('Dispatcher');
        $property = $reflectionDispatcher->getProperty('use_routes');
        $property->setAccessible(true);
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
        $property->setAccessible(true);
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
     * getLanguageLink() builds the hreflang alternates from the ids in the query string, so it sees whatever
     * the visitor typed. getProductLink() and getCategoryObject() throw on an id that casts to 0, which used
     * to turn a URL such as /0-some-product.html into a 500 instead of a 404.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/33306
     *
     * @dataProvider provideInvalidEntityIds
     */
    public function testLanguageLinkDoesNotThrowOnAnIdThatCastsToZero(string $controller, string $key, string $value): void
    {
        $_GET = ['controller' => $controller, $key => $value];
        $this->getControllerProperty()->setValue(Dispatcher::getInstance(), $controller);

        $link = Context::getContext()->link->getLanguageLink(Context::getContext()->language->id);

        $this->assertNotEmpty($link);
    }

    public static function provideInvalidEntityIds(): array
    {
        return [
            'product id zero' => ['product', 'id_product', '0'],
            'product id empty' => ['product', 'id_product', ''],
            'product id not a number' => ['product', 'id_product', 'abc'],
            'product id below one' => ['product', 'id_product', '0.5'],
            'category id zero' => ['category', 'id_category', '0'],
            'category id empty' => ['category', 'id_category', ''],
            'category id not a number' => ['category', 'id_category', 'abc'],
        ];
    }

    /**
     * The supplier, manufacturer and CMS builders accept such an id but return a URL naming no entity
     * (/supplier/-), which answers 404: the alternate link has to be the page being served instead.
     *
     * @dataProvider provideIdsWithoutAnEntity
     */
    public function testLanguageLinkPointsAtThePageForAnIdThatCastsToZero(string $controller, string $key, string $value): void
    {
        $_GET = ['controller' => $controller, $key => $value];
        $this->getControllerProperty()->setValue(Dispatcher::getInstance(), $controller);
        $link = Context::getContext()->link;
        $idLang = (int) Context::getContext()->language->id;
        $allow = (new ReflectionClass('Link'))->getProperty('allow');
        $allow->setAccessible(true);
        $params = $allow->getValue($link) ? [$key => $value] : [$key => $value, 'id_lang' => $idLang];

        $this->assertSame($link->getPageLink($controller, null, $idLang, $params), $link->getLanguageLink($idLang));
    }

    public static function provideIdsWithoutAnEntity(): array
    {
        return [
            'supplier id zero' => ['supplier', 'id_supplier', '0'],
            'supplier id not a number' => ['supplier', 'id_supplier', 'abc'],
            'manufacturer id zero' => ['manufacturer', 'id_manufacturer', '0'],
            'manufacturer id not a number' => ['manufacturer', 'id_manufacturer', 'abc'],
            'cms page id zero' => ['cms', 'id_cms', '0'],
            'cms category id zero' => ['cms', 'id_cms_category', '0'],
        ];
    }

    public function testLanguageLinkStillPointsAtTheProductForAValidId(): void
    {
        $_GET = ['controller' => 'product', 'id_product' => '1'];
        $this->getControllerProperty()->setValue(Dispatcher::getInstance(), 'product');

        $link = Context::getContext()->link->getLanguageLink(Context::getContext()->language->id);

        $this->assertStringContainsString('hummingbird-printed-t-shirt', $link);
    }
}
