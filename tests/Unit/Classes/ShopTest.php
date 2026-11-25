<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Shop;

final class ShopTest extends TestCase
{
    private const SHOPS = [
        3 => ['id' => 3, 'shops' => [7 => ['name' => 'Main shop']]],
    ];

    private mixed $previousShops;

    protected function setUp(): void
    {
        $property = new ReflectionProperty(Shop::class, 'shops');
        $this->previousShops = $property->getValue();
        $property->setValue(null, self::SHOPS);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(Shop::class, 'shops'))->setValue(null, $this->previousShops);
    }

    public function testGroupOfAKnownShopIsFound(): void
    {
        self::assertSame(3, Shop::getGroupFromShop(7));
        self::assertSame(self::SHOPS[3], Shop::getGroupFromShop(7, false));
    }

    public function testGroupOfAnUnknownShopIsNotFound(): void
    {
        self::assertFalse(Shop::getGroupFromShop(8));
    }

    /**
     * Shop::getContextShopID() is null outside of a single shop context, for instance in the webservice.
     * array_key_exists() with a null key is deprecated since PHP 8.5.
     */
    public function testNullShopIdIsNotFoundWithoutDeprecation(): void
    {
        $deprecations = [];
        set_error_handler(static function (int $level, string $message) use (&$deprecations): bool {
            $deprecations[] = $message;

            return true;
        }, E_DEPRECATED);

        try {
            $group = Shop::getGroupFromShop(null);
            $groupData = Shop::getGroupFromShop(null, false);
        } finally {
            restore_error_handler();
        }

        self::assertFalse($group);
        self::assertFalse($groupData);
        self::assertSame([], $deprecations);
    }

    #[DataProvider('provideDefaultShopRedirectUrls')]
    public function testDefaultShopRedirectUrl(
        string $domain,
        string $baseUri,
        bool $rewritingEnabled,
        string $httpHost,
        string $requestUri,
        array $queryParameters,
        string $expectedUrl
    ): void {
        $url = (new ReflectionMethod(Shop::class, 'getDefaultShopRedirectUrl'))->invoke(
            null,
            $domain,
            $baseUri,
            $rewritingEnabled,
            $httpHost,
            $requestUri,
            $queryParameters
        );

        self::assertSame($expectedUrl, $url);
    }

    public static function provideDefaultShopRedirectUrls(): iterable
    {
        yield 'shop on www, request on the apex domain' => [
            'www.example.com', '/', true, 'example.com', '/en/?q=shoes', ['q' => 'shoes'],
            'www.example.com/en/?q=shoes',
        ];
        yield 'shop on the apex domain, request on www' => [
            'example.com', '/', true, 'www.example.com', '/en/?q=shoes&page=2', ['q' => 'shoes', 'page' => '2'],
            'example.com/en/?q=shoes&page=2',
        ];
        yield 'www redirect without query string' => [
            'www.example.com', '/', true, 'example.com', '/en/', [],
            'www.example.com/en/',
        ];
        yield 'www redirect removes id_shop' => [
            'www.example.com', '/', true, 'example.com', '/en/?id_shop=2&q=shoes', ['id_shop' => '2', 'q' => 'shoes'],
            'www.example.com/en/?q=shoes',
        ];
        yield 'www redirect keeps a path starting with a double slash' => [
            'www.example.com', '/', true, 'example.com', '//en/?q=shoes', ['q' => 'shoes'],
            'www.example.com//en/?q=shoes',
        ];
        yield 'unrelated host redirects to the base URI' => [
            'www.example.com', '/shop/', true, 'other.com', '/en/?id_shop=2&q=shoes', ['id_shop' => '2', 'q' => 'shoes'],
            'www.example.com/shop/?q=shoes',
        ];
        yield 'friendly URLs disabled' => [
            'www.example.com', '/shop/', false, 'example.com', '/index.php?id_shop=2&controller=product', ['id_shop' => '2', 'controller' => 'product'],
            'www.example.com/shop/index.php?controller=product',
        ];
    }
}
