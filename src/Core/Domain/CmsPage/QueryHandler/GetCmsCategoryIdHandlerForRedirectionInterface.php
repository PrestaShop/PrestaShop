<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Defines contract for GetCmsCategoryIdHandlerForRedirection.
 */
interface GetCmsCategoryIdHandlerForRedirectionInterface
{
    /**
     * @param GetCmsCategoryIdForRedirection $query
     *
     * @return CmsPageCategoryId
     */
    public function handle(GetCmsCategoryIdForRedirection $query);
}
