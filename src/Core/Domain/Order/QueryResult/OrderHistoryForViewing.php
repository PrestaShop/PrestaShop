<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderHistoryForViewing
{
    /**
     * @var OrderStatusForViewing[]
     */
    private $statuses = [];

    /**
     * @var int
     */
    private $currentOrderStatusId;

    /**
     * @param int $currentOrderStatusId
     * @param OrderStatusForViewing[] $statuses
     */
    public function __construct(int $currentOrderStatusId, array $statuses)
    {
        $this->currentOrderStatusId = $currentOrderStatusId;

        foreach ($statuses as $status) {
            $this->add($status);
        }
    }

    /**
     * @return OrderStatusForViewing[]
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * @return int
     */
    public function getCurrentOrderStatusId(): int
    {
        return $this->currentOrderStatusId;
    }

    /**
     * @param OrderStatusForViewing $orderStatusForViewing
     */
    private function add(OrderStatusForViewing $orderStatusForViewing): void
    {
        $this->statuses[] = $orderStatusForViewing;
    }
}
