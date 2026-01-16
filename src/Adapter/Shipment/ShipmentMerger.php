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
