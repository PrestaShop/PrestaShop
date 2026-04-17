<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\AddCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;

/**
 * Interface for handling addCatalogPriceRule command
 */
interface AddCatalogPriceRuleHandlerInterface
{
    /**
     * @param AddCatalogPriceRuleCommand $command
     *
     * @return CatalogPriceRuleId
     */
    public function handle(AddCatalogPriceRuleCommand $command): CatalogPriceRuleId;
}
