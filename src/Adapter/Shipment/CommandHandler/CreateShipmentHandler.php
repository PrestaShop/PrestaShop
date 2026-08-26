<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use Exception;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\Shipment\OrderShippingTotalUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotFindProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\CreateShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\CreateShipmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;

#[AsCommandHandler]
class CreateShipmentHandler implements CreateShipmentHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly OrderRepository $orderRepository,
        private readonly ShippingCostCalculatorInterface $shippingCostCalculator,
        private readonly AdapterConfiguration $configuration,
        private readonly OrderShippingTotalUpdater $orderShippingTotalUpdater,
    ) {
    }

    public function handle(CreateShipment $command): int
    {
        try {
            $order = $this->orderRepository->get($command->getOrderId());
            $carrierId = $command->getCarrierId()->getValue();
            $productId = $command->getProductId()->getValue();
            $combinationId = null !== $command->getProductCombinationId() ? $command->getProductCombinationId()->getValue() : 0;
            $customizationId = null !== $command->getProductCustomizationId() ? $command->getProductCustomizationId()->getValue() : 0;
            $addressId = (int) $order->id_address_delivery;

            $shippingCostTaxExcluded = 0.00;
            $shippingCostTaxIncluded = 0.00;

            if ($this->configuration->get('PS_ORDER_RECALCULATE_SHIPPING')) {
                $product = $this->findOrderProduct($order->getProductsDetail(), $productId, $combinationId, $customizationId);
                if ($product === null) {
                    throw new CannotFindProductInOrderException(
                        sprintf(
                            'Product with id %d, combination %d and customization %d not found in order %d',
                            $productId,
                            $combinationId,
                            $customizationId,
                            (int) $order->id
                        )
                    );
                }

                $request = new ShippingCalculationRequest(
                    products: [
                        [
                            'id_product' => $productId,
                            'id_product_attribute' => $combinationId,
                            'quantity' => $command->getQuantity(),
                            'weight' => (float) ($product['product_weight'] ?? 0),
                            'weight_attribute' => null,
                            'is_virtual' => (bool) ($product['is_virtual'] ?? false),
                            'additional_shipping_cost' => (float) ($product['additional_shipping_cost'] ?? 0),
                            'price_wt' => (float) ($product['unit_price_tax_incl'] ?? 0),
                        ],
                    ],
                    carrierId: $carrierId,
                    zoneId: null,
                    addressId: $addressId,
                    countryZoneId: 0,
                    currencyId: (int) $order->id_currency,
                    customerId: (int) $order->id_customer,
                    orderTotal: (float) $order->total_products,
                );

                $context = ShippingCostPrice::createFromRequest($request);
                $this->shippingCostCalculator->compute($context);
                if ($context->getTaxExcluded() !== null && $context->getTaxIncluded() !== null) {
                    $shippingCostTaxExcluded = (float) (string) $context->getTaxExcluded();
                    $shippingCostTaxIncluded = (float) (string) $context->getTaxIncluded();
                }
            }

            $shipment = new Shipment();
            $shipment->setOrderId((int) $order->id);
            $shipment->setCarrierId($carrierId);
            $shipment->setAddressId($addressId);
            $shipment->setTrackingNumber(null);
            $shipment->setShippingCostTaxExcluded($shippingCostTaxExcluded);
            $shipment->setShippingCostTaxIncluded($shippingCostTaxIncluded);
            $shipment->setDeliveredAt(null);
            $shipment->setShippedAt(null);
            $shipment->setCancelledAt(null);

            $shipmentId = $this->shipmentRepository->save($shipment);

            if ($this->configuration->get('PS_ORDER_RECALCULATE_SHIPPING')) {
                $this->orderShippingTotalUpdater->update($order);
            }

            return $shipmentId;
        } catch (Exception $e) {
            if ($e instanceof ShipmentException || $e instanceof OrderException) {
                throw $e;
            }
            throw new ShipmentException('Failed to create shipment', $e->getCode(), $e);
        }
    }

    /**
     * An order can hold several order details for the same product and combination, they are then only
     * distinguished by their customization, so all three identifiers are part of the criteria.
     *
     * @param array<array<string, mixed>> $products
     *
     * @return array<string, mixed>|null
     */
    private function findOrderProduct(array $products, int $productId, int $combinationId, int $customizationId): ?array
    {
        foreach ($products as $product) {
            if (
                (int) $product['product_id'] === $productId
                && (int) ($product['product_attribute_id'] ?? 0) === $combinationId
                && (int) ($product['id_customization'] ?? 0) === $customizationId
            ) {
                return $product;
            }
        }

        return null;
    }
}
