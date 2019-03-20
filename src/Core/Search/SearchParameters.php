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

namespace PrestaShop\PrestaShop\Core\Search;

use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retrieve filters parameters if any from the User request.
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
        $filters = [];

        foreach (self::FILTER_TYPES as $type) {
            if ($request->request->has($type)) {
                $filters[$type] = $request->request->get($type);
            } elseif ($request->query->has($type)) {
                $filters[$type] = $request->query->get($type);
            }
        }

        return new $filterClass($filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltersFromRepository($employeeId, $shopId, $controller, $action, $filterClass)
    {
        return $this->getFiltersFromPersistence($employeeId, $shopId, $filterClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltersFromPersistence($employeeId, $shopId, $filtersClassName)
    {
        $adminFilter = $this->adminFilterRepository->findOneBy([
            'employee' => $employeeId,
            'shop' => $shopId,
            'uniqueKey' => $filtersClassName::getKey(),
        ]);

        $savedFilters = [];

        if (null !== $adminFilter) {
            $savedFilters = json_decode($adminFilter->getFilter(), true);
        }

        $filters = [];

        $defaultValues = $filtersClassName::getDefaults();

        foreach (self::FILTER_TYPES as $type) {
            $filters[$type] = isset($savedFilters[$type]) ? $savedFilters[$type] : $defaultValues[$type];
        }

        return new $filtersClassName($filters);
    }
}
