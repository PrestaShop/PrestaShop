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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\UpdateTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\UpdateTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRuleForCountries;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRuleForCountryStates;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TaxRule;

/**
 * Handles tax rule updating and bulk create if needed
 */
final class UpdateTaxRuleHandler extends AbstractTaxRulesGroupHandler implements UpdateTaxRuleHandlerInterface
{
    /**
     * @var int
     */
    private $langId;

    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @param ValidatorInterface $validator
     * @param LegacyContext $context
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        ValidatorInterface $validator,
        LegacyContext $context,
        CountryDataProvider $countryDataProvider
    ) {
        parent::__construct($validator);

        $this->langId = $context->getLanguage()->id;
        $this->countryDataProvider = $countryDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateTaxRuleException
     * @throws CannotUpdateTaxRuleForCountries
     * @throws CannotUpdateTaxRuleForCountryStates
     * @throws TaxRuleConstraintException
     */
    public function handle(UpdateTaxRuleCommand $command): void
    {
        try {
            $taxRule = new TaxRule($command->getTaxRuleId()->getValue());
        } catch (PrestaShopException $e) {
            throw new CannotUpdateTaxRuleException([]);
        }

        $taxRulesGroupId = (int) $taxRule->id_tax_rules_group;
        $zipCode = $command->getZipCode();
        $taxId = $command->getTaxId() !== null ? $command->getTaxId()->getValue() : TaxId::NO_TAX_ID;
        $behavior = $command->getBehavior()->getValue();
        $description = $command->getDescription();

        $taxRule->behavior = $behavior;
        $taxRule->id_tax = $taxId;
        $taxRule->description = $description;

        $selectedCountryIds = $this->getCountryIdsForTaxRule(
            $this->countryDataProvider,
            $this->langId,
            $command->getCountryId()
        );

        $stateIds = $command->getStateIds();

        $countriesWithErrors = [];

        foreach ($selectedCountryIds as $countryKey => $countryId) {
            $statesWithErrors = [];

            /** @var StateId $stateId */
            foreach ($stateIds as $stateKey => $stateId) {
                $error = false;
                try {
                    $violationOfUniqueTaxRuleForACountryRule = $this->assertUniqueBehaviorTaxRuleForCountry(
                        $taxRulesGroupId,
                        $countryId,
                        $stateId->getValue(),
                        $taxRule->id
                    );

                    if ($violationOfUniqueTaxRuleForACountryRule) {
                        $error = true;
                    }

                    if (null !== $command->getZipCode()) {
                        $isInvalid = $this->assertIsValidZipCode(
                            $zipCode,
                            $countryId
                        );

                        if ($isInvalid) {
                            $error = true;
                        }
                    }

                    $taxRule->id_country = $countryId;
                    $taxRule->id_state = $stateId->getValue();
                    list($taxRule->zipcode_from, $taxRule->zipcode_to) = $taxRule->breakDownZipCode($zipCode);

                    try {
                        $this->validateTaxRuleFields($taxRule);
                    } catch (TaxRuleConstraintException $e) {
                        $error = true;
                    }

                    reset($selectedCountryIds);
                    reset($stateIds);

                    // This is done to determine if this is first iteration of foreach cycle to know if
                    // tax rule should be updated or created to prevent overriding of tax rule that is being updated
                    // if on update more states or countries were added.
                    if ($countryKey === key($selectedCountryIds) && $stateKey === key($stateIds)) {
                        if (!$error && false === $taxRule->update()) {
                            $error = true;
                        }
                    } else {
                        $taxRuleCreate = clone $taxRule;

                        if (!$error && false === $taxRuleCreate->add()) {
                            $error = true;
                        }
                    }
                } catch (PrestaShopException $e) {
                    $error = true;
                }

                if ($error) {
                    $statesWithErrors[] = $stateId->getValue();
                }
            }

            if (!empty($statesWithErrors)) {
                $countriesWithErrors[$countryId] = $statesWithErrors;
            }
        }

        if (!empty($countriesWithErrors)) {
            // If there is only one country with errors, exception for states is thrown
            if (count($countriesWithErrors) === 1) {
                throw new CannotUpdateTaxRuleForCountryStates(
                    array_key_first($countriesWithErrors),
                    $countriesWithErrors[array_key_first($countriesWithErrors)]
                );
            }

            throw new CannotUpdateTaxRuleForCountries(array_keys($countriesWithErrors));
        }
    }
}
