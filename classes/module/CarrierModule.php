<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Base class to be used instead of `Module` for modules that manage their own carriers.
 *
 * A module can declare one or several carriers. For each carrier managed by the module:
 *   - The carrier's `shipping_external` flag must be set to `true`.
 *   - The carrier's `external_module_name` must match the module's technical name.
 *
 * This ensures that PrestaShop delegates shipping-cost calculation to the module
 * through the `CarrierModuleCore` methods.
 */
abstract class CarrierModuleCore extends Module
{
    /**
     * Calculate the shipping cost for an order.
     *
     * @param CartCore $params the cart instance
     * @param float $shipping_cost the base shipping cost before module adjustments
     *
     * @return float|false|null Returns a float for shipping cost.
     *                          Returns null to mark the delivery options in the checkout tunnel as "free".
     *                          Returns false to filter out (exclude) this delivery option.
     */
    abstract public function getOrderShippingCost($params, $shipping_cost);

    /**
     * Calculate the external shipping cost for an order. Called when `need_range` of the carrier is set as `false`.
     *
     * @param CartCore $params the cart instance
     *
     * @return float|false|null Returns a float for shipping cost.
     *                          Returns null to mark the delivery options in the checkout tunnel as "free".
     *                          Returns false to filter out (exclude) this delivery option.
     */
    abstract public function getOrderShippingCostExternal($params);

    /** @var int|null */
    public $id_carrier;
}
