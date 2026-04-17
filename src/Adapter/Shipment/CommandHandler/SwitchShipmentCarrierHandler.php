<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\SwitchShipmentCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\SwitchShipmentCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\CannotSaveShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Switch shipment carrier
 */
#[AsCommandHandler]
class SwitchShipmentCarrierHandler implements SwitchShipmentCarrierHandlerInterface
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
    public function handle(SwitchShipmentCarrierCommand $command): void
    {
        $shipmentId = $command->getShipmentId()->getValue();
        $carrierId = $command->getCarrierId()->getValue();

        try {
            /** @var Shipment|null $shipment */
            $shipment = $this->shipmentRepository->findOneBy(['id' => $shipmentId]);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }

        if ($shipment === null) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                )
            );
        }

        $shipment->setCarrierId($carrierId);

        try {
            $this->shipmentRepository->save($shipment);
        } catch (Throwable $e) {
            throw new CannotSaveShipmentException(
                $this->translator->trans(
                    'Could not save shipment update with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }
    }
}
