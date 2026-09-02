<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;

/**
 * Class CmsPageCategoryId is responsible for providing identificator for cms page category
 */
class CmsPageCategoryId
{
    /**
     * ID for the topmost Cms Page category
     */
    public const ROOT_CMS_PAGE_CATEGORY_ID = 1;

    /**
     * @var int
     */
    private $cmsPageCategoryId;

    /**
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($cmsPageCategoryId)
    {
        $this->assertIsIntegerGreaterThanZero($cmsPageCategoryId);
        $this->cmsPageCategoryId = $cmsPageCategoryId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * Validates that the value is integer and is greater than zero.
     *
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    private function assertIsIntegerGreaterThanZero($cmsPageCategoryId)
    {
        if (!is_int($cmsPageCategoryId) || 0 >= $cmsPageCategoryId) {
            throw new CmsPageCategoryException(sprintf('Invalid cms page category id %s', var_export($cmsPageCategoryId, true)));
        }
    }
}
