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
     * @param int $customerThreadId
     * @param string $replyMessage
     */
    public function __construct($customerThreadId, $replyMessage)
    {
        $this->customerThreadId = new CustomerThreadId($customerThreadId);
        $this->replyMessage = $replyMessage;
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
}
