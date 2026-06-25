<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use Context;
use Country;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRuleRepository;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command\AddTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandHandler\AddTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandResult\AddTaxRuleResult;
use TaxRule;

/**
 * Handles adding tax rules to a tax rules group.
 * When countryId is 0, creates rules for all active countries.
 */
#[AsCommandHandler]
final class AddTaxRuleHandler implements AddTaxRuleHandlerInterface
{
    public function __construct(
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
        private readonly TaxRuleRepository $taxRuleRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddTaxRuleCommand $command): AddTaxRuleResult
    {
        // Historize the group if it's used in orders (creates a new copy)
        $newGroupId = $this->taxRulesGroupRepository->historizeIfUsed($command->getTaxRulesGroupId());

        // Determine which countries to create rules for
        $countryIds = $this->resolveCountryIds($command->getCountryId());
        $stateIds = $command->getStateIds();

        $taxRulesGroup = $this->taxRulesGroupRepository->get($newGroupId);

        foreach ($countryIds as $countryId) {
            foreach ($stateIds as $stateId) {
                if ($taxRulesGroup->hasUniqueTaxRuleForCountry($countryId, $stateId)) {
                    continue;
                }

                $this->createTaxRule(
                    $newGroupId->getValue(),
                    $countryId,
                    $stateId,
                    $command
                );
            }
        }

        return new AddTaxRuleResult($newGroupId);
    }

    /**
     * @param int $countryId 0 means all active countries
     *
     * @return int[]
     */
    private function resolveCountryIds(int $countryId): array
    {
        if ($countryId !== 0) {
            return [$countryId];
        }

        $countries = Country::getCountries((int) Context::getContext()->language->id);
        $ids = [];
        foreach ($countries as $country) {
            $ids[] = (int) $country['id_country'];
        }

        return $ids;
    }

    /**
     * @param int $taxRulesGroupId
     * @param int $countryId
     * @param int $stateId
     * @param AddTaxRuleCommand $command
     */
    private function createTaxRule(
        int $taxRulesGroupId,
        int $countryId,
        int $stateId,
        AddTaxRuleCommand $command
    ): void {
        $taxRule = new TaxRule();
        $taxRule->id_tax_rules_group = $taxRulesGroupId;
        $taxRule->id_country = $countryId;
        $taxRule->id_state = $stateId;
        $taxRule->id_tax = $command->getTaxId();
        $taxRule->behavior = $command->getBehavior();
        $taxRule->description = $command->getDescription();

        // Parse zipcode range — empty input means no zip filter (store as 0/0)
        $zipCode = $command->getZipCode();

        if ($zipCode === '' || $zipCode === '0') {
            $taxRule->zipcode_from = '0';
            $taxRule->zipcode_to = '0';
        } else {
            [$zipcodeFrom, $zipcodeTo] = $taxRule->breakDownZipCode($zipCode);
            $taxRule->zipcode_from = $zipcodeFrom;
            $taxRule->zipcode_to = $zipcodeTo;
        }

        $this->taxRuleRepository->add($taxRule);
    }
}
