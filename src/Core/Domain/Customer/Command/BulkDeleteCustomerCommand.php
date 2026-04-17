<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Deletes given customers.
 */
class BulkDeleteCustomerCommand
{
    /**
     * @var CustomerId[]
     */
    private $customerIds;

    /**
     * @var CustomerDeleteMethod
     */
    private $deleteMethod;

    /**
     * @param int[] $customerIds
     * @param string $deleteMethod
     */
    public function __construct(array $customerIds, $deleteMethod)
    {
        $this->setCustomerIds($customerIds);
        $this->deleteMethod = new CustomerDeleteMethod($deleteMethod);
    }

    /**
     * @return CustomerId[]
     */
    public function getCustomerIds()
    {
        return $this->customerIds;
    }

    /**
     * @return CustomerDeleteMethod
     */
    public function getDeleteMethod()
    {
        return $this->deleteMethod;
    }

    /**
     * @param int[] $customerIds
     */
    private function setCustomerIds(array $customerIds)
    {
        foreach ($customerIds as $customerId) {
            $this->customerIds[] = new CustomerId($customerId);
        }
    }
}
