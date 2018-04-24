<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Search;

use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retrieve filters parameters if any from the User request.
 * @param array $defaultValues if a filter is not found, set the default value
 */
final class SearchParameters
{
    private $filterTypes = array(
        'limit',
        'offset',
        'orderBy',
        'sortOrder',
        'filters'
    );

    /**
     * @var AdminFilterRepository
     */
    private $adminFiltersRepository;

    public function __construct(AdminFilterRepository $adminFilterRepository)
    {
        $this->adminFiltersRepository = $adminFilterRepository;
    }

    /**
     * Retrieve list of filters from User Request.
     *
     * @param Request $request
     * @param string $filterClass the filter class.
     *
     * @return Filters A collection of filters.
     */
    public function getFiltersFromRequest(Request $request, $filterClass)
    {
        $filters = [];
        $defaultValues = $filterClass::getDefaults();

        foreach ($this->filterTypes as $type) {
            $filters[$type] = $request->get($type, $defaultValues[$type]);
        }

        return new $filterClass($filters);
    }

    /**
     * Retrieve list of filters from User searches.
     *
     * @param int $employeeId
     * @param int $shopId
     * @param string $filterClass the filter class.
     * @param Request $request
     *
     * @return Filters A collection of filters.
     */
    public function getFiltersFromRepository($employeeId, $shopId, Request $request, $filterClass)
    {
        list($controller, $action) = explode('::', $request->get('_controller'));
        $savedFilters = $this->adminFiltersRepository->findByEmployeeAndRouteParams($employeeId, $shopId, $controller, $action);
        $filters = [];

        $defaultValues = $filterClass::getDefaults();

        foreach ($this->filterTypes as $type) {
            $filters[$type] = isset($savedFilters[$type]) ? $savedFilters[$type] :  $defaultValues[$type];
        }

        return new $filterClass($filters);
    }
}
