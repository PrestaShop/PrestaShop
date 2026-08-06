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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\EditTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandHandler\EditTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Handles editing a tax rule
 */
#[AsCommandHandler]
final class EditTaxRuleHandler implements EditTaxRuleHandlerInterface
{
    public function __construct(
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
        private readonly TaxRuleRepository $taxRuleRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditTaxRuleCommand $command): void
    {
        $taxRule = $this->taxRuleRepository->get($command->getTaxRuleId());
        $groupId = new TaxRulesGroupId((int) $taxRule->id_tax_rules_group);

        // Historize the group if used in orders
        $newGroupId = $this->taxRulesGroupRepository->historizeIfUsed($groupId);

        // Remap the tax rule id if historization occurred
        $taxRuleId = $command->getTaxRuleId();
        if ($newGroupId->getValue() !== $groupId->getValue()) {
            $taxRuleId = $this->taxRulesGroupRepository->remapTaxRuleId($newGroupId, $taxRuleId);
        }

        // Load the (potentially remapped) tax rule
        $taxRule = $this->taxRuleRepository->get($taxRuleId);

        // Apply partial updates
        if (null !== $command->getCountryId()) {
            $taxRule->id_country = $command->getCountryId();
        }
        if (null !== $command->getStateId()) {
            $taxRule->id_state = $command->getStateId();
        }
        if (null !== $command->getTaxId()) {
            $taxRule->id_tax = $command->getTaxId();
        }
        if (null !== $command->getBehavior()) {
            $taxRule->behavior = $command->getBehavior();
        }
        if (null !== $command->getZipCode()) {
            $zipCode = $command->getZipCode();

            if ($zipCode === '' || $zipCode === '0') {
                $taxRule->zipcode_from = '0';
                $taxRule->zipcode_to = '0';
            } else {
                [$taxRule->zipcode_from, $taxRule->zipcode_to] = $taxRule->breakDownZipCode($zipCode);
            }
        }
        if (null !== $command->getDescription()) {
            $taxRule->description = $command->getDescription();
        }

        $this->taxRuleRepository->update($taxRule);
    }
}
