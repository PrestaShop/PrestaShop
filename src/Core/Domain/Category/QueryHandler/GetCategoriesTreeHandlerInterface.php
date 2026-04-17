<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoriesTree;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryForTree;

/**
 * Defines contract for handling @see GetCategoriesTree query
 */
interface GetCategoriesTreeHandlerInterface
{
    /**
     * @param GetCategoriesTree $query
     *
     * @return CategoryForTree[]
     */
    public function handle(GetCategoriesTree $query): array;
}
