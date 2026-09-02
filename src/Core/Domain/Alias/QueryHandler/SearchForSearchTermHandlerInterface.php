<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Alias\Query\SearchForSearchTerm;

/**
 * Interface SearchAliasesForAssociationHandlerInterface defines contract for SearchAliasesForAssociationHandler
 */
interface SearchForSearchTermHandlerInterface
{
    /**
     * @param SearchForSearchTerm $query
     *
     * @return string[]
     */
    public function handle(SearchForSearchTerm $query): array;
}
