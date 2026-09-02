<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;

/**
 * Updates customer thread with given status
 */
class UpdateCustomerThreadStatusCommand
{
    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    /**
     * @var CustomerThreadStatus
     */
    private $customerThreadStatus;

    /**
     * @param int $customerThreadId
     * @param string $newCustomerThreadStatus
     */
    public function __construct($customerThreadId, $newCustomerThreadStatus)
    {
        $this->customerThreadId = new CustomerThreadId($customerThreadId);
        $this->customerThreadStatus = new CustomerThreadStatus($newCustomerThreadStatus);
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return CustomerThreadStatus
     */
    public function getCustomerThreadStatus()
    {
        return $this->customerThreadStatus;
    }
}
