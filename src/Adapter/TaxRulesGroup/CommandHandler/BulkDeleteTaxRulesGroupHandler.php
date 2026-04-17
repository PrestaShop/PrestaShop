<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\BulkDeleteTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;

/**
 * Handles multiple tax rules group deletion
 */
#[AsCommandHandler]
final class BulkDeleteTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements BulkDeleteTaxRulesGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotBulkDeleteTaxRulesGroupException
     */
    public function handle(BulkDeleteTaxRulesGroupCommand $command): void
    {
        $errors = [];

        foreach ($command->getTaxRulesGroupIds() as $taxRulesGroupId) {
            try {
                $taxRulesGroup = $this->getTaxRulesGroup($taxRulesGroupId);

                if (!$this->deleteTaxRulesGroup($taxRulesGroup)) {
                    $errors[] = $taxRulesGroup->id;
                }
            } catch (TaxRulesGroupException) {
                $errors[] = $taxRulesGroupId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new CannotBulkDeleteTaxRulesGroupException($errors, 'Failed to delete all tax rules groups without errors');
        }
    }
}
