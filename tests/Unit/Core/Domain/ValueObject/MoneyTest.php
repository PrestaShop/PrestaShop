<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class MoneyTest extends TestCase
{
    /**
     * @dataProvider getValidDataForCreatingMoneyClass
     *
     * @param string $number
     * @param int $currencyId
     * @param bool $taxIncluded
     *
     * @return void
     */
    public function testItCreatesMoneyClass(
        string $number,
        int $currencyId,
        bool $taxIncluded
    ): void {
        $money = new Money(new DecimalNumber($number), new CurrencyId($currencyId), $taxIncluded);

        $this->assertTrue($money->getAmount()->equals(new DecimalNumber($number)));
        $this->assertSame('', $money->getAmount()->getSign());
        $this->assertSame($currencyId, $money->getCurrencyId()->getValue());
        $this->assertSame($taxIncluded, $money->isTaxIncluded());
    }

    public function getValidDataForCreatingMoneyClass(): iterable
    {
        yield ['100', 10, true];
        yield ['100.5', 5, false];
        yield ['0', 99, true];
    }

    public function testItFailsWhenCreatingWithNegativeValue()
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_MONEY_AMOUNT);
        $this->expectExceptionMessage('Money amount cannot be lower than zero, -100.000000 given');

        new Money(new DecimalNumber('-100'), new CurrencyId(10), false);
    }
}
