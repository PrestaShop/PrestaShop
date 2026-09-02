<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Query;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;

/**
 * Gets object which transfers cms page data for editing
 */
class GetCmsPageForEditing
{
    /**
     * @var CmsPageId
     */
    private $cmsPageId;

    /**
     * @param int $cmsPageId
     *
     * @throws CmsPageException
     */
    public function __construct($cmsPageId)
    {
        $this->cmsPageId = new CmsPageId($cmsPageId);
    }

    /**
     * @return CmsPageId
     */
    public function getCmsPageId()
    {
        return $this->cmsPageId;
    }
}
