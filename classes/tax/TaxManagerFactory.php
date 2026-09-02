<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class TaxManagerFactoryCore
{
    protected static $cache_tax_manager;

    /**
     * Returns a tax manager able to handle this address.
     *
     * @param Address $address
     * @param int $type
     *
     * @return TaxManagerInterface
     */
    public static function getManager(Address $address, $type)
    {
        $cache_id = TaxManagerFactory::getCacheKey($address) . '-' . $type;
        if (!isset(TaxManagerFactory::$cache_tax_manager[$cache_id])) {
            $tax_manager = TaxManagerFactory::execHookTaxManagerFactory($address, $type);
            if (!($tax_manager instanceof TaxManagerInterface)) {
                $tax_manager = new TaxRulesTaxManager($address, $type);
            }

            TaxManagerFactory::$cache_tax_manager[$cache_id] = $tax_manager;
        }

        return TaxManagerFactory::$cache_tax_manager[$cache_id];
    }

    /**
     * Check for a tax manager able to handle this type of address in the module list.
     *
     * @param Address $address
     * @param int $type
     *
     * @return TaxManagerInterface|false
     */
    public static function execHookTaxManagerFactory(Address $address, $type)
    {
        $modules_infos = Hook::getModulesFromHook(Hook::getIdByName('taxManager'));
        $tax_manager = false;

        foreach ($modules_infos as $module_infos) {
            $module_instance = Module::getInstanceByName($module_infos['name']);
            if (is_callable([$module_instance, 'hookTaxManager'])) {
                $tax_manager = $module_instance->hookTaxManager([
                    'address' => $address,
                    'params' => $type,
                ]);
            }

            if ($tax_manager) {
                break;
            }
        }

        return $tax_manager;
    }

    /**
     * Reset static cache (mainly for test environment)
     */
    public static function resetStaticCache()
    {
        TaxManagerFactory::$cache_tax_manager = null;
    }

    /**
     * Create a unique identifier for the address.
     *
     * @param Address $address
     *
     * @return string
     */
    protected static function getCacheKey(Address $address)
    {
        return $address->id_country . '-'
                . (int) $address->id_state . '-'
                . $address->postcode . '-'
                . $address->vat_number . '-'
                . $address->dni;
    }
}
