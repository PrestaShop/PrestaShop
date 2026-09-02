<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Order;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderDetailRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailId;

class ShipmentTotalsCalculator implements ShipmentTotalsCalculatorInterface
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderDetailRepository $orderDetailRepository,
        private LegacyContext $context,
        private Tools $tools,
    ) {
    }

    public function calculate(
        int $orderDetailId,
        int $quantity,
        bool $isTaxIncl = true,
    ): float {
        $orderDetail = $this->orderDetailRepository->get(new OrderDetailId($orderDetailId));
        $order = $this->orderRepository->get(new OrderId($orderDetail->id_order));

        $unitPrice = $isTaxIncl
            ? (float) $orderDetail->unit_price_tax_incl
            : (float) $orderDetail->unit_price_tax_excl;

        return $this->calculateTotal($order, $unitPrice, $quantity);
    }

    private function calculateTotal(Order $order, float $unitPrice, int $quantity): float
    {
        $precision = $this->context->getContext()->getComputingPrecision();

        switch ($order->round_type) {
            case Order::ROUND_TOTAL:
                return $unitPrice * $quantity;

            case Order::ROUND_LINE:
                return $this->tools->round(
                    $unitPrice * $quantity,
                    $precision,
                    $order->round_mode
                );

            case Order::ROUND_ITEM:
            default:
                return $this->tools->round(
                    $unitPrice,
                    $precision,
                    $order->round_mode
                ) * $quantity;
        }
    }
}
