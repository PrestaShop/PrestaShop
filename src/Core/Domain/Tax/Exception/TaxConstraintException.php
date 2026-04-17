<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Exception;

/**
 * Is thrown when tax is invalid
 */
class TaxConstraintException extends TaxException
{
    /**
     * Thrown when provided tax id value is not valid
     */
    public const INVALID_ID = 10;

    /**
     * Thrown when provided tax status value is not valid
     */
    public const INVALID_STATUS = 20;
}
