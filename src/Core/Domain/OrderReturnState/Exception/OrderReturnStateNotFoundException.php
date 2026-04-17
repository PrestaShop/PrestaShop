<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Is thrown when order state is not found
 */
class OrderReturnStateNotFoundException extends OrderReturnStateException
{
    /**
     * @var OrderReturnStateId
     */
    private $orderReturnStateId;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(OrderReturnStateId $orderReturnStateId, $message = '', $code = 0, $previous = null)
    {
        $this->orderReturnStateId = $orderReturnStateId;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId()
    {
        return $this->orderReturnStateId;
    }
}
