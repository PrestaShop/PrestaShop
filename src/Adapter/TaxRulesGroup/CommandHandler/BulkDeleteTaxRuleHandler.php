<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRuleRepository;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\BulkDeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandHandler\BulkDeleteTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotBulkDeleteTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleException;

/**
 * Handles bulk deletion of tax rules within a group
 */
#[AsCommandHandler]
final class BulkDeleteTaxRuleHandler implements BulkDeleteTaxRuleHandlerInterface
{
    public function __construct(
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
        private readonly TaxRuleRepository $taxRuleRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteTaxRuleCommand $command): void
    {
        // Historize the group once for the entire bulk operation
        $newGroupId = $this->taxRulesGroupRepository->historizeIfUsed($command->getTaxRulesGroupId());
        $historized = $newGroupId->getValue() !== $command->getTaxRulesGroupId()->getValue();

        $errors = [];

        foreach ($command->getTaxRuleIds() as $taxRuleId) {
            try {
                $effectiveId = $taxRuleId;
                if ($historized) {
                    $effectiveId = $this->taxRulesGroupRepository->remapTaxRuleId($newGroupId, $taxRuleId);
                }

                $taxRule = $this->taxRuleRepository->get($effectiveId);
                $this->taxRuleRepository->delete($taxRule);
            } catch (TaxRuleException) {
                $errors[] = $taxRuleId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new CannotBulkDeleteTaxRuleException(
                $errors,
                'Failed to delete all tax rules without errors'
            );
        }
    }
}
