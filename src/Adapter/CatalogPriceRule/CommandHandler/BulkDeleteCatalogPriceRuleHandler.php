<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\BulkDeleteCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler\BulkDeleteCatalogPriceRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CannotDeleteCatalogPriceRuleException;

/**
 * Deletes catalog prices rules in bulk action using legacy object model
 */
#[AsCommandHandler]
final class BulkDeleteCatalogPriceRuleHandler extends AbstractCatalogPriceRuleHandler implements BulkDeleteCatalogPriceRuleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteCatalogPriceRuleCommand $command)
    {
        foreach ($catalogPriceRuleId = $command->getCatalogPriceRuleIds() as $catalogPriceRuleId) {
            $specificPriceRule = $this->getSpecificPriceRule($catalogPriceRuleId);

            if (!$this->deleteSpecificPriceRule($specificPriceRule)) {
                throw new CannotDeleteCatalogPriceRuleException(sprintf('Cannot delete SpecificPriceRule object with id "%s".', $catalogPriceRuleId->getValue()), CannotDeleteCatalogPriceRuleException::FAILED_BULK_DELETE);
            }
        }
    }
}
