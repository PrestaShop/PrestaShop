<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;

class UpcTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $reference = new Upc('12345678901');
        Assert::assertSame('12345678901', $reference->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(string $value): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_UPC);

        new Upc($value);
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield ['1234567890013'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['-'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ['\''];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }
}
