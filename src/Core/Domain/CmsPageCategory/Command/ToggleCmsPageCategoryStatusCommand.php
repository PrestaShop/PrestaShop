<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class ToggleCmsPageCategoryStatusCommand is responsible for turning on and off cms page category status.
 */
class ToggleCmsPageCategoryStatusCommand
{
    /**
     * @var CmsPageCategoryId
     */
    private $cmsPageCategoryId;

    /**
     * @param int $cmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($cmsPageCategoryId)
    {
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getCmsPageCategoryId()
    {
        return $this->cmsPageCategoryId;
    }
}
