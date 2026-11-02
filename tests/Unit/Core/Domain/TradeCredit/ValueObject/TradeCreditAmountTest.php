<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\TradeCredit\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\ValueObject\TradeCreditAmount;

class TradeCreditAmountTest extends TestCase
{
    public function testZeroIsAllowed(): void
    {
        $this->assertSame('0', (string) (new TradeCreditAmount('0'))->getValue());
    }

    public function testPositiveAmountIsKept(): void
    {
        $this->assertTrue((new TradeCreditAmount('50000.00'))->getValue()->equals(new DecimalNumber('50000')));
    }

    public function testNegativeAmountIsRejected(): void
    {
        $this->expectException(TradeCreditConstraintException::class);
        $this->expectExceptionCode(TradeCreditConstraintException::INVALID_AMOUNT);

        new TradeCreditAmount('-0.01');
    }
}
