<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\QueryResult;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentForViewing;

/**
 * The order page offers a Details button on every payment row and fills the panel behind it with the
 * four card columns. Those columns are only written by a payment module that has card data, so for a
 * bank wire or a cheque the panel is four rows of "Not defined".
 */
class OrderPaymentForViewingCardDetailsTest extends TestCase
{
    /**
     * @dataProvider getPayments
     */
    public function testAPaymentKnowsWhetherItHasCardDetails(
        string $cardNumber,
        string $cardBrand,
        string $cardExpiration,
        string $cardHolder,
        bool $expected,
        string $because
    ): void {
        $payment = new OrderPaymentForViewing(
            1,
            new DateTimeImmutable(),
            'Bank wire',
            '',
            '10.00',
            null,
            $cardNumber,
            $cardBrand,
            $cardExpiration,
            $cardHolder
        );

        self::assertSame($expected, $payment->hasCardDetails(), $because);
    }

    public static function getPayments(): array
    {
        return [
            'nothing recorded' => [
                '', '', '', '', false,
                'a bank wire or a cheque records none of these, so there is nothing to show',
            ],
            'only the number' => [
                '1234', '', '', '', true,
                'one field is enough to make the panel worth opening',
            ],
            'only the brand' => [
                '', 'Visa', '', '', true,
                'the check must not look at the number alone',
            ],
            'only the expiration' => [
                '', '', '10/30', '', true,
                'the check must not look at the number alone',
            ],
            'only the holder' => [
                '', '', '', 'John Doe', true,
                'the check must not look at the number alone',
            ],
            'everything recorded' => [
                '1234', 'Visa', '10/30', 'John Doe', true,
                'the normal card payment case',
            ],
        ];
    }
}
