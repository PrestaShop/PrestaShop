<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;

/**
 * One trade credit operation, as shown in the customer history.
 */
class TradeCreditOperation
{
    public function __construct(
        private readonly DateTimeImmutable $date,
        private readonly string $type,
        private readonly DecimalNumber $amount,
        private readonly DecimalNumber $balanceAfter,
        private readonly ?int $orderId
    ) {
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    public function getBalanceAfter(): DecimalNumber
    {
        return $this->balanceAfter;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }
}
