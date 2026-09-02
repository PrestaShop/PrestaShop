<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentsForOrderDetail;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForOrderDetail;

/**
 * Defines contract for GetShipmentForViewingHandler.
 */
interface GetShipmentsForOrderDetailHandlerInterface
{
    /**
     * @return ShipmentForOrderDetail[]
     */
    public function handle(GetShipmentsForOrderDetail $query);
}
