<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 04/07/18
 * Time: 12:24.
 */

namespace PrestaShop\PrestaShop\Core\Search;

use Symfony\Component\HttpFoundation\Request;

/**
 * Contract that define how we can retrieve Grid filters
 * from an User request or Repository.
 *
 * @see SearchParametersResolver class for usage.
 */
interface SearchParametersInterface
{
    /**
     * Retrieve list of filters from User Request.
     *
     * @param Request $request
     * @param string $filterClass the filter class
     *
     * @return Filters A collection of filters
     */
    public function getFiltersFromRequest(Request $request, $filterClass);

    /**
     * Retrieve list of filters from User searches.
     *
     * @param int $employeeId
     * @param int $shopId
     * @param string $filterClass the filter class
     * @param string $controller the controller name
     * @param string $action the action name
     *
     * @return Filters A collection of filters
     */
    public function getFiltersFromRepository($employeeId, $shopId, $controller, $action, $filterClass);
}
