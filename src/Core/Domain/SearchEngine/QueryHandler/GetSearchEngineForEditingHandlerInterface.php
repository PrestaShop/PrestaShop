<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Query\GetSearchEngineForEditing;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryResult\SearchEngineForEditing;

/**
 * Defines contract for GetSearchEngineForEditingHandler.
 */
interface GetSearchEngineForEditingHandlerInterface
{
    /**
     * @param GetSearchEngineForEditing $query
     *
     * @return SearchEngineForEditing
     */
    public function handle(GetSearchEngineForEditing $query): SearchEngineForEditing;
}
