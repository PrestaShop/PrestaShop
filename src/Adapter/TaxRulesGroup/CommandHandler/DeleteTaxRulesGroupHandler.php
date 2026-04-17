<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\DeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\DeleteTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;

/**
 * Handles deletion of single tax rules group
 */
#[AsCommandHandler]
final class DeleteTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements DeleteTaxRulesGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteTaxRulesGroupException
     * @throws TaxRulesGroupNotFoundException
     */
    public function handle(DeleteTaxRulesGroupCommand $command): void
    {
        $taxRulesGroup = $this->getTaxRulesGroup($command->getTaxRulesGroupId());

        if (!$this->deleteTaxRulesGroup($taxRulesGroup)) {
            throw new CannotDeleteTaxRulesGroupException(sprintf('Cannot delete tax rules group object with id "%s".', $taxRulesGroup->id));
        }
    }
}
