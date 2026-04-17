<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Exception;

/**
 * Thrown when cannot delete carrier(s)
 */
class CannotDeleteCarrierException extends CarrierException
{
    /**
     * Thrown when cannot delete single carrier.
     */
    public const SINGLE_DELETE = 10;

    /**
     * Thrown when cannot bulk delete carriers.
     */
    public const BULK_DELETE = 20;
}
