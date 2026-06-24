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
}
