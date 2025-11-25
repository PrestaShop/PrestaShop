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
