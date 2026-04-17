<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Deletes given customer.
 */
class DeleteCustomerCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var CustomerDeleteMethod
     */
    private $deleteMethod;

    /**
     * @param int $customerId
     * @param string $deleteMethod
     */
    public function __construct($customerId, $deleteMethod)
    {
        $this->customerId = new CustomerId($customerId);
        $this->deleteMethod = new CustomerDeleteMethod($deleteMethod);
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return CustomerDeleteMethod
     */
    public function getDeleteMethod()
    {
        return $this->deleteMethod;
    }
}
