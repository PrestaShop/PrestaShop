<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use State;
use TaxRulesGroup;

/**
 * Provides tax rule group choices with tax rule name as key and id as value
 */
final class TaxRuleGroupChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var int
     */
    private $countryId;

    /**
     * @var TaxRulesGroupRepository
     */
    private $taxRulesGroupRepository;

    /**
     * @var TaxComputer
     */
    private $taxComputer;

    public function __construct(int $countryId, TaxRulesGroupRepository $taxRulesGroupRepository, TaxComputer $taxComputer)
    {
        $this->countryId = $countryId;
        $this->taxRulesGroupRepository = $taxRulesGroupRepository;
        $this->taxComputer = $taxComputer;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->getRules(),
            'id_tax_rules_group',
            'name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesAttributes(): array
    {
        $taxRates = [];
        foreach ($this->getRules() as $rule) {
            $taxRulesGroupId = new TaxRulesGroupId((int) $rule['id_tax_rules_group']);
            $stateId = $this->taxRulesGroupRepository->getTaxRulesGroupDefaultStateId($taxRulesGroupId, new CountryId($this->countryId));
            if (!$stateId) {
                $taxRate = $this->taxComputer->getTaxRate($taxRulesGroupId, new CountryId($this->countryId));
                $stateIsoCode = '';
            } else {
                $taxRate = $this->taxComputer->getTaxRateByState($taxRulesGroupId, new CountryId($this->countryId), new StateId($stateId));
                $state = new State($stateId);
                $stateIsoCode = $state->iso_code;
            }
            $taxRates[$rule['name']] = [
                'data-tax-rate' => (string) $taxRate,
                'data-state-iso-code' => $stateIsoCode,
            ];
        }

        return $taxRates;
    }

    /**
     * @return array
     */
    private function getRules(): array
    {
        return TaxRulesGroup::getTaxRulesGroupsForOptions();
    }
}
