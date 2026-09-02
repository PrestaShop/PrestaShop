<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult;

use DateTimeImmutable;

class OrderReturnForEditing
{
    /**
     * @var int
     */
    private $orderReturnId;

    /**
     * @var int
     */
    private $customerId;

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
    private $orderId;

    /**
     * @var DateTimeImmutable
     */
    private $orderDate;

    /**
     * @var int
     */
    private $orderReturnStateId;

    /**
     * @var string
     */
    private $question;

    /**
     * OrderReturnForEditing constructor.
     *
     * @param int $orderReturnId
     * @param int $customerId
     * @param string $customerFirstName
     * @param string $customerLastName
     * @param int $orderId
     * @param DateTimeImmutable $orderDate
     * @param int $orderReturnStateId
     * @param string $question
     */
    public function __construct(
        int $orderReturnId,
        int $customerId,
        string $customerFirstName,
        string $customerLastName,
        int $orderId,
        DateTimeImmutable $orderDate,
        int $orderReturnStateId,
        string $question
    ) {
        $this->orderReturnId = $orderReturnId;
        $this->customerId = $customerId;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->orderReturnStateId = $orderReturnStateId;
        $this->question = $question;
    }

    /**
     * @return int
     */
    public function getOrderReturnId(): int
    {
        return $this->orderReturnId;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getOrderReturnStateId(): int
    {
        return $this->orderReturnStateId;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @return string
     */
    public function getCustomerFullName(): string
    {
        return sprintf('%s %s', $this->customerFirstName, $this->customerLastName);
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
     * @return DateTimeImmutable
     */
    public function getOrderDate(): DateTimeImmutable
    {
        return $this->orderDate;
    }
}
