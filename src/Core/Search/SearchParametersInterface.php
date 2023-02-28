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
 * @deprecated Use FiltersBuilderInterface instead
 */
interface SearchParametersInterface
{
    public const FILTER_TYPES = [
        'limit',
        'offset',
        'orderBy',
        'sortOrder',
        'filters',
    ];

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
     * @return Filters|null A collection of filters
     */
    public function getFiltersFromRepository($employeeId, $shopId, $controller, $action, $filterClass);
}
