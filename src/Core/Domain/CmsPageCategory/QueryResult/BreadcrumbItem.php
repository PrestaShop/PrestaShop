<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class CmsPageCategory is responsible for providing cms page id and name combination.
 */
class BreadcrumbItem
{
    /**
     * @var CmsPageCategoryId
     */
    private $cmsPageCategoryId;

    /**
     * @var string
     */
    private $name;

    /**
     * @param int $cmsPageCategoryId
     * @param string $name
     *
     * @throws CmsPageCategoryException
     */
    public function __construct($cmsPageCategoryId, $name)
    {
        $this->cmsPageCategoryId = new CmsPageCategoryId($cmsPageCategoryId);
        $this->name = $name;
    }

    /**
     * @return CmsPageCategoryId
     */
    public function getCmsPageCategoryId()
    {
        return $this->cmsPageCategoryId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
