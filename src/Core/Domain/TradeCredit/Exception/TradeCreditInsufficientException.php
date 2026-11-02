<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception;

use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailability;
use Throwable;

/**
 * Thrown when an order cannot be charged to the customer trade credit.
 */
class TradeCreditInsufficientException extends TradeCreditException
{
    public function __construct(
        private readonly TradeCreditAvailability $availability,
        string $message = '',
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getAvailability(): TradeCreditAvailability
    {
        return $this->availability;
    }
}
