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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\AddTaxRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotAddTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRuleId;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TaxRule;

/**
 * Handles creation of new tax rule or bulk create tax rules
 */
final class AddTaxRuleHandler extends AbstractTaxRulesGroupHandler implements AddTaxRuleHandlerInterface
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
     * @throws CannotAddTaxRuleException
     * @throws CountryNotFoundException
     * @throws TaxRuleConstraintException
     */
    public function handle(AddTaxRuleCommand $command): TaxRuleId
    {
        $zipCode = $command->getZipCode();
        $taxId = $command->getTaxId() !== null ? $command->getTaxId()->getValue() : 0;
        $taxRulesGroupId = $command->getTaxRulesGroupId()->getValue();
        $behavior = $command->getBehaviorId()->getValue();
        $description = $command->getDescription();
        $taxRuleId = null;

        $selectedCountries = $this->getCountryForTaxRule(
            $this->countryDataProvider,
            $this->langId,
            $command->getCountryId()
        );

        $countriesWithErrors = [];
        $stateIds = $command->getStateIds();

        foreach ($selectedCountries as $country) {
            $statesWithErrors = [];

            /** @var StateId $stateId */
            foreach ($stateIds as $stateId) {
                $error = false;
                try {
                    $hasRuleForCountry = $this->assertUniqueBehaviorTaxRuleForCountry(
                        $taxRulesGroupId,
                        $country,
                        $stateId->getValue()
                    );

                    if ($hasRuleForCountry) {
                        $error = true;
                    }

                    $taxRule = new TaxRule();

                    $taxRule->id_tax_rules_group = $taxRulesGroupId;
                    $taxRule->behavior = $behavior;
                    $taxRule->id_country = $country;
                    $taxRule->id_tax = $taxId;
                    $taxRule->id_state = $stateId->getValue();

                    if (null !== $description) {
                        $taxRule->description = $description;
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

                    list($taxRule->zipcode_from, $taxRule->zipcode_to) = $taxRule->breakDownZipCode($zipCode);

                    try {
                        $this->validateTaxRuleFields($taxRule);
                    } catch (TaxRuleConstraintException $e) {
                        $error = true;
                    }

                    if (!$error && $taxRule->add()) {
                        $taxRuleId = $taxRule->id;

                        continue;
                    }

                    $error = true;
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
            throw new CannotAddTaxRuleException($countriesWithErrors);
        }

        //TODO find out how to handle hooks with multiple tax rule creation
        return new TaxRuleId($taxRuleId);
    }
}
