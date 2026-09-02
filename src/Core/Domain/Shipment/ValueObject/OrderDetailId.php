<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;

class OrderDetailId
{
    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @param int $orderDetailId
     *
     * @throws ShipmentException
     */
    public function __construct(int $orderDetailId)
    {
        if ($orderDetailId <= 0) {
            throw new ShipmentException('Should be a valid order detail id');
        }

        $this->orderDetailId = $orderDetailId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->orderDetailId;
    }
}
