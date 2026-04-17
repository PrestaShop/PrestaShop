<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\SearchCartRules;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\FoundCartRule;

/**
 * Interface for handling SearchCartRules query
 */
interface SearchCartRulesHandlerInterface
{
    /**
     * @param SearchCartRules $query
     *
     * @return FoundCartRule[]
     */
    public function handle(SearchCartRules $query): array;
}
