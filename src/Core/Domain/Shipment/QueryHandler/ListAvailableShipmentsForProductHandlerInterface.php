<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\ListAvailableShipmentsForProduct;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentsForProduct;

interface ListAvailableShipmentsForProductHandlerInterface
{
    /**
     * @return ShipmentsForProduct[]
     */
    public function handle(ListAvailableShipmentsForProduct $query);
}
