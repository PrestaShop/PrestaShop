<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer;

use Customer;
use PrestaShop\PrestaShop\Core\Customer\CustomerDataSourceInterface;

final class CustomerDataSource implements CustomerDataSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasCustomerWithEmail(string $email): bool
    {
        return Customer::customerExists($email, false, false);
    }
}
