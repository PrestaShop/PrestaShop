<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Gets name by cms category which are used for display in cms listing.
 */
class GetCmsPageCategoryNameForListing
{
    private ?CmsPageCategoryId $cmsPageCategoryId;

    /**
     * @param int|null $cmsPageCategoryId Explicit category id. Null (or 0)
     *                                    keeps the legacy behavior: the
     *                                    handler falls back to the current
     *                                    HTTP request's `id_cms_category`
     *                                    query parameter.
     */
    public function __construct(?int $cmsPageCategoryId = null)
    {
        $this->cmsPageCategoryId = ($cmsPageCategoryId !== null && $cmsPageCategoryId > 0)
            ? new CmsPageCategoryId($cmsPageCategoryId)
            : null;
    }

    public function getCmsPageCategoryId(): ?CmsPageCategoryId
    {
        return $this->cmsPageCategoryId;
    }
}
