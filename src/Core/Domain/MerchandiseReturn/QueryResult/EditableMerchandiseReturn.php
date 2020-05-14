<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use DateTime;

class EditableMerchandiseReturn
{
    /**
     * @var MerchandiseReturnId
     */
    private $merchandiseReturnId;

    /**
     * @var CustomerId
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
     * @var OrderId
     */
    private $orderId;

    /**
     * @var DateTime
     */
    private $orderDate;

    /**
     * @var int
     */
    private $state;

    /**
     * @var string
     */
    private $question;

    public function __construct(
        MerchandiseReturnId $merchandiseReturnId,
        CustomerId $customerId,
        string $customerFirstName,
        string $customerLastName,
        OrderId $orderId,
        DateTime $orderDate,
        int $state,
        string $question
    )
    {
        $this->merchandiseReturnId = $merchandiseReturnId;
        $this->customerId = $customerId;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->state = $state;
        $this->question = $question;
    }

    /**
     * @return MerchandiseReturnId
     */
    public function getMerchandiseReturnId(): MerchandiseReturnId
    {
        return $this->merchandiseReturnId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
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
     * @return DateTime
     */
    public function getOrderDate(): DateTime
    {
        return $this->orderDate;
    }
}
