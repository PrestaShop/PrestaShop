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

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\SwitchShipmentCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\SwitchShipmentCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\CannotSaveShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use Throwable;

/**
 * Switch shipment carrier
 */
#[AsCommandHandler]
class SwitchShipmentCarrierHandler implements SwitchShipmentCarrierHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository
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
            throw new ShipmentNotFoundException(sprintf('Could not find shipment with id "%s"', $shipmentId), 0, $e);
        }

        if ($shipment === null) {
            throw new ShipmentNotFoundException(sprintf('Could not find shipment with id "%s"', $shipmentId), 0);
        }

        $shipment->setCarrierId($carrierId);

        try {
            $this->shipmentRepository->save($shipment);
        } catch (Throwable $e) {
            throw new CannotSaveShipmentException(sprintf('Could not save shipment update with id "%s"', $shipmentId), 0, $e);
        }
    }
}
