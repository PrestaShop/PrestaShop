<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Event\FilterSearchCriteriaEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This builder is used to allow modification of the built filters via
 * a symfony event prestashop.search_criteria.filter (used to change the
 * generic building process in some edge cases)
 *
 * @see FilterCategorySearchCriteriaListener
 */
final class EventFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(?Filters $filters = null)
    {
        $filterSearchParametersEvent = new FilterSearchCriteriaEvent($filters);
        $this->dispatcher->dispatch($filterSearchParametersEvent, FilterSearchCriteriaEvent::NAME);

        /** @var Filters $filters */
        $filters = $filterSearchParametersEvent->getSearchCriteria();

        return $filters;
    }
}
