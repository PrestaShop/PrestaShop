<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;

/**
 * Reply to given customer thread
 */
class ReplyToCustomerThreadCommand
{
    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    /**
     * @var string
     */
    private $replyMessage;

    /**
     * @var int|null
     */
    private $employeeId;

    /**
     * @param int $customerThreadId
     * @param string $replyMessage
     * @param int|null $employeeId The employee the reply is attributed to, the context employee when null
     */
    public function __construct($customerThreadId, $replyMessage, ?int $employeeId = null)
    {
        $this->customerThreadId = new CustomerThreadId($customerThreadId);
        $this->replyMessage = $replyMessage;
        $this->employeeId = $employeeId;
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return string
     */
    public function getReplyMessage()
    {
        return $this->replyMessage;
    }

    /**
     * @return int|null
     */
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }
}
