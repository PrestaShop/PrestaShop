<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class BulkDisableCmsPageCategoryCommand is responsible for disabling cms category pages.
 */
class BulkDisableCmsPageCategoryCommand extends AbstractBulkCmsPageCategoryCommand
{
    /**
     * @var CmsPageCategoryId[]
     */
    private $cmsPageCategoryIds;

    /**
     * @param int[] $cmsPageCategoryIds
     *
     * @throws CmsPageCategoryConstraintException
     * @throws CmsPageCategoryException
     */
    public function __construct(array $cmsPageCategoryIds)
    {
        if ($this->assertIsEmptyOrContainsNonIntegerValues($cmsPageCategoryIds)) {
            throw new CmsPageCategoryConstraintException(sprintf('Missing cms page category data or array %s contains non integer values for bulk disabling', var_export($cmsPageCategoryIds, true)), CmsPageCategoryConstraintException::INVALID_BULK_DATA);
        }

        $this->setCmsPageCategoryIds($cmsPageCategoryIds);
    }

    /**
     * @return CmsPageCategoryId[]
     */
    public function getCmsPageCategoryIds()
    {
        return $this->cmsPageCategoryIds;
    }

    /**
     * @param int[] $cmsPageCategoryIds
     *
     * @throws CmsPageCategoryException
     */
    private function setCmsPageCategoryIds(array $cmsPageCategoryIds)
    {
        foreach ($cmsPageCategoryIds as $id) {
            $this->cmsPageCategoryIds[] = new CmsPageCategoryId($id);
        }
    }
}
