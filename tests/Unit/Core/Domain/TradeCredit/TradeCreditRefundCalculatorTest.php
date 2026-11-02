<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\TradeCredit;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditRefundCalculator;

class TradeCreditRefundCalculatorTest extends TestCase
{
    /**
     * @dataProvider refundProvider
     */
    public function testRefundable(string $orderShare, string $alreadyRefunded, string $requested, string $expected): void
    {
        $refundable = (new TradeCreditRefundCalculator())->refundable(
            new DecimalNumber($orderShare),
            new DecimalNumber($alreadyRefunded),
            new DecimalNumber($requested)
        );

        $this->assertTrue(
            $refundable->equals(new DecimalNumber($expected)),
            sprintf('Expected %s, got %s', $expected, $refundable)
        );
    }

    public static function refundProvider(): iterable
    {
        yield 'full refund' => ['300', '0', '300', '300'];
        yield 'partial then full returns remainder' => ['300', '100', '300', '200'];
        yield 'never above share' => ['300', '0', '500', '300'];
        yield 'never negative' => ['300', '350', '10', '0'];
    }
}
