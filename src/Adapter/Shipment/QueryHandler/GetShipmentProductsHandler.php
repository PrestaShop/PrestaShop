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

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use OrderDetail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentProducts;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetShipmentProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipmentProduct;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Throwable;

#[AsQueryHandler]
class GetShipmentProductsHandler implements GetShipmentProductsHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
    ) {
    }

    /**
     * @param GetShipmentProducts $query
     *
     * @return OrderShipmentProduct[]
     */
    public function handle(GetShipmentProducts $query)
    {
        $shipmentProducts = [];
        $shipmentId = $query->getShipmentId()->getValue();

        try {
            $result = $this->repository->findOneBy(['id' => $shipmentId]);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(sprintf('Could not find shipment with id "%s"', $shipmentId), 0, $e);
        }
        if (!empty($result)) {
            foreach ($result->getProducts() as $product) {
                $orderDetail = new OrderDetail($product->getOrderDetailId());
                $shipmentProducts[] = new OrderShipmentProduct(
                    $product->getOrderDetailId(),
                    $product->getQuantity(),
                    $orderDetail->product_name,
                );
            }
        }

        return $shipmentProducts;
    }
}
