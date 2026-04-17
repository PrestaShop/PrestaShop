<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Enables customers in bulk action.
 */
class BulkEnableCustomerCommand
{
    /**
     * @var CustomerId[]
     */
    private $customerIds = [];

    /**
     * @param int[] $customerIds
     */
    public function __construct(array $customerIds)
    {
        $this->setCustomerIds($customerIds);
    }

    /**
     * @return CustomerId[]
     */
    public function getCustomerIds()
    {
        return $this->customerIds;
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
