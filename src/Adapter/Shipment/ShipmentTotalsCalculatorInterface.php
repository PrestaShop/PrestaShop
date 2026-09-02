<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

interface ShipmentTotalsCalculatorInterface
{
    public function calculate(int $orderDetailId, int $quantity, bool $isTaxIncl): float;
}
