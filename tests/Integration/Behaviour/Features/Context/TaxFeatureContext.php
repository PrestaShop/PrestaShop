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

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Cache;
use Context;
use Tax;
use TaxRule;
use TaxRulesGroup;

class TaxFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Tax[]
     */
    protected $taxes = [];

    /**
     * @var TaxRule[]
     */
    protected $taxRules = [];

    /**
     * @var TaxRulesGroup[]
     */
    protected $taxRuleGroups = [];

    /**
     * @var CarrierFeatureContext
     */
    protected $carrierFeatureContext;

    /**
     * @var LegacyProductFeatureContext
     */
    protected $productFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        /** @var CarrierFeatureContext $carrierFeatureContext */
        $carrierFeatureContext = $environment->getContext(CarrierFeatureContext::class);
        /** @var LegacyProductFeatureContext $productFeatureContext */
        $productFeatureContext = $environment->getContext(LegacyProductFeatureContext::class);

        $this->carrierFeatureContext = $carrierFeatureContext;
        $this->productFeatureContext = $productFeatureContext;
    }

    /**
     * @Given /^there is a tax named "(.+)" and rate (\d+\.\d+)%$/
     */
    public function createTax(string $name, float $rate): void
    {
        $tax = new Tax();
        $tax->name = [(int) Context::getContext()->language->id => 'fake'];
        $tax->rate = $rate;
        $tax->active = true;
        $tax->add();
        $this->taxes[$name] = $tax;
    }

    /**
     * @Given /^there is a tax rule named "(.+)" in country "([^\"]+)" and state "(.+)" where tax "(.+)" is applied$/
     */
    public function createTaxRule($taxRuleName, $countryName, $stateName, $taxName)
    {
        $this->carrierFeatureContext->checkCountryWithNameExists($countryName);
        $this->carrierFeatureContext->checkStateWithNameExists($stateName);
        $this->checkTaxWithNameExists($taxName);

        $taxRuleGroup = new TaxRulesGroup();
        $taxRuleGroup->active = true;
        $taxRuleGroup->name = 'fake';
        $taxRuleGroup->add();
        $this->taxRuleGroups[$taxRuleName] = $taxRuleGroup;

        $taxRule = new TaxRule();
        $taxRule->id_country = $this->carrierFeatureContext->getCountryWithName($countryName)->id;
        $taxRule->id_state = $this->carrierFeatureContext->getStateWithName($stateName)->id;
        $taxRule->id_tax_rules_group = $taxRuleGroup->id;
        $taxRule->id_tax = $this->taxes[$taxName]->id;
        $taxRule->zipcode_from = 0;
        $taxRule->zipcode_to = 0;
        $taxRule->behavior = 1;
        $taxRule->add();
        $this->taxRules[$taxRuleName] = $taxRule;
    }

    /**
     * @Given /^there is a tax rule named "(.+)" in country "([^\"]+)" where tax "(.+)" is applied$/
     */
    public function createTaxRuleWithoutState(string $taxRuleName, string $countryName, string $taxName): void
    {
        $this->carrierFeatureContext->checkCountryWithNameExists($countryName);
        $this->checkTaxWithNameExists($taxName);

        $taxRuleGroup = new TaxRulesGroup();
        $taxRuleGroup->active = true;
        $taxRuleGroup->name = 'fake';
        $taxRuleGroup->add();
        $this->taxRuleGroups[$taxRuleName] = $taxRuleGroup;

        $taxRule = new TaxRule();
        $taxRule->id_country = $this->carrierFeatureContext->getCountryWithName($countryName)->id;
        $taxRule->id_state = 0;
        $taxRule->id_tax_rules_group = $taxRuleGroup->id;
        $taxRule->id_tax = $this->taxes[$taxName]->id;
        $taxRule->zipcode_from = 0;
        $taxRule->zipcode_to = 0;
        $taxRule->behavior = 1;
        $taxRule->add();
        $this->taxRules[$taxRuleName] = $taxRule;
    }

    /**
     * @param string $name
     */
    public function checkTaxWithNameExists(string $name): void
    {
        $this->checkFixtureExists($this->taxes, 'Tax', $name);
    }

    /**
     * @param string $name
     */
    public function checkTaxRuleWithNameExists(string $name): void
    {
        $this->checkFixtureExists($this->taxRules, 'Tax rule', $name);
    }

    /**
     * @AfterScenario
     */
    public function cleanFixtures()
    {
        foreach ($this->taxRules as $taxRule) {
            $taxRule->delete();
        }
        $this->taxRules = [];
        foreach ($this->taxRuleGroups as $taxRuleGroup) {
            $taxRuleGroup->delete();
        }
        $this->taxRuleGroups = [];
        foreach ($this->taxes as $tax) {
            $tax->delete();
        }
        $this->taxes = [];
    }

    /**
     * @Given /^product "(.+)" belongs to tax group "(.+)"$/
     */
    public function setProductTaxRuleGroup($productName, $taxName)
    {
        $this->productFeatureContext->checkProductWithNameExists($productName);
        $this->checkTaxRuleWithNameExists($taxName);
        $product = $this->productFeatureContext->getProductWithName($productName);
        $product->id_tax_rules_group = $this->taxRuleGroups[$taxName]->id;
        $product->save();

        // Clean cache after changing tax of product
        Cache::clean('product_id_tax_rules_group_*');
    }

    /**
     * @Given /^carrier "(.+)" belongs to tax group "(.+)"$/
     */
    public function setCarrierTaxRuleGroup($carrierName, $taxName)
    {
        $this->carrierFeatureContext->checkCarrierWithNameExists($carrierName);
        $this->checkTaxRuleWithNameExists($taxName);
        $carrier = $this->carrierFeatureContext->getCarrierWithName($carrierName);
        $carrier->setTaxRulesGroup($this->taxRuleGroups[$taxName]->id);
    }

    /**
     * @Given /^Ecotax belongs to tax group "(.+)"$/
     */
    public function setEcotaxTaxRuleGroup($taxName)
    {
        $this->checkTaxRuleWithNameExists($taxName);
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_ECOTAX_TAX_RULES_GROUP_ID', $this->taxRuleGroups[$taxName]->id);
    }
}
