<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Exception;

/**
 * Is thrown when tax or taxes cannot be updated
 */
class UpdateTaxException extends TaxException
{
    /**
     * When fails to update single tax status
     */
    public const FAILED_UPDATE_STATUS = 10;

    /**
     * When fails to update taxes status on bulk action
     */
    public const FAILED_BULK_UPDATE_STATUS = 20;
}
