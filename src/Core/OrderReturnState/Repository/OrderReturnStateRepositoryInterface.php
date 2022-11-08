<?php

namespace PrestaShop\PrestaShop\Core\OrderReturnState\Repository;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

interface OrderReturnStateRepositoryInterface
{
    public function get(OrderReturnStateId $orderReturnStateId): OrderReturnState;
}
