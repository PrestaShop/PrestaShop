<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception;

/**
 * Is thrown when SqlRequest cannot be deleted
 */
class CannotDeleteSqlRequestException extends SqlRequestException
{
    /**
     * When deleting single SqlRequest
     */
    public const CANNOT_SINGLE_DELETE = 10;

    /**
     * When deleting SqlRequest in bulk action
     */
    public const CANNOT_BULK_DELETE = 20;
}
