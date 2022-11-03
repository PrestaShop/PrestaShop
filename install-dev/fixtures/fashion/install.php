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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShopBundle\Install\XmlLoader;

/**
 * This class is only here to show the possibility of extending InstallXmlLoader, which is the
 * class parsing all XML files, copying all images, etc.
 *
 * Please read documentation in ~/install/dev/ folder if you want to customize PrestaShop install / fixtures.
 */
class InstallFixturesFashion extends XmlLoader
{
    public function createEntityCustomer($identifier, array $data, array $data_lang)
    {
        $crypto = ServiceLocator::get(Hashing::class);

        $data['passwd'] = $crypto->hash($data['passwd']);

        return $this->createEntity('customer', $identifier, 'Customer', $data, $data_lang);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromXmlFiles()
    {
        // US and FL match John's address in the fixtures, if the XML is modified this should be updated as well
        $taxRulesGroupId = $this->getTaxRulesGroupId('US', 'FL');
        // This special tax rule group is useful for tests, however for fresh install it may not be available depending
        // on the selected country, then we fallback on the default value 1 (legacy behaviour anyway)
        if (!$taxRulesGroupId) {
            $taxRulesGroupId = 1;
        }
        $this->storeId('tax_rules_group', 'default_tax_rule_group', $taxRulesGroupId);

        parent::populateFromXmlFiles();

        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'country SET active = 1 ' .
            'WHERE id_country IN (' .
            '  SELECT id_country FROM ' . _DB_PREFIX_ . 'address' .
            ')'
        );

        /**
         * Refresh facetedsearch cache
         */
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();
        if ($moduleManager->isInstalled('ps_facetedsearch')) {
            $moduleManager->reset('ps_facetedsearch');
        }
    }

    private function getTaxRulesGroupId(string $country, string $state)
    {
        $stateId = $this->retrieveId('state', $state);
        $countryId = $this->retrieveId('country', $country);

        return Db::getInstance()->getValue(
            'SELECT id_tax_rules_group
            FROM ' . _DB_PREFIX_ . 'tax_rule
            WHERE
            id_country=' . (int) $countryId . ' AND id_state=' . (int) $stateId
        );
    }
}
