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
 * Contract that define how we can retrieve Grid filters from an User request or Repository.
 * IMPORTANT NOTE: these methods should ONLY return the filters of their respective scope (no
 * default replacement) otherwise you can't know where the values exactly come from which make
 * it impossible to fine tune overrides (which one has the priority).
 *
 * @see SearchParametersResolver class for usage.
 */
interface SearchParametersInterface
{
    const FILTER_TYPES = array(
        'limit',
        'offset',
        'orderBy',
        'sortOrder',
        'filters',
    );

    /**
     * Retrieve list of filters from User Request (ONLY those present in
     * the request).
     *
     * @param Request $request
     * @param string $filterClass the filter class
     *
     * @return Filters A collection of filters
     */
    public function getFiltersFromRequest(Request $request, $filterClass);

    /**
     * Retrieve list of filters from User searches (ONLY those saved in repository).
     *
     * @param int $employeeId
     * @param int $shopId
     * @param string $filterClass the filter class
     * @param string $controller the controller name
     * @param string $action the action name
     *
     * @return Filters A collection of filters
     *
     * @deprecated since 1.7.6.0, to be removed in 1.8. Use $this->getFiltersFromPersistence() instead.
     */
    public function getFiltersFromRepository($employeeId, $shopId, $controller, $action, $filterClass);

    /**
     * Get filters that are stored in database.
     *
     * @param int $employeeId
     * @param int $shopId
     * @param string $filtersClassName
     *
     * @return Filters
     */
    public function getFiltersFromPersistence($employeeId, $shopId, $filtersClassName);
}
