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
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\AddTaxRulesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotAddTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRuleConstraintException;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TaxRule;

/**
 * Handles creation of new tax rule or bulk create tax rules
 */
final class AddTaxRulesHandler extends AbstractTaxRulesGroupHandler implements AddTaxRulesHandlerInterface
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
     * @throws TaxRuleConstraintException
     */
    public function handle(AddTaxRulesCommand $command)
    {
        $zipCode = $command->getZipCode();
        $taxId = $command->getTaxId() !== null ? $command->getTaxId()->getValue() : TaxId::NO_TAX_ID;
        $taxRulesGroupId = $command->getTaxRulesGroupId()->getValue();
        $behaviorId = $command->getBehavior()->getValue();
        $description = $command->getDescription();
        $taxRuleId = null;

        $selectedCountryIds = $this->getCountryIdsForTaxRule(
            $this->countryDataProvider,
            $this->langId,
            $command->getCountryId()
        );

        $countriesWithErrors = [];
        $stateIds = $command->getStateIds();

        /** @var int $countryId */
        foreach ($selectedCountryIds as $countryId) {
            $statesWithErrors = [];

            /** @var StateId $stateId */
            foreach ($stateIds as $stateId) {
                $error = false;
                try {
                    $violationOfUniqueTaxRuleForACountryRule = $this->assertUniqueBehaviorTaxRuleForCountry(
                        $taxRulesGroupId,
                        $countryId,
                        $stateId->getValue()
                    );

                    if ($violationOfUniqueTaxRuleForACountryRule) {
                        $error = true;
                    }

                    $taxRule = new TaxRule();

                    $taxRule->id_tax_rules_group = $taxRulesGroupId;
                    $taxRule->behavior = $behaviorId;
                    $taxRule->id_country = $countryId;
                    $taxRule->id_tax = $taxId;
                    $taxRule->id_state = $stateId->getValue();

                    if (null !== $description) {
                        $taxRule->description = $description;
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

                    list($taxRule->zipcode_from, $taxRule->zipcode_to) = $taxRule->breakDownZipCode($zipCode);

                    try {
                        $this->validateTaxRuleFields($taxRule);
                    } catch (TaxRuleConstraintException $e) {
                        $error = true;
                    }

                    if (!$error && $taxRule->add()) {
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
                $countriesWithErrors[$countryId] = $statesWithErrors;
            }
        }

        if (!empty($countriesWithErrors)) {
            throw new CannotAddTaxRuleException($countriesWithErrors);
        }
    }
}
