<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\ListAvailableShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentsForMerge;

interface ListAvailableShipmentsHandlerInterface
{
    /**
     * @param ListAvailableShipments $query
     *
     * @return ShipmentsForMerge[]
     */
    public function handle(ListAvailableShipments $query);
}
