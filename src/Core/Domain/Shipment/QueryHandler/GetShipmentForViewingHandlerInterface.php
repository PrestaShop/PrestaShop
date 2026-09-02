<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForViewing;

/**
 * Defines contract for GetShipmentForViewingHandler.
 */
interface GetShipmentForViewingHandlerInterface
{
    /**
     * @param GetShipmentForViewing $query
     *
     * @return ShipmentForViewing
     */
    public function handle(GetShipmentForViewing $query);
}
