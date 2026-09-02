<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Exception;

/**
 * Is thrown when address or addresses cannot be deleted
 */
class DeleteAddressException extends AddressException
{
    /**
     * When fails to delete single address
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete address in bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
