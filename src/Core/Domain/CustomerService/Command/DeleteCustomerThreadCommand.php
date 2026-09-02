<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;

class DeleteCustomerThreadCommand
{
    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    public function __construct(int $customerThreadId)
    {
        $this->customerThreadId = new CustomerThreadId($customerThreadId);
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId(): CustomerThreadId
    {
        return $this->customerThreadId;
    }
}
