<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;

/**
 * Disables multiple cms pages.
 */
class BulkDisableCmsPageCommand
{
    /**
     * @var CmsPageId[]
     */
    private $cmsPages;

    /**
     * @param array $cmsPageIds
     *
     * @throws CmsPageException
     */
    public function __construct(array $cmsPageIds)
    {
        $this->setCmsPages($cmsPageIds);
    }

    /**
     * @return CmsPageId[]
     */
    public function getCmsPages()
    {
        return $this->cmsPages;
    }

    /**
     * @param array $cmsPageIds
     *
     * @throws CmsPageException
     */
    private function setCmsPages(array $cmsPageIds)
    {
        foreach ($cmsPageIds as $cmsPageId) {
            $this->cmsPages[] = new CmsPageId($cmsPageId);
        }
    }
}
