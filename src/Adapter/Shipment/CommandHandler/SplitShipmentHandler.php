<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
