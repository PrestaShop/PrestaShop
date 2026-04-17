<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier;

/**
 * Class SupplierOrderValidator is responsible for handling supplier and its corresponding order validity.
 *
 * @deprecated since 9.0 and will be removed in 10.0
 */
class SupplierOrderValidator
{
    /**
     * Checks if the given supplier has pending orders.
     *
     * @param int $supplierId
     *
     * @return bool
     */
    public function hasPendingOrders($supplierId)
    {
        @trigger_error(sprintf(
            '%s is deprecated since 9.0 and will be removed in 10.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return false;
    }
}
