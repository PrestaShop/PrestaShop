<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use DateTime;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\FulfillShipmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\FulfillShipmentCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\CannotSaveShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Fulfill shipment by assigning the tracking number and marking it as packed
 */
#[AsCommandHandler]
class FulfillShipmentCommandHandler implements FulfillShipmentCommandHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ShipmentNotFoundException
     * @throws CannotSaveShipmentException
     */
    public function handle(FulfillShipmentCommand $command): void
    {
        $shipmentId = $command->getShipmentId()->getValue();
        $trackingNumber = $command->getTrackingNumber()->getValue();

        $shipment = $this->shipmentRepository->findById($shipmentId);

        if (!$shipment) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                )
            );
        }

        try {
            $shipment->setTrackingNumber($trackingNumber);
            $shipment->setPackedAt(new DateTime());

            $this->shipmentRepository->save($shipment);
        } catch (Throwable $e) {
            throw new CannotSaveShipmentException(
                $this->translator->trans(
                    'Could not fulfill shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }
    }
}
