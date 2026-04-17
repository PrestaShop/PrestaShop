<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\DeleteProductFromShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\DeleteProductFromShipmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsCommandHandler]
class DeleteProductFromShipmentHandler implements DeleteProductFromShipmentHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function handle(DeleteProductFromShipment $command): void
    {
        $shipmentId = $command->getShipmentId()->getValue();
        $shipment = $this->shipmentRepository->findOneBy(['id' => $shipmentId]);

        if (null === $shipment) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Cannot find product with order detail id %id%.',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                )
            );
        }

        foreach ($shipment->getProducts() as $product) {
            if ($command->getOrderDetailId()->getValue() === $product->getOrderDetailId()) {
                $shipment->removeProduct($product);
            }
        }

        try {
            $this->shipmentRepository->save($shipment);
        } catch (Throwable $e) {
            throw new ShipmentException(
                $this->translator->trans(
                    'Failed to delete products from shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }
    }
}
