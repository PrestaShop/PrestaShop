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

namespace PrestaShop\PrestaShop\Adapter\Tax;

use Address;
use Context;
use Product;
use Tax;
use TaxManagerFactory;
use TaxRulesGroup;

/**
 * This class will provide data from DB / ORM about tax rules.
 */
class TaxRuleDataProvider
{
    /**
     * Get all Tax Rules Groups.
     *
     * @param bool $only_active
     *
     * @return array TaxRulesGroup
     */
    public function getTaxRulesGroups($only_active = true)
    {
        return TaxRulesGroup::getTaxRulesGroups($only_active);
    }

    /**
     * Get most used Tax.
     *
     * @return int
     */
    public function getIdTaxRulesGroupMostUsed()
    {
        return Product::getIdTaxRulesGroupMostUsed();
    }

    /**
     * Get all Tax Rules Groups with rates.
     *
     * @return array TaxRulesGroup
     */
    public function getTaxRulesGroupWithRates()
    {
        $address = new Address();
        $address->id_country = (int) Context::getContext()->country->id;
        $tax_rules_groups = $this->getTaxRulesGroups();
        $tax_rates = array(
            0 => array(
                'id_tax_rules_group' => 0,
                'rates' => array(0),
                'computation_method' => 0,
            ),
        );

        foreach ($tax_rules_groups as $tax_rules_group) {
            $id_tax_rules_group = (int) $tax_rules_group['id_tax_rules_group'];
            $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
            $tax_rates[$id_tax_rules_group] = array(
                'id_tax_rules_group' => $id_tax_rules_group,
                'rates' => array(),
                'computation_method' => (int) $tax_calculator->computation_method,
            );

            if (isset($tax_calculator->taxes) && count($tax_calculator->taxes)) {
                foreach ($tax_calculator->taxes as $tax) {
                    $tax_rates[$id_tax_rules_group]['rates'][] = (float) $tax->rate;
                }
            } else {
                $tax_rates[$id_tax_rules_group]['rates'][] = 0;
            }
        }

        return $tax_rates;
    }

    /**
     * Get product eco taxe rate.
     *
     * @return float tax
     */
    public function getProductEcotaxRate()
    {
        return Tax::getProductEcotaxRate();
    }

    /**
     * Gets a list of tax rules groups for choice type.
     *
     * @param bool $onlyActive if true, returns only active tax rules groups
     *
     * @return array
     */
    public function getTaxRulesGroupChoices($onlyActive = true)
    {
        $taxRulesGroups = $this->getTaxRulesGroups($onlyActive);
        $choices = [];

        foreach ($taxRulesGroups as $taxRulesGroup) {
            $choices[$taxRulesGroup['name']] = $taxRulesGroup['id_tax_rules_group'];
        }

        return $choices;
    }
}
