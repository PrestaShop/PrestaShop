<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception;

/**
 * Thrown when requested credit slip/slips are not found
 */
class CreditSlipNotFoundException extends CreditSlipException
{
    /**
     * Thrown when no credit slips are found when querying by specific date range
     */
    public const BY_DATE_RANGE = 1;
}
