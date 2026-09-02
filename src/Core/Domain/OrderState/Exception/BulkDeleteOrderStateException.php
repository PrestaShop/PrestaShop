<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Exception;

use Exception;

/**
 * Thrown on failure to delete all selected order states without errors
 */
class BulkDeleteOrderStateException extends OrderStateException
{
    /**
     * @var int[]
     */
    private $orderStatesId;

    /**
     * @param int[] $orderStatesId
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(array $orderStatesId, $message = '', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->orderStatesId = $orderStatesId;
    }

    /**
     * @return int[]
     */
    public function getOrderStatesIds(): array
    {
        return $this->orderStatesId;
    }
}
