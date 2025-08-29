<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailQuantity;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class SplitShipment
{
    /**
     * @var ShipmentId
     */
    private $shipmentId;

    /**
     * @var OrderDetailQuantity
     */
    private $orderDetailQuantity;

    /**
     * @var CarrierId
     */
    private $carrierId;

    public function __construct(
        int $shipmentId,
        array $orderDetailQuantity,
        int $carrierId
    ) {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->orderDetailQuantity = new OrderDetailQuantity(
            $orderDetailQuantity,
        );
        $this->carrierId = new CarrierId($carrierId);
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getOrderDetailQuantity(): OrderDetailQuantity
    {
        return $this->orderDetailQuantity;
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }
}
