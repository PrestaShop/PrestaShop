<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter;

use Address;

/**
 * Class responsible of creation of Address ObjectModel.
 */
class AddressFactory
{
    /**
     * Initialize an address corresponding to the specified id address or if empty to the
     * default shop configuration.
     *
     * @param int|null $id_address
     * @param bool $with_geoloc
     *
     * @return Address
     */
    public function findOrCreate($id_address = null, $with_geoloc = false)
    {
        $func_args = func_get_args();

        return call_user_func_array(array('\\Address', 'initialize'), $func_args);
    }

    /**
     * Check if an address exists depending on given $id_address.
     *
     * @param $id_address
     *
     * @return bool
     */
    public function addressExists($id_address)
    {
        return Address::addressExists($id_address);
    }
}
