<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
use PrestaShop\PrestaShop\Adapter\Country\CountryNotFoundException;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\UpdateTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\UpdateTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRuleException;
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
     * @throws CountryNotFoundException
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
        $taxId = $command->getTaxId() !== null ? $command->getTaxId()->getValue() : 0;
        $behavior = $command->getBehaviorId()->getValue();
        $description = $command->getDescription();

        $taxRule->behavior = $behavior;
        $taxRule->id_tax = $taxId;
        $taxRule->description = $description;

        $selectedCountries = $this->getCountryForTaxRule(
            $this->countryDataProvider,
            $this->langId,
            $command->getCountryId()
        );

        $stateIds = $command->getStateIds();

        $countriesWithErrors = [];

        foreach ($selectedCountries as $countryKey => $country) {
            $statesWithErrors = [];

            /** @var StateId $stateId */
            foreach ($stateIds as $stateKey => $stateId) {
                $error = false;
                try {
                    $hasRuleForCountry = $this->assertUniqueBehaviorTaxRuleForCountry(
                        $taxRulesGroupId,
                        $country,
                        $stateId->getValue(),
                        $taxRule->id
                    );

                    if ($hasRuleForCountry) {
                        $error = true;
                    }

                    if (null !== $command->getZipCode()) {
                        $isInvalid = $this->assertIsValidZipCode(
                            $zipCode,
                            $country
                        );

                        if ($isInvalid) {
                            $error = true;
                        }
                    }

                    $taxRule->id_country = $country;
                    $taxRule->id_state = $stateId->getValue();
                    list($taxRule->zipcode_from, $taxRule->zipcode_to) = $taxRule->breakDownZipCode($zipCode);

                    try {
                        $this->validateTaxRuleFields($taxRule);
                    } catch (TaxRuleConstraintException $e) {
                        $error = true;
                    }

                    reset($selectedCountries);
                    reset($stateIds);

                    if ($countryKey === key($selectedCountries) && $stateKey === key($stateIds)) {
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
                $countriesWithErrors[$country] = $statesWithErrors;
            }
        }

        if (!empty($countriesWithErrors)) {
            throw new CannotUpdateTaxRuleException($countriesWithErrors);
        }
    }
}
