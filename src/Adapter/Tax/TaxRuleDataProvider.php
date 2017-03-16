<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Tax;

/**
 * This class will provide data from DB / ORM about tax rules
 */
class TaxRuleDataProvider
{
    /**
     * Get all Tax Rules Groups
     *
     * @param bool $only_active
     *
     * @return array TaxRulesGroup
     */
    public function getTaxRulesGroups($only_active = true)
    {
        return \TaxRulesGroupCore::getTaxRulesGroups($only_active);
    }

    /**
     * Get all Tax Rules Groups with rates
     *
     * @return array TaxRulesGroup
     */
    public function getTaxRulesGroupWithRates()
    {
        $address = new \Address();
        $address->id_country = (int)\ContextCore::getContext()->country->id;
        $tax_rules_groups = $this->getTaxRulesGroups();
        $tax_rates = array(
            0 => array(
                'id_tax_rules_group' => 0,
                'rates' => array(0),
                'computation_method' => 0
            )
        );

        foreach ($tax_rules_groups as $tax_rules_group) {
            $id_tax_rules_group = (int)$tax_rules_group['id_tax_rules_group'];
            $tax_calculator = \TaxManagerFactoryCore::getManager($address, $id_tax_rules_group)->getTaxCalculator();
            $tax_rates[$id_tax_rules_group] = array(
                'id_tax_rules_group' => $id_tax_rules_group,
                'rates' => array(),
                'computation_method' => (int)$tax_calculator->computation_method
            );

            if (isset($tax_calculator->taxes) && count($tax_calculator->taxes)) {
                foreach ($tax_calculator->taxes as $tax) {
                    $tax_rates[$id_tax_rules_group]['rates'][] = (float)$tax->rate;
                }
            } else {
                $tax_rates[$id_tax_rules_group]['rates'][] = 0;
            }
        }

        return $tax_rates;
    }

    /**
     * Get product eco taxe rate
     *
     * @return float tax
     */
    public function getProductEcotaxRate()
    {
        return \TaxCore::getProductEcotaxRate();
    }
}
