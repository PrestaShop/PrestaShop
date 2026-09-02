<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;

/**
 * Defines contract to handle @see SearchShops query
 */
interface SearchShopsHandlerInterface
{
    /**
     * @param SearchShops $query
     *
     * @return array
     */
    public function handle(SearchShops $query): array;
}
