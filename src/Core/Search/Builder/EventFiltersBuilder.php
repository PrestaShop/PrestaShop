<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    public function buildFilters(Filters $filters = null)
    {
        $filterSearchParametersEvent = new FilterSearchCriteriaEvent($filters);
        $this->dispatcher->dispatch(FilterSearchCriteriaEvent::NAME, $filterSearchParametersEvent);

        return $filterSearchParametersEvent->getSearchCriteria();
    }
}
