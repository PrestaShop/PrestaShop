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

use OrderDetail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetShipmentForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForEditing;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class GetShipmentForEditingHandler implements GetShipmentForEditingHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GetShipmentForEditing $query
     *
     * @return ShipmentForEditing
     */
    public function handle(GetShipmentForEditing $query)
    {
        $shipmentDetails = [];
        $orderId = $query->getOrderId()->getValue();
        $shipmentId = $query->getShipmentId()->getValue();

        try {
            $result = $this->shipmentRepository->findByOrderAndShipmentId($orderId, $shipmentId);
            $shipmentProducts = $result->getProducts()->toArray();
            $shipmentDetails['tracking_number'] = $result->getTrackingNumber();
            $shipmentDetails['carrier'] = $result->getCarrierId();

            foreach ($shipmentProducts as $shipmentProduct) {
                $shipmentDetails['selectedProducts'][
                    (new OrderDetail($shipmentProduct->getOrderDetailId()))->product_id
                ] = 0;
            }
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment for order with id "%id%".',
                    ['%id%' => $orderId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }

        return new ShipmentForEditing(
            $shipmentDetails['carrier'],
            $shipmentDetails['tracking_number'] ?? '',
            $shipmentDetails['selectedProducts']
        );
    }
}
