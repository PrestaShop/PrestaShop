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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;

class IsbnTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $isbn = new Isbn('978-3-16-148410-0');
        Assert::assertSame('978-3-16-148410-0', $isbn->getValue());
        $isbn = new Isbn('0-8044-2957-X');
        Assert::assertSame('0-8044-2957-X', $isbn->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(string $value): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_ISBN);

        new Isbn($value);
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield ['123456789123456789123456789-33'];
        yield ['978-3-16-144100-X']; // X is not valid for ISBN 13
        yield ['0-8044-29X7-X']; // X not at the end is not valid for ISBN 10
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
