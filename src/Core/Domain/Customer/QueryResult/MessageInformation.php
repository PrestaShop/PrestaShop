<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class CustomerMessageInformation holds customer message information.
 */
class MessageInformation
{
    /**
     * @var int
     */
    private $customerThreadId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $date;

    /**
     * @param int $customerThreadId
     * @param string $message
     * @param string $status
     * @param string $date
     */
    public function __construct($customerThreadId, $message, $status, $date)
    {
        $this->customerThreadId = $customerThreadId;
        $this->message = $message;
        $this->status = $status;
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
}
