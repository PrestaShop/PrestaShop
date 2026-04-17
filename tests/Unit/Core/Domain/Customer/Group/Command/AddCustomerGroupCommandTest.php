<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Customer\Group\Command;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;

class AddCustomerGroupCommandTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param DecimalNumber $reductionPercent
     */
    public function testItWorksWhenProvidingValidReduction(DecimalNumber $reductionPercent): void
    {
        new AddCustomerGroupCommand(
            ['toto', 'tata'],
            $reductionPercent,
            false,
            true,
            [1]
        );
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param DecimalNumber $reductionPercent
     */
    public function testItThrowsExceptionWhenProvidingInvalidReduction(DecimalNumber $reductionPercent): void
    {
        $this->expectException(GroupConstraintException::class);

        new AddCustomerGroupCommand(
            ['toto', 'tata'],
            $reductionPercent,
            false,
            true,
            [1]
        );
    }

    /**
     * @return Generator
     */
    public function getValidValues(): Generator
    {
        yield [new DecimalNumber('-0.00')];
        yield [new DecimalNumber('-0')];
        yield [new DecimalNumber('0')];
        yield [new DecimalNumber('0.001')];
        yield [new DecimalNumber('0.01')];
        yield [new DecimalNumber('1.23')];
        yield [new DecimalNumber('12.34')];
        yield [new DecimalNumber('99.99')];
        yield [new DecimalNumber('99.999')];
        yield [new DecimalNumber('100')];
        yield [new DecimalNumber('100.0')];
        yield [new DecimalNumber('100.00')];
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield [new DecimalNumber('-0.001')];
        yield [new DecimalNumber('-0.01')];
        yield [new DecimalNumber('100.01')];
        yield [new DecimalNumber('100.001')];
    }
}
