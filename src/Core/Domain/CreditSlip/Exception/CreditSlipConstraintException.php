<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception;

/**
 * Is thrown when credit slip constraints are violated
 */
class CreditSlipConstraintException extends CreditSlipException
{
    /**
     * When id value is not valid
     */
    public const INVALID_ID = 10;
}
