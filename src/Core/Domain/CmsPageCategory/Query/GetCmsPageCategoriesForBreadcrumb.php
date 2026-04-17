<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class GetCmsPageCategoriesForBreadcrumb is responsible for providing required data for displaying cms page category
 * breadcrumbs.
 */
class GetCmsPageCategoriesForBreadcrumb
{
    /**
     * @var CmsPageCategoryId
     */
    private $currentCategoryId;

    /**
     * @param int $currentCategoryId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($currentCategoryId)
    {
        $this->currentCategoryId = new CmsPageCategoryId($currentCategoryId);
    }

    /**
     * Gets current category id.
     *
     * @return CmsPageCategoryId
     */
    public function getCurrentCategoryId()
    {
        return $this->currentCategoryId;
    }
}
