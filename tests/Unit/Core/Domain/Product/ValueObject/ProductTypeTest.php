<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

class ProductTypeTest extends TestCase
{
    public function testVirtualCombinationsConstantValue(): void
    {
        Assert::assertSame('virtual_combinations', ProductType::TYPE_VIRTUAL_COMBINATIONS);
    }

    public function testItIsConstructedSuccessfullyWithVirtualCombinations(): void
    {
        $productType = new ProductType(ProductType::TYPE_VIRTUAL_COMBINATIONS);

        Assert::assertSame('virtual_combinations', $productType->getValue());
    }

    public function testVirtualCombinationsIsAvailableType(): void
    {
        Assert::assertContains(ProductType::TYPE_VIRTUAL_COMBINATIONS, ProductType::AVAILABLE_TYPES);
    }

    /**
     * @dataProvider provideIsVirtualType
     */
    public function testIsVirtualType(string $type, bool $expected): void
    {
        Assert::assertSame($expected, ProductType::isVirtualType($type));
    }

    public function provideIsVirtualType(): iterable
    {
        yield 'virtual is virtual' => [ProductType::TYPE_VIRTUAL, true];
        yield 'virtual_combinations is virtual' => [ProductType::TYPE_VIRTUAL_COMBINATIONS, true];
        yield 'combinations is not virtual' => [ProductType::TYPE_COMBINATIONS, false];
        yield 'standard is not virtual' => [ProductType::TYPE_STANDARD, false];
        yield 'pack is not virtual' => [ProductType::TYPE_PACK, false];
    }

    /**
     * @dataProvider provideHasCombinations
     */
    public function testHasCombinations(string $type, bool $expected): void
    {
        Assert::assertSame($expected, ProductType::hasCombinations($type));
    }

    public function provideHasCombinations(): iterable
    {
        yield 'combinations has combinations' => [ProductType::TYPE_COMBINATIONS, true];
        yield 'virtual_combinations has combinations' => [ProductType::TYPE_VIRTUAL_COMBINATIONS, true];
        yield 'virtual has no combinations' => [ProductType::TYPE_VIRTUAL, false];
        yield 'standard has no combinations' => [ProductType::TYPE_STANDARD, false];
        yield 'pack has no combinations' => [ProductType::TYPE_PACK, false];
    }
}
