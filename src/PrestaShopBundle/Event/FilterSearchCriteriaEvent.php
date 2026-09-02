<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Event;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class FilterSearchParametersEvent allows to filter search criteria when it is resolved.
 *
 * You can use getSearchCriteria() to get current search criteria and setSearchCriteria() to update it
 */
class FilterSearchCriteriaEvent extends Event
{
    /**
     * Name of event.
     */
    public const NAME = 'prestashop.search_criteria.filter';

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function __construct(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * @return SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;
    }
}
