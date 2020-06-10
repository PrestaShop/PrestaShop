<?php

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Query;

class GetOrderDetailCustomization
{
    /**
     * @var int
     */
    private $orderDetailId;

    public function __construct(int $orderDetailId)
    {
        $this->orderDetailId = $orderDetailId;
    }

    /**
     * @return int
     */
    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }
}
