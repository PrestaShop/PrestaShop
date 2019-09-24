<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Entity\Repository\TaxRuleRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates tax rule with tax only behavior to be unique
 */
class UniqueTaxRuleBehaviorValidator extends ConstraintValidator
{
    /**
     * @var TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * @var FormChoiceProviderInterface
     */
    private $countryChoiceProvider;

    /**
     * @param TaxRuleRepository $taxRuleRepository
     * @param FormChoiceProviderInterface $countryChoiceProvider
     */
    public function __construct(
        TaxRuleRepository $taxRuleRepository,
        FormChoiceProviderInterface $countryChoiceProvider
    ) {
        $this->taxRuleRepository = $taxRuleRepository;
        $this->countryChoiceProvider = $countryChoiceProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$this->valueExists($value, 'tax_rules_group_id')) {
            throw new TaxRulesGroupConstraintException(
                'Tax rules group id is required for tax rule',
                TaxRulesGroupConstraintException::INVALID_ID
            );
        }

        $countryId = $value['country_id'] ?? CountryId::ALL_COUNTRIES_ID;
        $taxRuleId = $this->valueExists($value, 'tax_rule_id') ? $value['tax_rule_id'] : null;
        $selectedCountryIds = [$countryId];

        if ($countryId === CountryId::ALL_COUNTRIES_ID) {
            $countryChoices = $this->countryChoiceProvider->getChoices();
            $selectedCountryIds = array_map('intval', $countryChoices);
        }

        foreach ($selectedCountryIds as $countryId) {
            $stateIds = $this->getStates($value);

            foreach ($stateIds as $stateId) {
                $taxRuleWithUniqueBehaviorExists = $this->taxRuleRepository->hasUniqueBehaviorTaxRule(
                    $value['tax_rules_group_id'],
                    $countryId,
                    $stateId,
                    $taxRuleId
                );

                if ($taxRuleWithUniqueBehaviorExists) {
                    $this->context->buildViolation($constraint->message)
                        ->atPath('[behavior_id]')
                        ->setTranslationDomain('Admin.International.Notifications')
                        ->addViolation()
                    ;
                }

                return;
            }
        }
    }

    /**
     * @param array $data
     * @param string $key
     *
     * @return int
     */
    private function valueExists(array $data, string $key): int
    {
        return array_key_exists($key, $data) && $data[$key] !== null;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getStates(array $data): array
    {
        return array_key_exists('state_ids', $data) && $data['state_ids'] !== null ?
            $data['state_ids'] :
            [StateId::ALL_STATES_ID];
    }
}
