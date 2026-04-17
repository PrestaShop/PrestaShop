<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

/**
 * Defines customer thread id
 */
class CustomerThreadId
{
    /**
     * @var int
     */
    private $customerThreadId;

    /**
     * @param int $customerThreadId
     */
    public function __construct($customerThreadId)
    {
        if (!is_int($customerThreadId) || 0 > $customerThreadId) {
            throw new CustomerServiceException('CustomerThreadId must be of type int and greater than zero.');
        }

        $this->customerThreadId = $customerThreadId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->customerThreadId;
    }
}
