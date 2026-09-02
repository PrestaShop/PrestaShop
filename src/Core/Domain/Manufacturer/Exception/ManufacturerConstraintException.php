<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception;

/**
 * Is thrown when Manufacturer constraint is violated
 */
class ManufacturerConstraintException extends ManufacturerException
{
    /**
     * When manufacturer id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * When manufacturer status is not valid
     */
    public const INVALID_STATUS = 20;
}
