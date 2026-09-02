<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;
use Throwable;

/**
 * Thrown when order message is not found
 */
class OrderMessageNotFoundException extends OrderMessageException
{
    /**
     * @var OrderMessageId
     */
    private $orderMessageId;

    /**
     * @param OrderMessageId $orderMessageId
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        OrderMessageId $orderMessageId,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->orderMessageId = $orderMessageId;
    }

    /**
     * @return OrderMessageId
     */
    public function getOrderMessageId(): OrderMessageId
    {
        return $this->orderMessageId;
    }
}
