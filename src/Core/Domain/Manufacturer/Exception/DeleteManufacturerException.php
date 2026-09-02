<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception;

/**
 * Is thrown manufacturer or manufacturers cannot be deleted
 */
class DeleteManufacturerException extends ManufacturerException
{
    /**
     * When fails to delete single manufacturer
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete manufacturers in bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
