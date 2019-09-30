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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueTaxRuleBehavior;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ZipCodeRange;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotDeleteTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TaxRule;
use TaxRulesGroup;

/**
 * Provides common methods for tax rules group handlers
 */
abstract class AbstractTaxRulesGroupHandler
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Gets legacy TaxRuleGroup object
     *
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @return TaxRulesGroup
     *
     * @throws TaxRulesGroupNotFoundException
     */
    protected function getTaxRulesGroup(TaxRulesGroupId $taxRulesGroupId): TaxRulesGroup
    {
        $taxRulesGroupIdValue = $taxRulesGroupId->getValue();

        try {
            $taxRulesGroup = new TaxRulesGroup($taxRulesGroupIdValue);
        } catch (PrestaShopException $e) {
            throw new TaxRulesGroupNotFoundException(
                sprintf('Tax rules group with id "%s" was not found.', $taxRulesGroupIdValue)
            );
        }

        if ($taxRulesGroup->id !== $taxRulesGroupIdValue) {
            throw new TaxRulesGroupNotFoundException(
                sprintf('Tax rules group with id "%s" was not found.', $taxRulesGroupIdValue)
            );
        }

        return $taxRulesGroup;
    }

    /**
     * Gets legacy TaxRule object
     *
     * @param TaxRuleId $taxRuleId
     *
     * @return TaxRule
     *
     * @throws TaxRuleNotFoundException
     */
    protected function getTaxRule(TaxRuleId $taxRuleId): TaxRule
    {
        $taxRuleIdValue = $taxRuleId->getValue();

        try {
            $taxRule = new TaxRule($taxRuleIdValue);
        } catch (PrestaShopException $e) {
            throw new TaxRuleNotFoundException(
                sprintf('Tax rule with id "%s" was not found.', $taxRuleIdValue)
            );
        }

        if ($taxRule->id !== $taxRuleIdValue) {
            throw new TaxRuleNotFoundException(
                sprintf('Tax rule with id "%s" was not found.', $taxRuleIdValue)
            );
        }

        return $taxRule;
    }

    /**
     * Deletes legacy TaxRule
     *
     * @param TaxRule $taxRule
     *
     * @return bool
     *
     * @throws CannotDeleteTaxRuleException
     */
    protected function deleteTaxRule(TaxRule $taxRule): bool
    {
        try {
            return $taxRule->delete();
        } catch (PrestaShopException $e) {
            throw new CannotDeleteTaxRuleException(
                sprintf('An error occurred when deleting tax rule object with id "%s".', $taxRule->id)
            );
        }
    }

    /**
     * @param TaxRulesGroup $taxRulesGroup
     *
     * @throws TaxRulesGroupConstraintException
     * @throws PrestaShopException
     */
    protected function validateTaxRulesGroupFields(TaxRulesGroup $taxRulesGroup): void
    {
        if (!$taxRulesGroup->validateFields(false)) {
            throw new TaxRulesGroupConstraintException(
                'Tax rules group contains invalid field values'
            );
        }
    }

    /**
     * @param TaxRule $taxRule
     *
     * @throws PrestaShopException
     * @throws TaxRuleConstraintException
     */
    protected function validateTaxRuleFields(TaxRule $taxRule): void
    {
        if (!$taxRule->validateFields(false)) {
            throw new TaxRuleConstraintException(
                'Tax rule contains invalid field values'
            );
        }
    }

    /**
     * Checks if country with tax only behavior is already registered in tax rule group
     *
     * @param int $taxRulesGroupId
     * @param int $countryId
     * @param int $stateId
     * @param int|null $taxRuleId
     *
     * @return bool
     */
    protected function assertUniqueBehaviorTaxRuleForCountry(
        int $taxRulesGroupId,
        int $countryId,
        int $stateId,
        ?int $taxRuleId = null
    ): bool {
        $errors = $this->validator->validate([
            'tax_rules_group_id' => $taxRulesGroupId,
            'country_id' => $countryId,
            'state_ids' => [$stateId],
            'tax_rule_id' => $taxRuleId,
        ], new UniqueTaxRuleBehavior());

        return 0 !== count($errors);
    }

    /**
     * @param string $zipCode
     * @param int $countryId
     *
     * @return bool
     */
    protected function assertIsValidZipCode(
        string $zipCode,
        int $countryId
    ): bool {
        $errors = $this->validator->validate([
            'zip_code' => $zipCode,
            'country_id' => $countryId,
        ], new ZipCodeRange());

        return 0 !== count($errors);
    }

    /**
     * @param CountryDataProvider $countryDataProvider
     * @param int $langId
     * @param CountryId|null $countryId
     *
     * @return int[]
     *
     * @throws TaxRuleConstraintException
     */
    protected function getCountryIdsForTaxRule(
        CountryDataProvider $countryDataProvider,
        int $langId,
        ?CountryId $countryId
    ): array {
        return $countryId !== null && $countryId->getValue() > 0 ?
            $this->getSelectedCountryId($countryId) :
            $this->getAllCountryIdsByLanguage($countryDataProvider, $langId)
        ;
    }

    /**
     * @param CountryDataProvider $countryDataProvider
     * @param int $langId
     *
     * @return int[]
     *
     * @throws TaxRuleConstraintException
     */
    private function getAllCountryIdsByLanguage(
        CountryDataProvider $countryDataProvider,
        int $langId
    ): array {
        try {
            $countries = $countryDataProvider->getCountries($langId);
            $selectedCountryIds = array_map(function (array $countries) {
                return (int) $countries['id_country'];
            }, $countries);
        } catch (PrestaShopException $e) {
            throw new TaxRuleConstraintException(
                'Countries for tax rule creation failed to load',
                TaxRuleConstraintException::FAILED_TO_LOAD_COUNTRIES
            );
        }

        return $selectedCountryIds;
    }

    /**
     * @param CountryId $countryId
     *
     * @return int[]
     */
    private function getSelectedCountryId(CountryId $countryId): array
    {
        return [$countryId->getValue()];
    }
}
