<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Exception;

class CannotUpdateCarrierException extends CarrierException
{
    /**
     * When generic carrier update fails
     */
    public const FAILED_UPDATE_CARRIER = 1;
}
