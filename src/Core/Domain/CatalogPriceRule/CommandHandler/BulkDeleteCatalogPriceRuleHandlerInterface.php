<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\BulkDeleteCatalogPriceRuleCommand;

/**
 * Defines contract for BulkDeleteCatalogPriceRuleHandler
 */
interface BulkDeleteCatalogPriceRuleHandlerInterface
{
    /**
     * @param BulkDeleteCatalogPriceRuleCommand $command
     */
    public function handle(BulkDeleteCatalogPriceRuleCommand $command);
}
