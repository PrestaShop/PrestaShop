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

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\SplitShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\SplitShipmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\CannotEditShipmentShippedException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Service\ShipmentSplitterInterface;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\ShipmentProduct;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommandHandler]
class SplitShipmentHandler implements SplitShipmentHandlerInterface
{
    public function __construct(
        private ShipmentRepository $repository,
        private ShipmentSplitterInterface $splitter,
        private TranslatorInterface $translator,
    ) {
    }

    public function handle(SplitShipment $command): void
    {
        $shipmentId = $command->getShipmentId()->getValue();
        $carrierId = $command->getCarrierId()->getValue();

        $shipment = $this->repository->findById($shipmentId);

        if (!$shipment) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                )
            );
        }

        if (!empty($shipment->getTrackingNumber())) {
            throw new CannotEditShipmentShippedException(
                $this->translator->trans(
                    'Cannot split the shipment "%id%" because it has already been shipped.',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                )
            );
        }

        $productsToMove = array_map(function ($product) {
            return (new ShipmentProduct())
                ->setOrderDetailId($product['id_order_detail'])
                ->setQuantity($product['quantity']);
        }, $command->getOrderDetailQuantity()->getValue());

        $newShipment = $this->splitter->split(
            $shipment,
            $carrierId,
            $productsToMove
        );

        $this->repository->save($newShipment);

        if ($shipment->getProducts()->isEmpty()) {
            $this->repository->delete($shipment);
        } else {
            $this->repository->save($shipment);
        }
    }
}
