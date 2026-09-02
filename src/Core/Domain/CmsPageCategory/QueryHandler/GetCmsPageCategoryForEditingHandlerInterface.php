<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\EditableCmsPageCategory;

/**
 * Interface GetCmsPageCategoryForEditingHandlerInterface defines contract for GetCmsPageCategoryForEditingHandler.
 */
interface GetCmsPageCategoryForEditingHandlerInterface
{
    /**
     * @param GetCmsPageCategoryForEditing $query
     *
     * @return EditableCmsPageCategory
     */
    public function handle(GetCmsPageCategoryForEditing $query);
}
