<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

/**
 * Used in order page view to display 'linked orders': orders linked
 * to the order being displayed
 *
 * Two orders are linked if they are the result of an Order Split
 */
class LinkedOrderForViewing
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $statusName;

    /**
     * @var string
     */
    private $amount;

    /**
     * @param int $orderId
     * @param string $statusName
     * @param string $amount
     */
    public function __construct(int $orderId, string $statusName, string $amount)
    {
        $this->orderId = $orderId;
        $this->statusName = $statusName;
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return $this->statusName;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }
}
