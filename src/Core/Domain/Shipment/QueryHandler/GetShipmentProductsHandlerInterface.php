<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentProducts;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipmentProduct;

/**
 * Defines contract for GetShipmentProductsForViewingHandler.
 */
interface GetShipmentProductsHandlerInterface
{
    /**
     * @param GetShipmentProducts $query
     *
     * @return OrderShipmentProduct[]
     */
    public function handle(GetShipmentProducts $query);
}
