<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\ViewableManufacturer;

/**
 * Interface for service that handles gettting manufacturer for viewing query
 */
interface GetManufacturerForViewingHandlerInterface
{
    /**
     * @param GetManufacturerForViewing $query
     *
     * @return ViewableManufacturer
     */
    public function handle(GetManufacturerForViewing $query);
}
