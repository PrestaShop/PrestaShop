<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;

/**
 * A product that costs nothing refunds nothing, and refusing a zero amount made it impossible to take one
 * back into stock through a partial refund. Only a negative amount is meaningless.
 */
class OrderDetailRefundTest extends TestCase
{
    public function testAFreeProductCanBeRefunded(): void
    {
        $refund = OrderDetailRefund::createPartialRefund(1, 10, '0');

        self::assertSame(1, $refund->getOrderDetailId());
        self::assertSame(10, $refund->getProductQuantity());
        self::assertTrue($refund->getRefundedAmount()->equalsZero());
    }

    /**
     * @dataProvider getAmountsThatCostSomething
     */
    public function testAnAmountIsStillAccepted(string $amount): void
    {
        $refund = OrderDetailRefund::createPartialRefund(1, 1, $amount);

        self::assertTrue($refund->getRefundedAmount()->isGreaterThanZero());
    }

    /**
     * @dataProvider getAmountsThatMakeNoSense
     */
    public function testANegativeAmountIsStillRefused(string $amount): void
    {
        $this->expectException(InvalidCancelProductException::class);

        OrderDetailRefund::createPartialRefund(1, 1, $amount);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function getAmountsThatCostSomething(): iterable
    {
        yield 'a round amount' => ['10'];
        yield 'a fractional amount' => ['0.01'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function getAmountsThatMakeNoSense(): iterable
    {
        yield 'negative' => ['-1'];
        yield 'a small negative amount' => ['-0.01'];
    }
}
