<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnProductException;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderReturnProductsFilters;
use PrestaShopBundle\Event\FilterSearchCriteriaEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FilterOrderReturnProductsSearchCriteriaListener is responsible for updating OrderReturnProducts filter with
 * order return product id.
 */
class FilterOrderReturnProductsSearchCriteriaListener
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
     *
     * @throws Exception
     */
    public function onFilterSearchCriteria(FilterSearchCriteriaEvent $event)
    {
        $isAvailableFilter = $event->getSearchCriteria() instanceof OrderReturnProductsFilters;

        if (!$isAvailableFilter) {
            return;
        }

        $searchCriteria = $event->getSearchCriteria();

        $searchCriteriaClass = get_class($searchCriteria);

        $filters = $searchCriteria->getFilters();

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $orderReturnId = $this->requestStack->getCurrentRequest()->attributes->get('orderReturnId');

            if (!$orderReturnId) {
                throw new OrderReturnProductException(
                    'orderReturnId attribute is needed for order return product list'
                );
            }

            $filters['order_return_id'] = $orderReturnId;
        } else {
            throw new OrderReturnProductException('Request is needed for order return product list');
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
