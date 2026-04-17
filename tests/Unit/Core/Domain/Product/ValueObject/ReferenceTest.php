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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;

class ReferenceTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $reference = new Reference('ref5-01');
        Assert::assertSame('ref5-01', $reference->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(string $value): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_REFERENCE);

        new Reference($value);
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield ['123456789012345678901234567890123456789012345678901234567890---65'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['<'];
        yield ['>'];
        yield [';'];
    }
}
