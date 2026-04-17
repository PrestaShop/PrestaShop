<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Grid\Search\Factory\DecoratedSearchCriteriaFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\CategoryFilters;
use PrestaShopBundle\Event\FilterSearchCriteriaEvent;

/**
 * Class FilterCategorySearchCriteriaListener updates category search criteria filters with resolved category parent id.
 */
class FilterCategorySearchCriteriaListener
{
    /**
     * @var DecoratedSearchCriteriaFactory
     */
    private $categorySearchCriteriaFactory;

    /**
     * @param DecoratedSearchCriteriaFactory $categorySearchCriteriaFactory
     */
    public function __construct(DecoratedSearchCriteriaFactory $categorySearchCriteriaFactory)
    {
        $this->categorySearchCriteriaFactory = $categorySearchCriteriaFactory;
    }

    /**
     * @param FilterSearchCriteriaEvent $event
     */
    public function onFilterSearchCriteria(FilterSearchCriteriaEvent $event)
    {
        if (!$event->getSearchCriteria() instanceof CategoryFilters) {
            return;
        }

        $newSearchCriteria = $this->categorySearchCriteriaFactory->createFrom($event->getSearchCriteria());

        $newFilters = new CategoryFilters([
            'orderBy' => $newSearchCriteria->getOrderBy(),
            'sortOrder' => $newSearchCriteria->getOrderWay(),
            'offset' => $newSearchCriteria->getOffset(),
            'limit' => $newSearchCriteria->getLimit(),
            'filters' => $newSearchCriteria->getFilters(),
        ]);

        $event->setSearchCriteria($newFilters);
    }
}
