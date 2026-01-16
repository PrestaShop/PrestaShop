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
