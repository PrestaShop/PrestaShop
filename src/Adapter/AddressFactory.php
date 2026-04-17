<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Address;

/**
 * Class responsible for creating Address ObjectModel.
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

        return call_user_func_array(['\\Address', 'initialize'], $func_args);
    }

    /**
     * Check if an address exists depending on given $id_address.
     *
     * @param int $id_address
     * @param bool $useCache [default=false] If true, use Cache for optimizing queries
     *
     * @return bool
     */
    public function addressExists($id_address, bool $useCache = false)
    {
        return Address::addressExists($id_address, $useCache);
    }
}
