<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
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
        $choices = [];
        foreach ($this->getRules() as $rule) {
            $choices[$rule['name']] = (int) $rule['id_tax_rules_group'];
        }

        return $choices;
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
