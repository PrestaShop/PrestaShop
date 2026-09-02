<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\EditableTaxRule;

/**
 * Provides data for tax rule add/edit form.
 */
class TaxRuleFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /** @var EditableTaxRule $editableTaxRule */
        $editableTaxRule = $this->queryBus->handle(new GetTaxRuleForEditing($id));

        $from = $editableTaxRule->getZipcodeFrom();
        $to = $editableTaxRule->getZipcodeTo();

        $zipcode = '';
        if ($from !== '' && $from !== '0') {
            $zipcode = $from;
            if ($to !== '' && $to !== '0') {
                $zipcode .= '-' . $to;
            }
        }

        return [
            'country' => $editableTaxRule->getCountryId(),
            'state' => $editableTaxRule->getStateId(),
            'zipcode' => $zipcode,
            'behavior' => $editableTaxRule->getBehavior(),
            'tax' => $editableTaxRule->getTaxId(),
            'description' => $editableTaxRule->getDescription(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'tax_rules_group_id' => 0,
            'country' => 0,
            'state' => null,
            'zipcode' => '',
            'behavior' => 0,
            'tax' => 0,
            'description' => '',
        ];
    }
}
