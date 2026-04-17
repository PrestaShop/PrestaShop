<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

class ReductionTest extends TestCase
{
    /**
     * @dataProvider getValidValuesForClassCreation
     *
     * @param string $type
     * @param string $value
     */
    public function testItCreatesClassWithValidValues(string $type, string $value): void
    {
        $reduction = new Reduction($type, $value);

        Assert::assertEquals($reduction->getType(), $type);
        Assert::assertTrue($reduction->getValue()->equals(new DecimalNumber($value)));
    }

    /**
     * @dataProvider getInvalidTypes
     *
     * @param string $type
     */
    public function testItThrowsExceptionWhenInvalidTypeIsProvided(string $type): void
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_REDUCTION_TYPE);

        new Reduction($type, '10');
    }

    /**
     * @dataProvider getInvalidPercentages
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidPercentageIsProvided(string $value): void
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_REDUCTION_PERCENTAGE);

        new Reduction('percentage', $value);
    }

    /**
     * @dataProvider getInvalidAmounts
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidAmountIsProvided(string $value): void
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_REDUCTION_AMOUNT);

        new Reduction('amount', $value);
    }

    /**
     * @return Generator
     */
    public function getInvalidTypes(): Generator
    {
        yield ['random'];
        yield ['NOK'];
        yield ['AMOUNT'];
        yield ['Percentage'];
        yield ['percent'];
    }

    /**
     * @return Generator
     */
    public function getInvalidAmounts(): Generator
    {
        yield ['-10'];
        yield ['-0.33'];
    }

    /**
     * @return Generator
     */
    public function getInvalidPercentages(): Generator
    {
        yield ['-10'];
        yield ['200'];
    }

    /**
     * @return Generator
     */
    public function getValidValuesForClassCreation(): Generator
    {
        yield ['amount', '10'];
        yield ['amount', '0.33'];
        yield ['amount', '0'];
        yield ['percentage', '15'];
        yield ['percentage', '0.45'];
        yield ['percentage', '100'];
        yield ['percentage', '0'];
    }
}
