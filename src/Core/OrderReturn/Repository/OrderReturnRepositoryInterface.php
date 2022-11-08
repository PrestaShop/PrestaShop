<?php

namespace PrestaShop\PrestaShop\Core\OrderReturn\Repository;

use OrderReturn;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;

interface OrderReturnRepositoryInterface
{
    public function get(OrderReturnId $orderReturnId): OrderReturn;

    public function update(OrderReturn $orderReturn): void;
}
