<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier;

use Address;

/**
 * Class SupplierAddressProvider is responsible for supplier address data retrieval.
 */
class SupplierAddressProvider
{
    /**
     * Gets address id by supplier
     *
     * @param int $supplierId
     *
     * @return int
     */
    public function getIdBySupplier($supplierId)
    {
        return Address::getAddressIdBySupplierId($supplierId);
    }
}
