<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierRanges;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierRangesCollection;

/**
 * Describes get carrier ranges handler.
 */
interface GetCarrierRangesHandlerInterface
{
    /**
     * @param GetCarrierRanges $query
     *
     * @return CarrierRangesCollection
     */
    public function handle(GetCarrierRanges $query): CarrierRangesCollection;
}
