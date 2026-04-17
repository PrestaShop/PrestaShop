<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception;

/**
 * Is thrown when catalog price rule cannot be deleted
 */
class CannotDeleteCatalogPriceRuleException extends CatalogPriceRuleException
{
    /**
     * When fails to delete single catalog price rule
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete catalog price rule in bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
