<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class Probe_Carrier_Module extends CarrierModule
{
    /**
     * Configuration key holding the price returned for every cart; the carrier is unavailable when it is not set
     */
    public const SHIPPING_COST_KEY = 'PROBE_CARRIER_MODULE_SHIPPING_COST';

    public function __construct()
    {
        $this->name = 'probe_carrier_module';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Probe carrier module';
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        return $this->getOrderShippingCostExternal($params);
    }

    public function getOrderShippingCostExternal($params)
    {
        $shippingCost = Configuration::getGlobalValue(self::SHIPPING_COST_KEY);

        return false === $shippingCost ? false : (float) $shippingCost;
    }
}
