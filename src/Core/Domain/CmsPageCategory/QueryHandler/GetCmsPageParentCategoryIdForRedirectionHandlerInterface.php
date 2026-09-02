<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageParentCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Interface GetCmsPageParentCategoryIdForRedirectionHandlerInterface defines contract
 * for GetCmsPageParentCategoryIdForRedirectionHandler.
 */
interface GetCmsPageParentCategoryIdForRedirectionHandlerInterface
{
    /**
     * @param GetCmsPageParentCategoryIdForRedirection $query
     *
     * @return CmsPageCategoryId
     */
    public function handle(GetCmsPageParentCategoryIdForRedirection $query);
}
