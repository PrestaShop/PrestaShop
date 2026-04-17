<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Stock\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\MovementReasonConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\MovementReasonId;

class MovementReasonIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testItIsSuccessfullyConstructed(int $value): void
    {
        $specificPriceId = new MovementReasonId($value);

        Assert::assertSame($value, $specificPriceId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $value): void
    {
        $this->expectException(MovementReasonConstraintException::class);

        new MovementReasonId($value);
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
