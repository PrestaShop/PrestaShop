<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
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
}
