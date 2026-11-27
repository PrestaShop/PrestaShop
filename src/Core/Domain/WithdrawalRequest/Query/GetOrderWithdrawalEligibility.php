<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Tells whether the consumer can still withdraw from an order through the withdrawal function, and until when.
 */
final class GetOrderWithdrawalEligibility
{
    private OrderId $orderId;

    private DateTimeImmutable $checkedAt;

    public function __construct(int $orderId, ?DateTimeImmutable $checkedAt = null)
    {
        $this->orderId = new OrderId($orderId);
        $this->checkedAt = $checkedAt ?? new DateTimeImmutable();
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getCheckedAt(): DateTimeImmutable
    {
        return $this->checkedAt;
    }
}
