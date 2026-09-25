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
        /*
         * WHY not the source's cost: this shipment is being created for a DIFFERENT carrier, so what the
         * previous one costs says nothing about what this one does. Copying it showed the merchant a price
         * the new carrier never quoted, and it only ever went unnoticed because SplitShipmentHandler
         * recomputes every shipment of the order straight afterwards when PS_ORDER_RECALCULATE_SHIPPING is
         * on. With that setting off nothing recomputes it, and the copied value is what the merchant sees.
         * A merchant who turns recalculation off sets shipping by hand, so an empty cost is the honest
         * starting point; an inherited one is not.
         */
        $newShipment->setShippingCostTaxExcluded(0.0);
        $newShipment->setShippingCostTaxIncluded(0.0);

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
