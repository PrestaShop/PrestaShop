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

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use Carrier;
use OrderDetail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\ListAvailableShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\ListAvailableShipmentsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentsForMerge;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Product;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class ListAvailableShipmentsHandler implements ListAvailableShipmentsHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ListAvailableShipments $query
     *
     * @return ShipmentsForMerge[]
     */
    public function handle(ListAvailableShipments $query)
    {
        $shipments = [];
        $orderId = $query->getOrderId()->getValue();
        $orderDetailsIds = $query->getOrderIdDetails()->getValue();

        try {
            $getShipmentsFromOrder = $this->repository->findByOrderId($orderId);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(sprintf('Could not find shipment for order id "%s"', $orderId), 0, $e);
        }
        if (empty($getShipmentsFromOrder)) {
            return $shipments;
        }

        foreach ($getShipmentsFromOrder as $shipment) {
            foreach ($orderDetailsIds as $orderDetailId) {
                $orderDetail = new OrderDetail($orderDetailId);
                $carrierCompatibleWithProduct = array_map(function ($carrier) {
                    return $carrier['id_carrier'];
                }, (new Product($orderDetail->product_id))->getCarriers());

                if ($shipment->getDeliveredAt() === null) {
                    $isCompatible = in_array($shipment->getCarrierId(), $carrierCompatibleWithProduct);
                    $shipmentName = $this->translator->trans('Shipment ', [], 'Shop.Forms.Labels') . $shipment->getId() . ' ' . (new Carrier($shipment->getCarrierId()))->name;
                    $shipments[] = new ShipmentsForMerge($shipment->getId(), $shipmentName, $isCompatible);
                }
            }
        }

        return $shipments;
    }
}
