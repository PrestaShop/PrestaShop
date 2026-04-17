<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\EditCatalogPriceRuleCommand;

/**
 * Defines contract for CatalogPriceRuleHandler
 */
interface EditCatalogPriceRuleHandlerInterface
{
    /**
     * @param EditCatalogPriceRuleCommand $command
     */
    public function handle(EditCatalogPriceRuleCommand $command);
}
