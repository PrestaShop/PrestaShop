<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\Breadcrumb;

/**
 * Interface GetCmsPageCategoriesForBreadcrumbHandlerInterface defines contract for GetCmsPageCategoriesForBreadcrumbHandlerInterface.
 */
interface GetCmsPageCategoriesForBreadcrumbHandlerInterface
{
    /**
     * @param GetCmsPageCategoriesForBreadcrumb $query
     *
     * @return Breadcrumb
     */
    public function handle(GetCmsPageCategoriesForBreadcrumb $query);
}
