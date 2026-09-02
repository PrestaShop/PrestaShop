<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use Manufacturer;

/**
 * This class will provide data from DB / ORM about Manufacturer.
 */
class ManufacturerDataProvider
{
    /**
     * Get all Manufacturer.
     *
     * @param bool $get_nb_products
     * @param int $id_lang
     * @param bool $active
     * @param bool $p
     * @param bool $n
     * @param bool $all_group
     * @param bool $group_by
     *
     * @return array Manufacturer
     */
    public function getManufacturers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_group = false, $group_by = false)
    {
        return Manufacturer::getManufacturers($get_nb_products, $id_lang, $active, $p, $n, $all_group, $group_by);
    }
}
