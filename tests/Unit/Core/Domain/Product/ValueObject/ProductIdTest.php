<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class ProductIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     */
    public function testItIsConstructedSuccessfully(int $value): void
    {
        $productId = new ProductId($value);

        Assert::assertSame($value, $productId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testItThrowsExceptionWhenBeingConstructedWithInvalidValue(int $value): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_ID);

        new ProductId($value);
    }

    public function getValidValues(): iterable
    {
        yield [1];
        yield [100];
        yield [999991];
    }

    public function getInvalidValues(): iterable
    {
        yield [0];
        yield [-5];
    }
}
