<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetAvailableCarriers;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\GetCarriersResult;

/**
 * Defines contract for GetAvailableCarriersHandler.
 */
interface GetAvailableCarriersHandlerInterface
{
    /**
     * @param GetAvailableCarriers $query
     *
     * @return GetCarriersResult
     */
    public function handle(GetAvailableCarriers $query);
}
