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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Gtin;

class GtinTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $gtin = new Gtin('97802013796212');
        Assert::assertSame('97802013796212', $gtin->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(string $value): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_GTIN);

        new Gtin($value);
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield ['123456789012345'];
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
