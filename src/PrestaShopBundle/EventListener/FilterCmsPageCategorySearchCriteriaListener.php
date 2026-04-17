<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageCategoryFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageFilters;
use PrestaShopBundle\Event\FilterSearchCriteriaEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FilterCmsPageCategorySearchCriteriaListener is responsible for updating CmsCategoryFilters filter with
 * cms page category id.
 */
class FilterCmsPageCategorySearchCriteriaListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param FilterSearchCriteriaEvent $event
     */
    public function onFilterSearchCriteria(FilterSearchCriteriaEvent $event)
    {
        $isAvailableFilter = $event->getSearchCriteria() instanceof CmsPageCategoryFilters
            || $event->getSearchCriteria() instanceof CmsPageFilters
        ;

        if (!$isAvailableFilter) {
            return;
        }

        $searchCriteriaClass = $event->getSearchCriteria()::class;

        $searchCriteria = $event->getSearchCriteria();

        $filters = $searchCriteria->getFilters();

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $cmsCategoryId = $this->requestStack->getCurrentRequest()->query->getInt('id_cms_category');

            if (!$cmsCategoryId) {
                $cmsCategoryId = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
            }

            $filters['id_cms_category_parent'] = $cmsCategoryId;
        }

        $newSearchCriteria = new $searchCriteriaClass([
            'orderBy' => $searchCriteria->getOrderBy(),
            'sortOrder' => $searchCriteria->getOrderWay(),
            'offset' => $searchCriteria->getOffset(),
            'limit' => $searchCriteria->getLimit(),
            'filters' => $filters,
        ]);

        $event->setSearchCriteria($newSearchCriteria);
    }
}
