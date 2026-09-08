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
     * @param int|null $cmsPageCategoryId Explicit category id. Null keeps the
     *                                    legacy behavior: the handler falls
     *                                    back to the current HTTP request's
     *                                    `id_cms_category` query parameter.
     *                                    Any other value is validated by
     *                                    CmsPageCategoryId (must be > 0).
     */
    public function __construct(?int $cmsPageCategoryId = null)
    {
        $this->cmsPageCategoryId = $cmsPageCategoryId !== null
            ? new CmsPageCategoryId($cmsPageCategoryId)
            : null;
    }

    public function getCmsPageCategoryId(): ?CmsPageCategoryId
    {
        return $this->cmsPageCategoryId;
    }
}
