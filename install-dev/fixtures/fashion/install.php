<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
