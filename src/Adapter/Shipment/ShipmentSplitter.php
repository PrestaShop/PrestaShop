<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Service\ShipmentSplitterInterface;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShipmentSplitter implements ShipmentSplitterInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ShipmentProduct[] $productsToMove
     */
    public function split(
        Shipment $source,
        int $carrierId,
        array $productsToMove
    ): Shipment {
        $productsByOrderDetailId = $this->indexProducts($source);

        $newShipment = new Shipment();
        $newShipment->setCarrierId($carrierId);
        $newShipment->setOrderId($source->getOrderId());
        $newShipment->setTrackingNumber(null);
        $newShipment->setAddressId($source->getAddressId());
        $newShipment->setShippingCostTaxExcluded($source->getShippingCostTaxExcluded());
        $newShipment->setShippingCostTaxIncluded($source->getShippingCostTaxIncluded());

        foreach ($productsToMove as $productToMove) {
            $orderDetailId = $productToMove->getOrderDetailId();
            $quantity = $productToMove->getQuantity();

            if (!isset($productsByOrderDetailId[$orderDetailId])) {
                throw new ShipmentException(
                    $this->translator->trans(
                        'Cannot find product with order detail id %id%.',
                        ['%id%' => $orderDetailId],
                        'Admin.Shipment.Error'
                    )
                );
            }

            $sourceProduct = $productsByOrderDetailId[$orderDetailId];
            $remainingQty = $sourceProduct->getQuantity() - $quantity;

            if ($remainingQty <= 0) {
                $source->removeProduct($sourceProduct);
            } else {
                $sourceProduct->setQuantity($remainingQty);
            }

            $newShipment->addShipmentProduct(
                (new ShipmentProduct())
                    ->setOrderDetailId($orderDetailId)
                    ->setQuantity($quantity)
            );
        }

        return $newShipment;
    }

    private function indexProducts(Shipment $shipment): array
    {
        $indexed = [];
        foreach ($shipment->getProducts() as $product) {
            $indexed[$product->getOrderDetailId()] = $product;
        }

        return $indexed;
    }
}
