<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\DeleteCatalogPriceRuleCommand;

/**
 * Defines contract for delete catalog price rule handler
 */
interface DeleteCatalogPriceRuleHandlerInterface
{
    /**
     * @param DeleteCatalogPriceRuleCommand $command
     */
    public function handle(DeleteCatalogPriceRuleCommand $command);
}
