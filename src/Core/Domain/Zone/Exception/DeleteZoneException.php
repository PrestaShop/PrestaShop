<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\Exception;

/**
 * Is raised when zone or zones cannot be deleted
 */
class DeleteZoneException extends ZoneException
{
    /**
     * When fails to delete single zone
     */
    public const FAILED_DELETE = 1;

    /**
     * When fails to delete zones in bulk actions
     */
    public const FAILED_BULK_DELETE = 2;
}
