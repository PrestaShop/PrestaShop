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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\DeleteTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandHandler\DeleteTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Handles deletion of a single tax rule
 */
#[AsCommandHandler]
final class DeleteTaxRuleHandler implements DeleteTaxRuleHandlerInterface
{
    public function __construct(
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
        private readonly TaxRuleRepository $taxRuleRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteTaxRuleCommand $command): void
    {
        $taxRule = $this->taxRuleRepository->get($command->getTaxRuleId());
        $groupId = new TaxRulesGroupId((int) $taxRule->id_tax_rules_group);

        // Historize the group if used in orders
        $newGroupId = $this->taxRulesGroupRepository->historizeIfUsed($groupId);

        // Remap and reload if historization occurred
        $taxRuleId = $command->getTaxRuleId();
        if ($newGroupId->getValue() !== $groupId->getValue()) {
            $taxRuleId = $this->taxRulesGroupRepository->remapTaxRuleId($newGroupId, $taxRuleId);
        }

        $taxRule = $this->taxRuleRepository->get($taxRuleId);
        $this->taxRuleRepository->delete($taxRule);
    }
}
