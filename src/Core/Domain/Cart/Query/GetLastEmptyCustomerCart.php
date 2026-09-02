<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Query for retrieving last empty cart for customer
 */
class GetLastEmptyCustomerCart
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @param int $customerId
     */
    public function __construct(int $customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }
}
