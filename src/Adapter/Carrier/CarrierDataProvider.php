<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use Carrier;

/**
 * This class will provide data from DB / ORM about Category
 */
class CarrierDataProvider
{
    /**
     * Get all carriers in a given language
     *
     * @param int $id_lang Language id
     * @param bool $active Returns only active carriers when true
     * @param bool $delete
     * @param bool|int $id_zone
     * @param null|string $ids_group
     * @param $modules_filters, possible values:
     * PS_CARRIERS_ONLY
     * CARRIERS_MODULE
     * CARRIERS_MODULE_NEED_RANGE
     * PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
     * ALL_CARRIERS
     *
     * @return array Carriers
     */
    public function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = Carrier::PS_CARRIERS_ONLY)
    {
        return Carrier::getCarriers($id_lang, $active, $delete, $id_zone, $ids_group, $modules_filters);
    }

    /**
     * Get the CarrierCore class ALL_CARRIERS constant value
     *
     * @return int
     */
    public function getAllCarriersConstant()
    {
        return Carrier::ALL_CARRIERS;
    }
}
