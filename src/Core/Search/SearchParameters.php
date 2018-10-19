<?php
/**
 * 2007-2018 PrestaShop.
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
use Symfony\Component\HttpFoundation\Request;

/**
 * Retrieve filters parameters if any from the User request.
 */
final class SearchParameters implements SearchParametersInterface
{
    const FILTER_TYPES = array(
        'limit',
        'offset',
        'orderBy',
        'sortOrder',
        'filters',
    );

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
        $filters = [];
        $defaultValues = $filterClass::getDefaults();

        foreach (self::FILTER_TYPES as $type) {
            $filters[$type] = $request->get($type, $defaultValues[$type]);
        }

        return new $filterClass($filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltersFromRepository($employeeId, $shopId, $controller, $action, $filterClass)
    {
        $adminFilter = $this->adminFilterRepository
            ->findByEmployeeAndRouteParams($employeeId, $shopId, $controller, $action)
        ;

        $savedFilters = [];

        if ($adminFilter !== null) {
            $savedFilters = json_decode($adminFilter->getFilter(), true);
        }

        $filters = [];

        $defaultValues = $filterClass::getDefaults();

        foreach (self::FILTER_TYPES as $type) {
            $filters[$type] = isset($savedFilters[$type]) ? $savedFilters[$type] : $defaultValues[$type];
        }

        return new $filterClass($filters);
    }
}
