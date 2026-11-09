<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\CMS;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageParentCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\Breadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class CmsPageViewDataProvider provides cms page view data for cms listing page.
 */
final class CmsPageViewDataProvider implements CmsPageViewDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function getView($cmsCategoryParentId)
    {
        return [
            'root_category_id' => CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID,
            'parent_category_id' => $this->getParentCategoryId((int) $cmsCategoryParentId),
            'breadcrumb_tree' => $this->getBreadcrumbTree($cmsCategoryParentId),
        ];
    }

    /**
     * Gets the category one level above the one being listed, so that "Back to list" climbs the tree
     * instead of jumping to the root. Falls back to the root when there is nothing above.
     *
     * @param int $cmsCategoryId
     *
     * @return int
     */
    private function getParentCategoryId($cmsCategoryId)
    {
        if (CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID === $cmsCategoryId) {
            return CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }

        try {
            return $this->queryBus->handle(
                new GetCmsPageParentCategoryIdForRedirection($cmsCategoryId)
            )->getValue();
        } catch (CmsPageCategoryException) {
            return CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }
    }

    /**
     * Gets breadcrumb tree which contains cms page categories. If the exception is raised when it returns empty array.
     *
     * @param int $cmsCategoryParentId
     *
     * @return Breadcrumb|array
     *
     * @throws CmsPageCategoryException
     */
    private function getBreadcrumbTree($cmsCategoryParentId)
    {
        return $this->queryBus->handle(new GetCmsPageCategoriesForBreadcrumb($cmsCategoryParentId));
    }
}
