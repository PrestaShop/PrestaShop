<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderMessageForViewing
{
    /**
     * @var int
     */
    private $messageId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var OrderMessageDateForViewing
     */
    private $messageDate;

    /**
     * @var string
     */
    private $employeeFirstName;

    /**
     * @var string
     */
    private $employeeLastName;

    /**
     * @var string
     */
    private $customerFirstName;

    /**
     * @var string
     */
    private $customerLastName;

    /**
     * @var int
     */
    private $employeeId;
    /**
     * @var bool
     */
    private $isPrivate;
    /**
     * @var bool
     */
    private $isCurrentEmployeesMessage;

    /**
     * @param int $messageId
     * @param string $message
     * @param OrderMessageDateForViewing $messageDate
     * @param int $employeeId
     * @param bool $isCurrentEmployeesMessage
     * @param string $employeeFirstName
     * @param string $employeeLastName
     * @param string $customerFirstName
     * @param string $customerLastName
     * @param bool $isPrivate
     */
    public function __construct(
        int $messageId,
        string $message,
        OrderMessageDateForViewing $messageDate,
        int $employeeId,
        bool $isCurrentEmployeesMessage,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        string $customerFirstName,
        string $customerLastName,
        bool $isPrivate
    ) {
        $this->messageId = $messageId;
        $this->message = $message;
        $this->messageDate = $messageDate;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->employeeId = $employeeId;
        $this->isPrivate = $isPrivate;
        $this->isCurrentEmployeesMessage = $isCurrentEmployeesMessage;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return OrderMessageDateForViewing
     */
    public function getMessageDate(): OrderMessageDateForViewing
    {
        return $this->messageDate;
    }

    /**
     * @return string
     */
    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    /**
     * @return string
     */
    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
    }

    /**
     * @return string
     */
    public function getCustomerFirstName(): string
    {
        return $this->customerFirstName;
    }

    /**
     * @return string
     */
    public function getCustomerLastName(): string
    {
        return $this->customerLastName;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @return bool
     */
    public function isCurrentEmployeesMessage(): bool
    {
        return $this->isCurrentEmployeesMessage;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }
}
