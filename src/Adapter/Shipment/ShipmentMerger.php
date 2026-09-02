<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Service\ShipmentMergerInterface;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShipmentMerger implements ShipmentMergerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ShipmentProduct[] $productsToMove
     */
    public function merge(
        Shipment $source,
        Shipment $target,
        array $productsToMove
    ): void {
        $sourceProducts = $this->indexProducts($source);
        $targetProducts = $this->indexProducts($target);

        foreach ($productsToMove as $productToMove) {
            $orderDetailId = $productToMove->getOrderDetailId();
            $quantity = $productToMove->getQuantity();

            if (!isset($sourceProducts[$orderDetailId])) {
                throw new ShipmentException(
                    $this->translator->trans(
                        'Order detail with id %id% does not exist in source shipment.',
                        ['%id%' => $orderDetailId],
                        'Admin.Shipment.Error'
                    )
                );
            }

            if (!isset($targetProducts[$orderDetailId])) {
                $target->addShipmentProduct(
                    (new ShipmentProduct())
                        ->setOrderDetailId($orderDetailId)
                        ->setQuantity($quantity)
                );
            } else {
                $targetProduct = $targetProducts[$orderDetailId];
                $newQty = $targetProduct->getQuantity() + $quantity;
                $targetProduct->setQuantity($newQty);
            }

            $sourceProduct = $sourceProducts[$orderDetailId];
            $remainingQty = $sourceProduct->getQuantity() - $quantity;

            if ($remainingQty <= 0) {
                $source->removeProduct($sourceProduct);
            } else {
                $sourceProduct->setQuantity($remainingQty);
            }
        }
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
