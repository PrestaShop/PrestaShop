<?php

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
