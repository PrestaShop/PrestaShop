<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetOrderShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipment;

/**
 * Defines contract for GetOrderShipmentsHandler.
 */
interface GetOrderShipmentsHandlerInterface
{
    /**
     * @param GetOrderShipments $query
     *
     * @return OrderShipment[]
     */
    public function handle(GetOrderShipments $query);
}
