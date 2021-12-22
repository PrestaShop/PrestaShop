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

namespace PrestaShop\PrestaShop\Core\Search;

use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retrieve filters parameters if any from the User request.
 *
 * @deprecated Use FiltersBuilderInterface instead
 */
final class SearchParameters implements SearchParametersInterface
{
    /**
     * @var AdminFilterRepository
     */
    private $adminFilterRepository;

    public function __construct(AdminFilterRepository $adminFilterRepository)
    {
        $this->adminFilterRepository = $adminFilterRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltersFromRequest(Request $request, $filterClass)
    {
        $queryParams = $request->query->all();
        $requestParams = $request->request->all();

        $parameters = [];
        foreach (self::FILTER_TYPES as $type) {
            if (isset($queryParams[$type])) {
                $parameters[$type] = $queryParams[$type];
            } elseif (isset($requestParams[$type])) {
                $parameters[$type] = $requestParams[$type];
            }
        }

        return new $filterClass($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltersFromRepository($employeeId, $shopId, $controller, $action, $filterClass)
    {
        $adminFilter = $this->adminFilterRepository->findByEmployeeAndRouteParams(
            $employeeId,
            $shopId,
            $controller,
            $action
        );

        $parameters = null !== $adminFilter ? json_decode($adminFilter->getFilter(), true) : [];

        return new $filterClass($parameters);
    }
}
