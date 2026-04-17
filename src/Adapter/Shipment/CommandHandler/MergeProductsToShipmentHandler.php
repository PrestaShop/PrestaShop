<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\MergeProductsToShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\MergeProductsToShipmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\CannotEditShipmentShippedException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\CannotMergeProductToShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Service\ShipmentMergerInterface;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\ShipmentProduct;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsCommandHandler]
class MergeProductsToShipmentHandler implements MergeProductsToShipmentHandlerInterface
{
    public function __construct(
        private ShipmentRepository $repository,
        private ShipmentMergerInterface $merger,
        private TranslatorInterface $translator,
    ) {
    }

    public function handle(MergeProductsToShipment $command): void
    {
        $sourceId = $command->getSourceShipmentId()->getValue();
        $targetId = $command->getTargetShipmentId()->getValue();

        $sourceShipment = $this->repository->findById($sourceId);
        $targetShipment = $this->repository->findById($targetId);

        if (!$sourceShipment) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Cannot find product with order detail id %id%.',
                    ['%id%' => $sourceId],
                    'Admin.Shipment.Error'
                )
            );
        }

        if (!empty($sourceShipment->getTrackingNumber())) {
            throw new CannotEditShipmentShippedException(
                $this->translator->trans(
                    'Cannot merge shipment "%id%" because it has already been shipped.',
                    ['%id%' => $sourceId],
                    'Admin.Shipment.Error'
                )
            );
        }

        if (!$targetShipment) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Cannot find product with order detail id %id%.',
                    ['%id%' => $targetId],
                    'Admin.Shipment.Error'
                )
            );
        }

        if (!empty($targetShipment->getTrackingNumber())) {
            throw new CannotEditShipmentShippedException(
                $this->translator->trans(
                    'Cannot merge into shipment "%id%" because it has already been shipped.',
                    ['%id%' => $targetId],
                    'Admin.Shipment.Error'
                )
            );
        }

        $productsToMove = array_map(function ($product) {
            return (new ShipmentProduct())
                ->setOrderDetailId($product['id_order_detail'])
                ->setQuantity($product['quantity']);
        }, $command->getOrderDetailQuantity()->getValue());

        try {
            $this->merger->merge($sourceShipment, $targetShipment, $productsToMove);

            $this->repository->save($targetShipment);

            if ($sourceShipment->getProducts()->isEmpty()) {
                $this->repository->delete($sourceShipment);
            } else {
                $this->repository->save($sourceShipment);
            }
        } catch (Throwable $e) {
            throw new CannotMergeProductToShipmentException(
                $this->translator->trans(
                    'Cannot merge products to shipment with id "%id%".',
                    ['%id%' => $targetId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }
    }
}
