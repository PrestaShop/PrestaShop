<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;

class OrderDetailQuantity
{
    /**
     * @var array<int, array{id_order_detail: int, quantity: int}>
     */
    private array $items;

    /**
     * @param array<int, array{id_order_detail: int, quantity: int}> $items
     *
     * @throws ShipmentException
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if (!isset($item['id_order_detail']) || !isset($item['quantity'])) {
                throw new ShipmentException(sprintf('Invalid order detail quantity item: %s', json_encode($item)));
            }
        }

        $this->items = $items;
    }

    /**
     * @return array<int, array{id_order_detail: int, quantity: int}>
     */
    public function getValue(): array
    {
        return $this->items;
    }
}
