<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

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
     * @var OrderId
     */
    private $orderId;

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
        OrderId $orderId,
        int $state,
        string $question
    )
    {
        $this->merchandiseReturnId = $merchandiseReturnId;
        $this->customerId = $customerId;
        $this->orderId = $orderId;
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
}
