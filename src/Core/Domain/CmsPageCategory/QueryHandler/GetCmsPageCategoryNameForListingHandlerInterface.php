<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryNameForListing;

/**
 * Defines contract for GetCmsPageCategoryNameForListingHandler.
 */
interface GetCmsPageCategoryNameForListingHandlerInterface
{
    /**
     * @param GetCmsPageCategoryNameForListing $query
     *
     * @return string
     */
    public function handle(GetCmsPageCategoryNameForListing $query);
}
