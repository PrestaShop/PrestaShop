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
