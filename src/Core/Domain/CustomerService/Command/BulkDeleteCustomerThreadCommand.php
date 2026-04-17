<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;

class BulkDeleteCustomerThreadCommand
{
    /**
     * @var CustomerThreadId[]
     */
    private $customerThreadIds;

    /**
     * @param array<int, int> $customerThreadIds
     *
     * @throws CustomerServiceException
     */
    public function __construct(array $customerThreadIds)
    {
        $this->setCustomerThreadIds($customerThreadIds);
    }

    /**
     * @return CustomerThreadId[]
     */
    public function getCustomerThreadIds(): array
    {
        return $this->customerThreadIds;
    }

    /**
     * @param array<int, int> $customerThreadIds
     *
     * @throws CustomerServiceException
     */
    private function setCustomerThreadIds(array $customerThreadIds): void
    {
        foreach ($customerThreadIds as $customerThreadId) {
            $this->customerThreadIds[] = new CustomerThreadId($customerThreadId);
        }
    }
}
