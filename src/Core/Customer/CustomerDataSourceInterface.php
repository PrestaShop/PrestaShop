<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Customer;

interface CustomerDataSourceInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function hasCustomerWithEmail(string $email): bool;
}
