<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception;

use Exception;

/**
 * Thrown on failure to delete all selected order return states without errors
 */
class BulkDeleteOrderReturnStateException extends OrderReturnStateException
{
    /**
     * @var int[]
     */
    private $orderReturnStatesId;

    /**
     * @param int[] $orderReturnStatesId
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(array $orderReturnStatesId, string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->orderReturnStatesId = $orderReturnStatesId;
    }

    /**
     * @return int[]
     */
    public function getOrderReturnStatesIds(): array
    {
        return $this->orderReturnStatesId;
    }
}
