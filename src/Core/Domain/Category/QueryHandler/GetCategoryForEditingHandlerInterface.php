<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;

/**
 * Interface GetCategoryForEditingHandlerInterface.
 */
interface GetCategoryForEditingHandlerInterface
{
    /**
     * @param GetCategoryForEditing $query
     *
     * @return EditableCategory
     */
    public function handle(GetCategoryForEditing $query);
}
