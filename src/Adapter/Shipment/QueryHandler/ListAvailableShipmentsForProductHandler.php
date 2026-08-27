<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\ListAvailableShipmentsForProduct;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\ListAvailableShipmentsForProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentsForProduct;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class ListAvailableShipmentsForProductHandler implements ListAvailableShipmentsForProductHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
        private readonly TranslatorInterface $translator,
        private readonly ShopContext $shopContext,
        private readonly ProductRepository $productRepository,
    ) {
    }

    /**
     * @return ShipmentsForProduct[]
     */
    public function handle(ListAvailableShipmentsForProduct $query)
    {
        $orderId = $query->getOrderId()->getValue();
        $productInstance = $this->productRepository->get(new ProductId($query->getProductId()->getValue()), new ShopId($this->shopContext->getContextShopID()));
        $availableShipmentsForProductSelected = [];

        try {
            $getShipmentsFromOrder = $this->repository->findByOrderId($orderId);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(sprintf('Could not find shipment for order id "%s"', $orderId), 0, $e);
        }

        if (empty($getShipmentsFromOrder)) {
            return $availableShipmentsForProductSelected;
        }

        foreach ($getShipmentsFromOrder as $shipment) {
            // productInstance->getCarriers() return empty array if product is handle by ALL carriers
            if (count($productInstance->getCarriers()) === 0 || in_array($shipment->getCarrierId(), array_column($productInstance->getCarriers(), 'id_carrier'))) {
                $availableShipmentsForProductSelected[] = new ShipmentsForProduct($shipment->getId(), $this->translator->trans('Shipment ', [], 'Shop.Forms.Labels') . $shipment->getId());
            }
        }

        return $availableShipmentsForProductSelected;
    }
}
