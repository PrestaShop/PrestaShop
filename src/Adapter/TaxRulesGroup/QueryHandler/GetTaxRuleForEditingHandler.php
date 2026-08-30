<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\QueryHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRuleRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryHandler\GetTaxRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\EditableTaxRule;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Handles query which gets tax rule data for editing
 */
#[AsQueryHandler]
final class GetTaxRuleForEditingHandler implements GetTaxRuleForEditingHandlerInterface
{
    public function __construct(
        private readonly TaxRuleRepository $taxRuleRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetTaxRuleForEditing $query): EditableTaxRule
    {
        $taxRule = $this->taxRuleRepository->get($query->getTaxRuleId());

        return new EditableTaxRule(
            new TaxRuleId((int) $taxRule->id),
            new TaxRulesGroupId((int) $taxRule->id_tax_rules_group),
            (int) $taxRule->id_country,
            (int) $taxRule->id_state,
            (string) $taxRule->zipcode_from,
            (string) $taxRule->zipcode_to,
            (int) $taxRule->id_tax,
            (int) $taxRule->behavior,
            (string) $taxRule->description
        );
    }
}
