<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Query;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;

/**
 * Gets customer thread for viewing
 */
class GetCustomerThreadForViewing
{
    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    /**
     * @param int $customerThreadId
     */
    public function __construct($customerThreadId)
    {
        $this->customerThreadId = new CustomerThreadId($customerThreadId);
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }
}
