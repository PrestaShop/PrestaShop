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
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\ShipmentProduct;
use Throwable;

#[AsCommandHandler()]
class SplitShipmentHandler implements SplitShipmentHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
    ) {
    }

    /**
     * @param SplitShipment $command
     */
    public function handle(SplitShipment $command): void
    {
        $carrierId = $command->getCarrierId()->getValue();
        $shipmentToRemoveProductId = $command->getShipmentId()->getValue();
        $products = $command->getOrderDetailQuantity()->getValue();
        $findShipment = $this->repository->findOneBy(['id' => $shipmentToRemoveProductId]);

        if (!$findShipment) {
            throw new ShipmentNotFoundException(sprintf('Could not find shipment with id "%s"', $shipmentToRemoveProductId));
        }

        $shipmentProducts = array_map(function ($product) {
            $shipmentProduct = new ShipmentProduct();
            $shipmentProduct->setOrderDetailId($product['id_order_detail']);
            $shipmentProduct->setQuantity($product['quantity']);

            return $shipmentProduct;
        }, $products);

        try {
            $this->repository->splitShipment($carrierId, $findShipment, $shipmentProducts);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException();
        }
    }
}
