<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForEditing;

/**
 * Defines contract for GetShipmentForEditingHandlerInterface.
 */
interface GetShipmentForEditingHandlerInterface
{
    /**
     * @param GetShipmentForEditing $query
     *
     * @return ShipmentForEditing
     */
    public function handle(GetShipmentForEditing $query);
}
