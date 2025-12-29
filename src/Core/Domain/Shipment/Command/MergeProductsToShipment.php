<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailQuantity;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class MergeProductsToShipment
{
    /**
     * @var ShipmentId
     */
    private $sourceShipmentId;

    /**
     * @var ShipmentId
     */
    private $targetShipmentId;

    /**
     * @var OrderDetailQuantity
     */
    private $orderDetailQuantities;

    public function __construct(int $sourceShipmentId, int $targetShipmentId, array $orderDetailQuantities)
    {
        $this->sourceShipmentId = new ShipmentId($sourceShipmentId);
        $this->targetShipmentId = new ShipmentId($targetShipmentId);
        $this->orderDetailQuantities = new OrderDetailQuantity(
            $orderDetailQuantities,
        );
    }

    public function getSourceShipmentId(): ShipmentId
    {
        return $this->sourceShipmentId;
    }

    public function getTargetShipmentId(): ShipmentId
    {
        return $this->targetShipmentId;
    }

    public function getOrderDetailQuantity(): OrderDetailQuantity
    {
        return $this->orderDetailQuantities;
    }
}
