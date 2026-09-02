<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

/**
 * Status that customer thread can have
 */
final class CustomerThreadStatus
{
    public const OPEN = 'open';
    public const CLOSED = 'closed';
    public const PENDING_1 = 'pending1';
    public const PENDING_2 = 'pending2';

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $availableStatuses = [
            self::OPEN,
            self::CLOSED,
            self::PENDING_1,
            self::PENDING_2,
        ];

        if (!in_array($status, $availableStatuses)) {
            throw new CustomerServiceException(sprintf('Customer thread status "%s" is not defined, available statuses are "%s"', $status, implode(',', $availableStatuses)));
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->status;
    }
}
