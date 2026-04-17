<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Defines contract to handle @see GetCombinationIds query
 *
 * @see GetCombinationIds
 */
interface GetCombinationIdsHandlerInterface
{
    /**
     * @param GetCombinationIds $query
     *
     * @return CombinationId[]
     */
    public function handle(GetCombinationIds $query): array;
}
