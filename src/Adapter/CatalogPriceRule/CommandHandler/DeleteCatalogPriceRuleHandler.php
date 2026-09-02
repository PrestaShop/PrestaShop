<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\DeleteCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\CommandHandler\DeleteCatalogPriceRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CannotDeleteCatalogPriceRuleException;

/**
 * Handles deletion of catalog price rule using legacy object model
 */
#[AsCommandHandler]
final class DeleteCatalogPriceRuleHandler extends AbstractCatalogPriceRuleHandler implements DeleteCatalogPriceRuleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCatalogPriceRuleCommand $command)
    {
        $catalogPriceRuleId = $command->getCatalogPriceRuleId();
        $specificPriceRule = $this->getSpecificPriceRule($catalogPriceRuleId);

        if (!$this->deleteSpecificPriceRule($specificPriceRule)) {
            throw new CannotDeleteCatalogPriceRuleException(sprintf('Cannot delete SpecificPriceRule object with id "%s".', $catalogPriceRuleId->getValue()), CannotDeleteCatalogPriceRuleException::FAILED_DELETE);
        }
    }
}
