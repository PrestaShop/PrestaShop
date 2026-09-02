<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Exception;

/**
 * Is thrown when tax or taxes cannot be deleted
 */
class DeleteTaxException extends TaxException
{
    /**
     * When fails to delete single tax
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete taxes on bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
