<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Is thrown when order state is not found
 */
class OrderStateNotFoundException extends OrderStateException
{
    /**
     * @var OrderStateId
     */
    private $orderStateId;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(OrderStateId $orderStateId, $message = '', $code = 0, $previous = null)
    {
        $this->orderStateId = $orderStateId;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return OrderStateId
     */
    public function getOrderStateId()
    {
        return $this->orderStateId;
    }
}
