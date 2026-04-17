<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Exception;

/**
 * Is thrown when address constraint is violated
 */
class AddressConstraintException extends AddressException
{
    /**
     * When address id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * When manufacturer id provided for address is not valid
     */
    public const INVALID_MANUFACTURER_ID = 20;

    /**
     * When one or more unspecified fields in address are invalid
     */
    public const INVALID_REQUIRED_FIELDS = 30;
}
