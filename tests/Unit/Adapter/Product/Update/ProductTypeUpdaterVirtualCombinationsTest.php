<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductTypeUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use ReflectionClass;

class ProductTypeUpdaterVirtualCombinationsTest extends TestCase
{
    /**
     * @dataProvider provideIsVirtualType
     */
    public function testIsVirtualType(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->invokePrivate('isVirtualType', $type));
    }

    public function provideIsVirtualType(): iterable
    {
        yield 'virtual_combinations is virtual' => [ProductType::TYPE_VIRTUAL_COMBINATIONS, true];
        yield 'virtual is virtual' => [ProductType::TYPE_VIRTUAL, true];
        yield 'combinations is not virtual' => [ProductType::TYPE_COMBINATIONS, false];
        yield 'standard is not virtual' => [ProductType::TYPE_STANDARD, false];
    }

    /**
     * @dataProvider provideHasCombinationsType
     */
    public function testHasCombinationsType(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->invokePrivate('hasCombinationsType', $type));
    }

    public function provideHasCombinationsType(): iterable
    {
        yield 'virtual_combinations has combinations' => [ProductType::TYPE_VIRTUAL_COMBINATIONS, true];
        yield 'combinations has combinations' => [ProductType::TYPE_COMBINATIONS, true];
        yield 'virtual has no combinations' => [ProductType::TYPE_VIRTUAL, false];
    }

    private function invokePrivate(string $method, string $type): bool
    {
        $reflection = new ReflectionClass(ProductTypeUpdater::class);
        $updater = $reflection->newInstanceWithoutConstructor();
        $reflectionMethod = $reflection->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invoke($updater, $type);
    }
}
