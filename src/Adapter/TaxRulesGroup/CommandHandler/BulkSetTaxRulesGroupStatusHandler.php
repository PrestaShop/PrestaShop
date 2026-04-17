<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkSetTaxRulesGroupStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\BulkToggleTaxRulesGroupStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;

/**
 * Handles toggling of multiple tax rules groups statuses
 */
#[AsCommandHandler]
final class BulkSetTaxRulesGroupStatusHandler extends AbstractTaxRulesGroupHandler implements BulkToggleTaxRulesGroupStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotBulkUpdateTaxRulesGroupException
     */
    public function handle(BulkSetTaxRulesGroupStatusCommand $command): void
    {
        $errors = [];

        foreach ($command->getTaxRulesGroupIds() as $taxRuleGroupId) {
            try {
                $taxRuleGroup = $this->getTaxRulesGroup($taxRuleGroupId);

                if (!$this->setTaxRulesGroupStatus($taxRuleGroup, $command->getExpectedStatus())) {
                    $errors[] = $taxRuleGroup->id;
                }
            } catch (TaxRulesGroupException) {
                $errors[] = $taxRuleGroupId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new CannotBulkUpdateTaxRulesGroupException($errors, 'Failed to set all tax rules groups statuses without errors');
        }
    }
}
