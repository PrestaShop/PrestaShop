<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception;

/**
 * Thrown when trade credit data is invalid.
 */
class TradeCreditConstraintException extends TradeCreditException
{
    /**
     * @var int Used when a negative trade credit amount is provided
     */
    public const INVALID_AMOUNT = 1;
}
