<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\SpecificPrice\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;

class SpecificPriceIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testItIsSuccessfullyConstructed(int $value): void
    {
        $specificPriceId = new SpecificPriceId($value);

        Assert::assertSame($value, $specificPriceId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $value): void
    {
        $this->expectException(SpecificPriceConstraintException::class);

        new SpecificPriceId($value);
    }

    public function getValidValues(): Generator
    {
        yield [1];
        yield [10];
        yield [5000000001];
    }

    public function getInvalidValues(): Generator
    {
        yield [0];
        yield [-1];
        yield [-999];
    }
}
